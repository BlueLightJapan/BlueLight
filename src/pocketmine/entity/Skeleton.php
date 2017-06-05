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

use pocketmine\entity\Attribute;
use pocketmine\entity\AI\EntityAIArrowAttack;
use pocketmine\entity\AI\EntityAIAttackOnCollide;
use pocketmine\entity\AI\EntityAISwimming;
use pocketmine\entity\AI\EntityAIMoveTowardsRestriction;
use pocketmine\entity\AI\EntityAIWatchClosest;
use pocketmine\entity\AI\EntityAIHurtByTarget;
use pocketmine\entity\AI\EntityAILookIdle;
use pocketmine\entity\AI\EntityAIWander;
use pocketmine\entity\AI\EntityAINearestAttackableTarget;
use pocketmine\entity\AI\EntityAIRestrictSun;
use pocketmine\entity\AI\EntityAIFleeSun;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\item\Item as InHandItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\level\sound\LaunchSound;

class Skeleton extends Monster implements ProjectileSource{
	const NETWORK_ID = 34;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	public $maxhealth = 20;

	public $itemBow;

	public function initEntity(){
		$this->itemBow = new InHandItem(InHandItem::BOW);
		$this->tasks->addTask(1, new EntityAISwimming($this));
		//$this->tasks->addTask(2, new EntityAIRestrictSun($this));
		//$this->tasks->addTask(3, new EntityAIFleeSun($this, 1.0));
		//$this->tasks->addTask(3, new EntityAIAvoidEntity($this, "pocketmine\entity\Wolf", 6.0, 1.0, 1.2));
		$this->tasks->addTask(4, new EntityAIWander($this, 1.0));
		$this->tasks->addTask(4, new EntityAIArrowAttack($this, 1.0, 20, 60, 15.0));
		$this->tasks->addTask(6, new EntityAIWatchClosest($this, "pocketmine\Player", 8.0));
		$this->tasks->addTask(6, new EntityAILookIdle($this));
		$this->targetTasks->addTask(1, new EntityAIHurtByTarget($this, false, []));
		$this->targetTasks->addTask(2, new EntityAINearestAttackableTarget($this, "pocketmine\Player", true));
		$this->targetTasks->addTask(3, new EntityAINearestAttackableTarget($this, "pocketmine\entity\IronGolem", true));
		$this->setMaxHealth(20);
		parent::initEntity();
	}

	protected function addAttributes(){
		parent::addAttributes();
		$this->getAttributeMap()->getAttribute(Attribute::FOLLOW_RANGE)->setValue(16.0);
		$this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue(0.25);
	}

	public function getName(){
		return "Skeleton";
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Skeleton::NETWORK_ID;
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

		$pk = new MobEquipmentPacket();
		$pk->eid = $this->getId();
		$pk->item = $this->itemBow;
		$pk->slot = 0;
		$pk->selectedSlot = 0;
		$player->dataPacket($pk);
	}

	public function setAttackTarget($entitylivingbaseIn){
		parent::setAttackTarget($entitylivingbaseIn);
		//TODO \‚¦‚é
	}

	public function attackEntityWithRangedAttack($target, float $p_82196_2_){
		$posY = $this->y + $this->getEyeHeight() - 0.10000000149011612;
		$d0 = $target->x - $this->x;
		$d1 = $target->getBoundingBox()->minY + ($target->height / 3.0) - $this->y;
		$d2 = $target->z - $this->z;
		$d3 = sqrt($d0 * $d0 + $d2 * $d2);

		//if ($d3 >= 1.0E-7){
			$f = (atan2($d2, $d0) * 180.0 / M_PI) - 90.0;
			$f1 = (-(atan2($d1, $d3) * 180.0 / M_PI));
			$d4 = $d0 / $d3;
			$d5 = $d2 / $d3;
			$posX = $this->x + $d4;
			$posZ = $this->z + $d5;
			$f2 = ($d3 * 0.20000000298023224);
			$result = $this->getThrowableHeading($d0, $d1 + $f2, $d2, 1.6, 10);
		//}

		$nbt = new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $posX),
				new DoubleTag("", $posY),
				new DoubleTag("", $posZ)
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", $result[0]),
				new DoubleTag("", $result[1]),
				new DoubleTag("", $result[2])
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", $result[3]),
				new FloatTag("", $result[4])
			]),
			"Fire" => new ShortTag("Fire", 0)
		]);

		$ev = new EntityShootBowEvent($this, $this->itemBow, Entity::createEntity("Arrow", $this->getLevel(), $nbt, $this, true), 2);

		$this->server->getPluginManager()->callEvent($ev);

		if($ev->isCancelled()){
			$ev->getProjectile()->kill();
		}else{
			if($ev->getProjectile() instanceof Projectile){
				$this->server->getPluginManager()->callEvent($projectileEv = new ProjectileLaunchEvent($ev->getProjectile()));
				if($projectileEv->isCancelled()){
					$ev->getProjectile()->kill();
				}else{
					$ev->getProjectile()->spawnToAll();
					$this->level->addSound(new LaunchSound($this), $this->getViewers());
				}
			}else{
				$ev->getProjectile()->spawnToAll();
			}
		}
	}

	public function getThrowableHeading($x, $y, $z, float $velocity, float $inaccuracy) : array{
		$f = sqrt($x * $x + $y * $y + $z * $z);
		$x = $x / $f;
		$y = $y / $f;
		$z = $z / $f;
		$x = $x + (rand(0, 10) / 10) * (rand(0, 1) == 1 ? -1 : 1) * 0.007499999832361937 * $inaccuracy;
		$y = $y + (rand(0, 10) / 10) * (rand(0, 1) == 1 ? -1 : 1) * 0.007499999832361937 * $inaccuracy;
		$z = $z + (rand(0, 10) / 10) * (rand(0, 1) == 1 ? -1 : 1) * 0.007499999832361937 * $inaccuracy;
		$x = $x * $velocity;
		$y = $y * $velocity;
		$z = $z * $velocity;
		$motionX = $x;
		$motionY = $y;
		$motionZ = $z;
		$f1 = sqrt($x * $x + $z * $z);
		$yaw = (atan2($x, $z) * 180.0 / M_PI);
		$pitch = (atan2($y, $f1) * 180.0 / M_PI);
		return [$motionX, $motionY, $motionZ, $yaw, $pitch];
	}
}
