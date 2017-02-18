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
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class Wolf extends Animal implements Tameable{
	const NETWORK_ID = 14;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	public $maxhealth = 10;

	public $collarcolor = 0;
	public $isDyed = false;

	const COLLAR_COLOR_WHITE = 0;
	const COLLAR_COLOR_ORANGE = 1;
	const COLLAR_COLOR_MAZENTA = 2;
	const COLLAR_COLOR_LIGHT_BLUE = 3;
	const COLLAR_COLOR_YELLOW = 4;
	const COLLAR_COLOR_LIME = 5;
	const COLLAR_COLOR_PINK = 6;
	const COLLAR_COLOR_GRAY = 7;
	const COLLAR_COLOR_LIGHT_GRAY = 8;
	const COLLAR_COLOR_CYAN = 9;
	const COLLAR_COLOR_PURPLE = 10;
	const COLLAR_COLOR_BLUE = 11;
	const COLLAR_COLOR_BROWN = 12;
	const COLLAR_COLOR_GREEN = 13;
	const COLLAR_COLOR_RED = 14;
	const COLLAR_COLOR_BLACK = 15;

	public function initEntity(){
		$this->setMaxHealth(8);

		parent::initEntity();

		if(isset($this->namedtag->CollarColor)){
			$this->collarcolor = $this->namedtag["CollarColor"];
			$this->isDyed = true;
			$this->setCollarColor($this->collarcolor);
		}else{
			$this->collarcolor = 0;
			$this->isDyed = false;
		}
	}

	public function getName(){
		return "Wolf";
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Wolf::NETWORK_ID;
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

	public function onRightClick(Player $player){
		//TODO Tamed
		$item = $player->getInventory()->getItemInHand();
		if($item->getId() == ItemItem::DYE){
			$this->setCollarColor($this->getCollarColorByDye($item->getDamage()));

			if($player->isSurvival()){
				$count = $item->getCount();
				if($count-- <= 0){
					$player->getInventory()->setItemInHand(Item::get(Item::AIR));
					return;
				}
				$item->setCount($count);
				$player->getInventory()->setItemInHand($item);
			}
		}
		parent::onRightClick($player);
	}

	public function getCollarColorByDye($damage){
		switch($damage){
			case 0:
				return self::COLLAR_COLOR_BLACK;
			case 1:
				return self::COLLAR_COLOR_RED;
			case 2:
				return self::COLLAR_COLOR_GREEN;
			case 3:
				return self::COLLAR_COLOR_BROWN;
			case 4:
				return self::COLLAR_COLOR_BLUE;
			case 5:
				return self::COLLAR_COLOR_PURPLE;
			case 6:
				return self::COLLAR_COLOR_CYAN;
			case 7:
				return self::COLLAR_COLOR_LIGHT_GRAY;
			case 8:
				return self::COLLAR_COLOR_GRAY;
			case 9:
				return self::COLLAR_COLOR_PINK;
			case 10:
				return self::COLLAR_COLOR_LIME;
			case 11:
				return self::COLLAR_COLOR_YELLOW;
			case 12:
				return self::COLLAR_COLOR_LIGHT_BLUE;
			case 13:
				return self::COLLAR_COLOR_MAZENTA;
			case 14:
				return self::COLLAR_COLOR_ORANGE;
			case 15:
				return self::COLLAR_COLOR_WHITE;
		}
	}

	public function setCollarColor($color){
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_TAMED, true);
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INLOVE, true);
		$this->setDataProperty(Entity::DATA_COLOR, Entity::DATA_TYPE_BYTE, $color, true);
		$this->setDataProperty(Entity::DATA_OWNER_EID, Entity::DATA_TYPE_LONG, 0, true);
	}
}