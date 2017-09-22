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
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/

namespace pocketmine\entity;

use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\math\Vector3;

class ZombieHorse extends Horse{
	const NETWORK_ID = 27;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	public $maxhealth = 52;
	public $maxjump = 3;

	public function getName() : string{
		return "Zombie Horse";
	}
	
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->position = $this->asVector3();

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
			Entity::DATA_COLOR => [Horse::TYPE_ZOMBIE, 1],
		];

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
		$pk->entityRuntimeId = $this->getId();
		$player->dataPacket($pk);

	}

	public function getRidePosition(){
		return [-0.02, 2.3, 0.19];
	}
}
