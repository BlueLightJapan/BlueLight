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
use pocketmine\Player;
use pocketmine\entity\AI\EntityAISwimming;
use pocketmine\entity\AI\EntityAIWatchClosest;
use pocketmine\entity\AI\EntityAILookIdle;
use pocketmine\entity\AI\EntityAIWander;
use pocketmine\entity\AI\EntityAIPanic;

class Cow extends Animal{
	const NETWORK_ID = 11;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	public $maxhealth = 10;

	public function getName(){
		return "Cow";
	}

	public function initEntity(){
		$this->getNavigator()->setAvoidsWater(true);
		$this->tasks->addTask(0, new EntityAISwimming($this));
		$this->tasks->addTask(1, new EntityAIPanic($this, 2.0));
		//$this->tasks->addTask(2, new EntityAIMate($this, 1.0));
		//$this->tasks->addTask(3, new EntityAITempt($this, 1.25, Item::WHEAT, false));
		//$this->tasks->addTask(4, new EntityAIFollowParent($this, 1.25));
		$this->tasks->addTask(5, new EntityAIWander($this, 1.0));
		$this->tasks->addTask(6, new EntityAIWatchClosest($this, "pocketmin\Player", 6.0));
		$this->tasks->addTask(7, new EntityAILookIdle($this));
		//$this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue(0.20000000298023224);
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Cow::NETWORK_ID;
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

	public function onUpdate($currentTick) {
		if($this->closed){
			return false;
		}


		$tickDiff = $currentTick - $this->lastUpdate;
		if($tickDiff <= 0 and !$this->justCreated){
			return true;
		}
		$this->lastUpdate = $currentTick;

		$hasUpdate = $this->entityBaseTick($tickDiff);
		$this->updateMovement();
		return true;
	}
}