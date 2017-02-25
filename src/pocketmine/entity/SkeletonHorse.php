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

use pocketmine\network\protocol\UpdateAttributesPacket;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\math\Vector3;

class SkeletonHorse extends Animal implements Rideable{
	const NETWORK_ID = 26;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	public $maxhealth = 52;
	public $maxjump = 3;

	public function getName(){
		return "Skeleton Horse";
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
		$this->sendAttribute($player);
		parent::spawnTo($player);
	}
	public function sendAttribute(Player $player){
		$entry = array();
		$entry[] = new Attribute($this->getId(), "minecraft:horse.jump_strength", 0, $this->maxjump, 0.6679779);
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
		if($block->isTransparent()) return true;
		else return false;
	}

	public function jump($power){
		$this->move(0, $this->maxjump * ($power * 0.0001), 0);
		$this->updateMovement();
	}

	public function getRidePosition(){
		return [-0.02, 2.3, 0.19];
	}
}
