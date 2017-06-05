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
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;

class ElderGuardian extends Monster{
	const NETWORK_ID = 50;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	public $maxhealth = 80;

	public function getName(){
		return "ElderGuardian";
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
		$pk->metadata = [
		Entity::DATA_FLAGS => [28, 1],//0
//		Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, ""],//4
//		Entity::DATA_AIR => [Entity::DATA_TYPE_SHORT, 400],//7
		Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],//38
//		Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 1],//39
//		Entitt::DATA_MAX_AIR => [Entity::DATA_TYPE_SHORT, 400],//44
		49 => [7,-1], 50 => [7,-1], 51 => [7,-1],

		45 => [2,0], 46 => [0,0], 47 => [2,0],
		53 => [3,1.99], 54 => [3,1.99],
		56 => [8,[0,0,0]],
		57 => [0,0], 58 => [3,0], 59 => [3,0]
		];
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}
}