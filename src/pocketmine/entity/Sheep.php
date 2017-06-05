<?php

/*
 *   ____  _            _      _       _     _
 *  |  _ \| |          | |    (_)     | |   | |
 *  | |_) | |_   _  ___| |     _  __ _| |__ | |_
 *  |  _ <| | | | |/ _ \ |    | |/ _` | '_ \| __|
 *  | |_) | | |_| |  __/ |____| | (_| | | | | |_
 *  |____/|_|\__,_|\___|______|_|\__, |_| |_|\__|
 *                                __/ |
 *                               |___/
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/

namespace pocketmine\entity;

use pocketmine\entity\Attribute;
use pocketmine\entity\AI\EntityAIEatGrass;
use pocketmine\entity\AI\EntityAISwimming;
use pocketmine\entity\AI\EntityAIWatchClosest;
use pocketmine\entity\AI\EntityAILookIdle;
use pocketmine\entity\AI\EntityAIWander;
use pocketmine\entity\AI\EntityAIPanic;
use pocketmine\entity\AI\EntityAIMate;
use pocketmine\entity\AI\EntityAITempt;
use pocketmine\entity\AI\EntityAIFollowParent;
use pocketmine\block\Wool;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\enchantment\Enchantment;

class Sheep extends Animal{
	const NETWORK_ID = 13;

	const DATA_COLOR_INFO = 16;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.9;
	public $maxhealth = 8;

	private $sheepTimer;
	private $entityAIEatGrass;

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

	public function initEntity(){
		$this->entityAIEatGrass = new EntityAIEatGrass($this);
		$this->tasks->addTask(0, new EntityAISwimming($this));
		$this->tasks->addTask(1, new EntityAIPanic($this, 1.25));
		$this->tasks->addTask(2, new EntityAIMate($this, 1.0));
		$this->tasks->addTask(3, new EntityAITempt($this, 1.1, ItemItem::WHEAT, false));
		//$this->tasks->addTask(4, new EntityAIFollowParent($this, 1.1));
		$this->tasks->addTask(5, $this->entityAIEatGrass);
		$this->tasks->addTask(6, new EntityAIWander($this, 1.0));
		$this->tasks->addTask(7, new EntityAIWatchClosest($this, "pocketmine\Player", 6.0));
		$this->tasks->addTask(8, new EntityAILookIdle($this));
		$this->setMaxHealth(20);
		parent::initEntity();
	}

	protected function addAttributes(){
		parent::addAttributes();
		$this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue(0.23000000417232513);
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

	public function doEatGrass(){
		$pk = new EntityEventPacket();
		$pk->eid = $this->getId();
		$pk->event = EntityEventPacket::EAT_GRASS_ANIMATION;
		Server::getInstance()->broadcastPacket($this->getViewers(), $pk);
	}

	public function eatGrassBonus(){
		$this->setSheared(false);
		//if ($this->isBaby()){
		//	$this->addGrowth(60);
		//}
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

	public function onUpdate($currentTick) {
		parent::onUpdate($currentTick);
		return true;
	}

	public function getDrops(){
		$ev = $this->getLastDamageCause();
		$looting = $ev instanceof EntityDamageByEntityEvent ? $ev->getDamager() instanceof Player ? $ev->getDamager()->getInventory()->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING) : 0 : 0;

		$wools = rand(0, 1) + 1 + rand(0, $looting);

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

	public function createChild($ageable){
	}
}