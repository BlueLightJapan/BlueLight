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

use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class Guardian extends Monster{
	const NETWORK_ID = 49;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;
	public $maxhealth = 30;

	public function getName(){
		return "Guardian";
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Guardian::NETWORK_ID;
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

	public function isElder() : bool{
		return $this->getDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_ELDER);
	}

	public function setElder(bool $elder){
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_ELDER, $elder);

		if ($elder){
			$this->width = 1.9975;
			$this->length = 1.9975;
			//$this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue(0.30000001192092896);
			$this->setMaxHelth(80);//AttackDamage => 8.0
			//$this->wander->setExecutionChance(400);
	        }
	}

	public function getDrops(){
		$drops = [];
		$ev = $this->getLastDamageCause();
		$looting = $ev instanceof EntityDamageByEntityEvent ? $ev->getDamager() instanceof Player ? $ev->getDamager()->getInventory()->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING) : 0 : 0;

		$prismarine = rand(0, 2) + rand(0, $looting);

		$drops[] = ItemItem::get(ItemItem::PRISMARINE_SHARD, 0, $prismarine);

		if (rand(2 + $looting) > 1){
			$drops[] = ItemItem::get(ItemItem::RAW_FISH, 0, 1);
		}else if (rand(2 + $looting) > 1){
			$drops[] = ItemItem::get(ItemItem::PRISMARINE_CRYSTALS, 0, 1);
		}

		if($ev instanceof EntityDamageByEntityEvent && $ev->getDamager() instanceof Player && $this->isElder()){
			$drops[] = ItemItem::get(Block::SPONGE, 1, 1);
		}
		return $drops;
	}
}