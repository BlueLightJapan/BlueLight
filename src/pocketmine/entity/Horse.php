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

use pocketmine\scheduler\CallBackTask;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\protocol\EntityEventPacket;

class Horse extends Living{

	const NETWORK_ID = 23;

	const DATA_HORSE_TYPE = 19;

	const TYPE_NORMAL = -1;
	const TYPE_WHITE = 0;
	const TYPE_BROWN = 2;
	const TYPE_ZOMBIE = 3;
	const TYPE_SKELETON = 4;
	const TYPE_GOLD = 6;
	const TYPE_LIGHTBROWN = 7;
	const TYPE_DARKBROWN = 8;//
	const TYPE_GRAY = 9;
	const TYPE_SILVER = 10;
	const TYPE_BLACK = 12;
	const TYPE_BLACKANDWHITE = 14;
	const TYPE_WHITEANDBLACK = 15;

	const TYPE_WEAR_LEATHER = 18;
	const TYPE_WEAR_IRON = 19;
	const TYPE_WEAR_GOLD = 20;
	const TYPE_WEAR_DIAMOND = 21;

	public function getName(){
		return "Horse";
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

@$flags |= 1 << 2;

@$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
@$flags |= 1 << Entity::DATA_FLAG_SADDLED;

$pk->metadata = [

//Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
Entity::DATA_LEAD_HOLDER => [Entity::DATA_TYPE_LONG,-1],



		Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, 0],
		Entity::DATA_AIR => [Entity::DATA_TYPE_SHORT, 400],
		Entity::DATA_MAX_AIR => [Entity::DATA_TYPE_SHORT, 400],
		Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, ""],
		Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 1]


		];
		$player->dataPacket($pk);
		parent::spawnTo($player);
	}

	public function getSaveId(){
		$class = new \ReflectionClass(static::class);
		return $class->getShortName();
	
}
	public function getDrops(){
		return [Item::get(Item::LEATHER, 0, mt_rand(0, 2))];
	}
}
