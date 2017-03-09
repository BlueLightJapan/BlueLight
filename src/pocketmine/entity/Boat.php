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

use pocketmine\nbt\tag\ByteTag;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class Boat extends Vehicle{
	const NETWORK_ID = 90;

	const DATA_WOOD_ID = 20;

	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->WoodID)){
			$nbt->WoodID = new ByteTag("WoodID", 0);
		}
		parent::__construct($level, $nbt);
		$this->setDataProperty(Entity::DATA_VARIANT, self::DATA_TYPE_INT, $this->getWoodID());
	}

	public function getWoodID(){
		return $this->namedtag["WoodID"];
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = 0;
		$pk->speedY = 0;
		$pk->speedZ = 0;
		$pk->yaw = 0;
		$pk->pitch = 0;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	public function attack($damage, EntityDamageEvent $source){
		parent::attack($damage, $source);

		if(!$source->isCancelled()){
			$pk = new EntityEventPacket();
			$pk->eid = $this->id;
			$pk->event = EntityEventPacket::HURT_ANIMATION;
			foreach($this->getLevel()->getPlayers() as $player){
				$player->dataPacket($pk);
			}
		}
	}

	public function goStraight($x,$y,$z){

		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}

	public function getDrops(){
		return [
			ItemItem::get(ItemItem::BOAT,$this->getWoodID() , 1)
		];
	}

	public function getSaveId(){
		$class = new \ReflectionClass(static::class);
		return $class->getShortName();
	}

	public function getRidePosition(){
		return [0, 1, 0];
	}
}
