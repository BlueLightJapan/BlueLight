<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\entity;

use pocketmine\block\Wool;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\enchantment\Enchantment;

class Sheep extends Animal{
	const NETWORK_ID = 13;

	const DATA_COLOR_INFO = 16;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.9;
	public $maxhealth = 8;

	public function getName(){
		return "Sheep";
	}

	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->Color)){
			$nbt->Color = new ByteTag("Color", self::getRandomColor());
		}
		parent::__construct($level, $nbt);

		$this->setDataProperty(self::DATA_COLOR_INFO, self::DATA_TYPE_BYTE, $this->getColor());
	}

	public static function getRandomColor() : int{
		$rand = rand(0, 99);
		return $rand < 5 ? Wool::BLACK : ($rand < 10 ? Wool::GRAY : ($rand < 15 ? Wool::LIGHT_GRAY : ($rand < 18 ? Wool::BROWN : (rand(0, 499) == 0 ? Wool::PINK : Wool::WHITE))));
	}

	public function getColor() : int{
		return (int) $this->namedtag["Color"];
	}

	public function setColor(int $color){
		$this->namedtag->Color = new ByteTag("Color", $color);
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	public function getSheared() : bool{
		return $this->getDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SHEARED);
	}

	public function setSheared(bool $sheared){
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SHEARED, $sheared);
	}

	public function onRightClick(Player $player){
		$item = $player->getInventory()->getItemInHand();
		if($item->getId() == ItemItem::SHEARS && !$this->getSheared()){
			$this->setSheared(true);
			$wools = rand(1, 3);

			for ($j = 0; $j < $wools; ++$j){
				$motion = new Vector3();
				$motion->y += (rand(0, 10) / 10) * 0.05;
				$motion->x += ((rand(0, 10) / 10) - (rand(0, 10) / 10)) * 0.1;
				$motion->z += ((rand(0, 10) / 10) - (rand(0, 10) / 10)) * 0.1;
				$this->level->dropItem($this, ItemItem::get(ItemItem::WOOL, $this->getColor()), $motion);
			}

			if($player->isSurvival()){
				$item->useOn(null);
				if ($item->getDamage() >= $item->getMaxDurability()) {
					$player->getInventory()->setItemInHand(ItemItem::get(ItemItem::AIR));
				} else {
					$player->getInventory()->setItemInHand($item);
				}
			}

			//mob.sheep.shear
		}
		parent::onRightClick($player);
	}

	public function getDrops(){
		$ev = $this->getLastDamageCause();
		$looting = $ev instanceof EntityDamageByEntityEvent ? $ev->getDamager() instanceof Player ? $ev->getDamager()->getInventory()->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING) : 0 : 0;

		$wools = this.rand.nextInt(2) + 1 + this.rand.nextInt(1 + looting);

		if ($this->isOnFire()){
			$mutton = ItemItem::get(ItemItem::COOKED_MUTTON, 0, 1);
		}else{
			$mutton = ItemItem::get(ItemItem::RAW_MUTTON, 0, 1);
		}
		if(!$this->getSheared()){
			return [$mutton, ItemItem::get(ItemItem::WOOL, $this->getColor(), $wools)];
		}
		return [$mutton];
	}
}