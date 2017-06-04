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

use pocketmine\entity\Attribute;
use pocketmine\entity\AI\EntityAISwimming;
use pocketmine\entity\AI\EntityAIWatchClosest;
use pocketmine\entity\AI\EntityAILookIdle;
use pocketmine\entity\AI\EntityAIWander;
use pocketmine\entity\AI\EntityAIPanic;
use pocketmine\entity\AI\EntityAIMate;
use pocketmine\entity\AI\EntityAITempt;
use pocketmine\entity\AI\EntityAIFollowParent;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class Chicken extends Animal{
	const NETWORK_ID = 10;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;
	public $maxhealth = 4;

	public $wingRotation = 0;
	public $destPos = 0;
	public $field_70884_g = 0;
	public $field_70888_h = 0;
	public $wingRotDelta = 1.0;
	public $timeUntilNextEgg;
	public $chickenJockey = false;

	public function getName(){
		return "Chicken";
	}

	public function initEntity(){
		$this->timeUntilNextEgg = rand(0, 5999) + 6000;
		$this->tasks->addTask(0, new EntityAISwimming($this));
		$this->tasks->addTask(1, new EntityAIPanic($this, 1.4));
		$this->tasks->addTask(2, new EntityAIMate($this, 1.0));
		$this->tasks->addTask(3, new EntityAITempt($this, 1.0, ItemItem::WHEAT_SEEDS, false));
		//$this->tasks->addTask(4, new EntityAIFollowParent($this, 1.1));
		$this->tasks->addTask(5, new EntityAIWander($this, 1.0));
		$this->tasks->addTask(6, new EntityAIWatchClosest($this, "pocketmine\Player", 6.0));
		$this->tasks->addTask(7, new EntityAILookIdle($this));
		$this->setMaxHealth(4);
		parent::initEntity();
	}

	protected function addAttributes(){
		parent::addAttributes();
		$this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue(0.25);
	}

	public function onUpdate($currentTick) {
		parent::onUpdate($currentTick);
		$this->field_70888_h = $this->wingRotation;
		$this->field_70884_g = $this->destPos;
		$this->destPos = ($this->destPos + ($this->onGround ? -1 : 4) * 0.3);
		$this->destPos = $this->clamp($this->destPos, 0.0, 1.0);

		if (!$this->onGround && $this->wingRotDelta < 1.0){
			$this->wingRotDelta = 1.0;
		}

		$this->wingRotDelta = $this->wingRotDelta * 0.9;

		if (!$this->onGround && $this->motionY < 0.0){
			$this->motionY *= 0.6;
		}

		$this->wingRotation += $this->wingRotDelta * 2.0;

		if (/*!$this->isBaby() && */!$this->isChickenJockey() && --$this->timeUntilNextEgg <= 0){
			//"mob.chicken.plop", 1.0, ((rand(0, 10) / 10) - (rand(0, 10) / 10)) * 0.2 + 1.0
			$this->level->dropItem($this, ItemItem::get(ItemItem::EGG));
			$this->timeUntilNextEgg = rand(0, 5999) + 6000;
		}
		return true;
	}

	public function fall($fallDistance){
	}

	public function isChickenJockey() : bool{
		return $this->chickenJockey;
	}

	public function setChickenJockey(bool $jockey){
		$this->chickenJockey = $jockey;
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Chicken::NETWORK_ID;
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

	public function createChild($ageable){
	}
}