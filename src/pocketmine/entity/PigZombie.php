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

use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\item\Item as ItemItem;
use pocketmine\item\enchantment\Enchantment;

class PigZombie extends Monster{
	const NETWORK_ID = 36;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;
	public $maxhealth = 20;

	public $drag = 0.2;
	public $gravity = 0.3;

	private $angerLevel;
	private $angerTarget;

	public function getName() : string{
		return "PigZombie";
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->type = PigZombie::NETWORK_ID;
	
		$pk->position = $this->asVector3();

		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);

		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->item = new ItemItem(283);
		$pk->inventorySlot = $pk->hotbarSlot = 0;
		$pk->windowId = 0;
		$player->dataPacket($pk);
	}

	private function becomeAngryAt($target){
		$this->angerLevel = 400 + rand(0, 399);

		if ($target instanceof Living){
			$this->setRevengeTarget($target);
		}
	}

	public function isAngry() : bool{
		return $this->angerLevel > 0;
	}

	public function getDrops() : array{
		$cause = $this->lastDamageCause;
		$drops = [];
		if($cause instanceof EntityDamageByEntityEvent and $cause->getDamager() instanceof Player){
			if(($lootingobj = $ev->getDamager()->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING)) != null){
				$lootingL = $lootingobj->getLevel();
			}
			if(mt_rand(1, 200) <= (5 + 2 * $lootingL)){
				$drops[] = ItemItem::get(ItemItem::GOLD_INGOT, 0, 1);
			}
			$drops[] = ItemItem::get(ItemItem::GOLD_NUGGET, 0, mt_rand(0, 1 + $lootingL));
			$drops[] = ItemItem::get(ItemItem::ROTTEN_FLESH, 0, mt_rand(0, 1 + $lootingL));
		}
		return $drops;
	}
}