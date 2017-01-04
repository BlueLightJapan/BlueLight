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
 * @link http://bluelight.cf
 * 
*/

namespace pocketmine\entity;

use pocketmine\Player;
use pocketmine\network\protocol\UpdateAttributesPacket;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\MobArmorEquipmentPacket;
use pocketmine\item\Item;
use pocketmine\math\Vector3;

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
	const TYPE_DARKBROWN = 8;
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

		@$flags |= 1 << Entity::DATA_FLAG_SADDLED;
		@$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
		@$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;

		$pk->metadata = [

		Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
		Entity::DATA_AIR => [Entity::DATA_TYPE_SHORT, 400],
		Entity::DATA_MAX_AIR => [Entity::DATA_TYPE_SHORT, 400],
		Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, ""],
		Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
		Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 1],

		];

		$player->dataPacket($pk);

		$this->setAttribute($player);

		parent::spawnTo($player);

		$this->setChestPlate(419);

	}

	public function getSaveId(){
		$class = new \ReflectionClass(static::class);
		return $class->getShortName();
	
}
	public function getDrops(){
		return [Item::get(Item::LEATHER, 0, mt_rand(0, 2))];
	}

	public function setChestPlate($id = 419){
		$pk = new MobArmorEquipmentPacket();
		$pk->eid = $this->getId();
		$pk->slots = [

		Item::get(0,0),
		Item::get($id,0),
		Item::get(0,0),
		Item::get(0,0)

		];
		foreach($this->level->getPlayers() as $player){
			$player->dataPacket($pk);
		}
	}

	public function setAttribute(Player $player){
		$entry = array();
		$entry[] = new Attribute($this->getId(), "minecraft:horse.jump_strength", 0, 2, 0.6679779);
		$entry[] = new Attribute($this->getId(), "minecraft:fall_damage", 0, 3.402823, 1);
		$entry[] = new Attribute($this->getId(), "minecraft:luck", -1024, 1024, 0);
		$entry[] = new Attribute($this->getId(), "minecraft:movement", 0, 3.402823, 0.223);
		$entry[] = new Attribute($this->getId(), "minecraft:absorption", 0, 3.402823, 0);
		$entry[] = new Attribute($this->getId(), "minecraft:health", 0, 40, 40);

		$pk = new UpdateAttributesPacket();
		$pk->entries = $entry;
		$pk->entityId = $this->getId();
		$player->dataPacket($pk);

	}


	public function goBack(Player $player){
		$xz = $this->getXZ($this->yaw,$this->pitch);

		$movex = $xz[0];
		$movez = $xz[1];
		$newx = ($this->x - $movex/2);
		$newy = $this->y;
		$newz = ($this->z - $movez/2);

		if($this->isGoing(new Vector3($newx,$newy,$newz))){

			$this->x -= $movex/2;
			$this->z -= $movez/2;
		}
	}
	public function goStraight(Player $player){

		$xz = $this->getXZ($this->yaw,$this->pitch);

		$movex = $xz[0];
		$movez = $xz[1];
		$newx = $this->x + $movex;
		$newy = $this->y;
		$newz = $this->z + $movez;
		if($this->isGoing(new Vector3($newx,$newy,$newz))){
			$this->x += $movex;
			$this->z += $movez;
		}
	}

	public function getXZ($yaw,$pitch){

		$x = (-sin($yaw/180*M_PI))/2;
		$z = (cos($yaw/180*M_PI))/2;

		return array($x, $z);
	}

	public function isGoing($vector3){
		$level = $this->getLevel();
		$block = $level->getBlock($vector3);
		$id = $block->getID();
		$damage = $block->getDamage();
		switch($id){
			case 0:case 6:case 27:case 31:case 50:
			case 63:case 66:case 68:case 78:case 141:
			case 142:case 171:case 175:case 244:
			case 323:case 70:case 72:case 147:case 148:case 44:case 64:
				return true;
			break;
			default:
				return false;
			break;
		}
	}
}//written by Kametan