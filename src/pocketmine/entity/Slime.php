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

use pocketmine\entity\AI\EntitySlimeMoveHelper;
use pocketmine\entity\AI\EntityAISlimeFloat;
use pocketmine\entity\AI\EntityAISlimeAttack;
use pocketmine\entity\AI\EntityAISlimeFaceRandom;
use pocketmine\entity\AI\EntityAISlimeHop;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class Slime extends Living{
	const NETWORK_ID = 37;
	const DATA_SIZE = 16;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 5;
	public $maxhealth = 16;

	public $squishAmount;
	public $squishFactor;
	public $prevSquishFactor;
	private $wasOnGround;

	public function initEntity(){
		$this->moveHelper = new EntitySlimeMoveHelper($this);
		$this->tasks->addTask(1, new EntityAISlimeFloat($this));
		$this->tasks->addTask(2, new EntityAISlimeAttack($this));
		$this->tasks->addTask(3, new EntityAISlimeFaceRandom($this));
		$this->tasks->addTask(5, new EntityAISlimeHop($this));
		//$this->targetTasks->addTask(1, new EntityAIFindEntityNearestPlayer($this));
		//$this->targetTasks->addTask(3, new EntityAIFindEntityNearest($this, "pocketmine\entity\IronGolem"));
		$size = $this->getRandomSlimeSize();
		$this->setDataProperty(self::DATA_SIZE, self::DATA_TYPE_INT, $size);
		$this->setMaxHealth($size * $size);
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

	public function onUpdate($currentTick) {
		parent::onUpdate($currentTick);
		return true;
	}

	public function getJumpDelay() : int{
		return rand(0, 19) + 10;
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

	/*public function close(){
		parent::close();
		$i = $this->getSlimeSize();
		if($i == 1) return;
		$j = 2 + rand(0, 2);

		for ($k = 0; $k < $j; ++$k){
			$f = (($k % 2) - 0.5) * $i / 4.0;
			$f1 = (($k / 2) - 0.5) * $i / 4.0;

			$nbt = new CompoundTag("", [
				"Pos" => new ListTag("Pos", [
					new DoubleTag("", $this->getX() + $f),
					new DoubleTag("", $this->getY() + 0.5),
					new DoubleTag("", $this->getZ() + $f1)
				]),
				"Motion" => new ListTag("Motion", [
					new DoubleTag("", 0),
					new DoubleTag("", 0),
					new DoubleTag("", 0)
				]),
				"Rotation" => new ListTag("Rotation", [
					new FloatTag("", lcg_value() * 360),
					new FloatTag("", 0)
				]),
			]);
			if($this->getNameTag() !== ""){
				$nbt->CustomName = new StringTag("CustomName", $this->getNameTag());
			}

			$entityslime = Entity::createEntity("Slime", $this->level, $nbt);

			$entityslime->setSlimeSize(floor($i / 2));
			$entityslime->spawnToAll();
		}
	}*/

	public function getDrops(){
		$drops = [];
		if($this->getSlimeSize() == 1){
			$drops[] = ItemItem::get(ItemItem::SLIME_BALL, 0, 1);
		}
		return $drops;
	}

}