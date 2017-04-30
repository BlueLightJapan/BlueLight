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

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class Slime extends Living{
	const NETWORK_ID = 37;
	const DATA_SIZE = 16;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 5;
	public $maxhealth = 16;

	public function initEntity(){
		$this->setDataProperty(self::DATA_SIZE, self::DATA_TYPE_INT, $this->getRandomSlimeSize());
		parent::initEntity();
	}

	public function getName(){
		return "Slime";
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Slime::NETWORK_ID;
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

	protected function getRandomSlimeSize() : int{
		$i = rand(0, 2);

		if ($i < 2 && (rand(0, 10) / 10) < 0.5 * 1){
			$i++;
		}

		$j = 1 << $i;
		return $j;
	}

	public function getSlimeSize() : int{
		return $this->getDataProperty(self::DATA_SIZE);
	}

	public function setSlimeSize(int $size){
		$this->setDataProperty(self::DATA_SIZE, self::DATA_TYPE_INT, $size);
	}

	public function getDrops(){
		$drops = [];
		if($this->getSlimeSize() == 1){
			$drops[] = ItemItem::get(ItemItem::SLIME_BALL, 0, 1);
		}
		return $drops;
	}

}