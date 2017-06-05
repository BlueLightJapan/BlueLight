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
use pocketmine\entity\AI\EntityAIAttackOnCollide;
use pocketmine\entity\AI\EntityAIMoveTowardsRestriction;
use pocketmine\entity\AI\EntityAIWatchClosest;
use pocketmine\entity\AI\EntityAIHurtByTarget;
use pocketmine\entity\AI\EntityAILookIdle;
use pocketmine\entity\AI\EntityAIWander;
use pocketmine\entity\AI\EntityAINearestAttackableTarget;
use pocketmine\entity\AI\EntityAIEndermanFindPlayer;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\block\Block;

class Enderman extends Monster{

	const NETWORK_ID = 38;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	public $maxhealth = 40;

	public $isAggressive;

	public function getName(){
		return "Enderman";
	}

	public function initEntity(){
		try{
		$this->tasks->addTask(0, new EntityAISwimming($this));
		$this->tasks->addTask(2, new EntityAIAttackOnCollide($this, "", 1.0, false));
		$this->tasks->addTask(7, new EntityAIWander($this, 1.0));
		$this->tasks->addTask(8, new EntityAIWatchClosest($this, "pocketmine\Player", 8.0));
		$this->tasks->addTask(8, new EntityAILookIdle($this));
		//$this->tasks->addTask(10, new EntityAIEndermanPlaceBlock($this));
		//$this->tasks->addTask(11, new EntityAIEndermanTakeBlock($this));
		$this->targetTasks->addTask(1, new EntityAIHurtByTarget($this, false, []));
		$this->targetTasks->addTask(2, new EntityAIEndermanFindPlayer($this));
			}catch(\Throwable $e){echo($e);}
		$this->setMaxHealth(40);
		parent::initEntity();
	}

	protected function addAttributes(){
		parent::addAttributes();
		$this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue(0.30000001192092896);
		$this->getAttributeMap()->getAttribute(Attribute::ATTACK_DAMAGE)->setValue(7.0);
		$this->getAttributeMap()->getAttribute(Attribute::FOLLOW_RANGE)->setValue(64.0);
	}

	public function spawnTo(Player $player){
		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, 1 | (0 << 8));
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Enderman::NETWORK_ID;
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

	public function isScreaming() : bool{
		return $this->getDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_CONVERTING);
	}

	public function setScreaming(bool $screaming){
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_CONVERTING, $screaming);
	}

	public function shouldAttackPlayer($player){
		$item = $player->getInventory()->getHelmet();

		if ($item->getId() == Block::PUMPKIN){
			return false;
		}else{
			$vec3 = $player->getLook(1.0)->normalize();
			$vec31 = new Vector3($this->x - $player->x, $this->getBoundingBox()->minY + ($this->height / 2.0) - ($player->y + $player->getEyeHeight()), $this->z - $player->z);
			$d0 = $vec31->length();
			$vec31 = $vec31->normalize();
			$d1 = $vec3->dot($vec31);
			return $d1 > 1.0 - 0.025 / $d0 ? true : false;
		}
	}

	public function updateAITasks(){
		if ($this->isInsideOfWater()){
			$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_DROWNING, 1.0);
			$this->attack($ev->getFinalDamage(), $ev);
		}

		if ($this->isScreaming() && !$this->isAggressive && rand(0, 99) == 0){
			$this->setScreaming(false);
		}

		if ($this->level->isDaytime()){
			$f = $this->level->getBlockSkyLightAt($this->x, $this->y, $this->z) / 10;

			if ($f > 0.5 /*&& $this->level->canSeeSky($this)*/ && (rand(0, 10) / 10) * 30.0 < ($f - 0.4) * 2.0){
				$this->setAttackTarget(null);
				$this->setScreaming(false);
				$this->isAggressive = false;
				$this->teleportRandomly();
			}
		}

		parent::updateAITasks();
	}

	public function teleportRandomly() : bool{
		$d0 = $this->x + ((rand(0, 100) / 100) - 0.5) * 64.0;
		$d1 = $this->y + (rand(0, 63) - 32);
		$d2 = $this->z + ((rand(0, 100) / 100) - 0.5) * 64.0;
		return $this->teleportTo($d0, $d1, $d2);
	}

	public function teleportToEntity($entity) : bool{
		$vec3 = new Vector3($this->x -$entity->x, $this->getBoundingBox()->minY + ($this->height / 2.0) - $entity->y + $entity->getEyeHeight(), $this->z - $entity->z);
		$vec3 = $vec3->normalize();
		$d0 = 16.0;
		$d1 = $this->x + ((rand(0, 100) / 100) - 0.5) * 8.0 - $vec3->x * $d0;
		$d2 = $this->y + (rand(0, 15) - 8) - $vec3->y * $d0;
		$d3 = $this->z + ((rand(0, 100) / 100) - 0.5) * 8.0 - $vec3->z * $d0;
		return $this->teleportTo($d1, $d2, $d3);
	}

	protected function teleportTo(float $x, float $y, float $z) : bool{
		$d0 = $this->x;
		$d1 = $this->y;
		$d2 = $this->z;
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		$flag = false;
		$blockpos = new Vector3($this);

		$flag1 = false;

		while (!$flag1 && $blockpos->getY() > 0){
			$blockpos1 = $blockpos->getSide(Vector3::SIDE_DOWN);
			$block = $this->level->getBlock($blockpos1);

			if (!$block->isTransparent()){
				$flag1 = true;
			}else{
				--$this->y;
				$blockpos = $blockpos1;
			}
		}

		if ($flag1){
			$this->setPosition(new Vector3($this->x, $this->y, $this->z));

			if (!$this->isInsideOfSolid() && $this->isInsideOfWater()){//TODO
				$flag = true;
			}
		}

		if (!$flag){
			$this->setPosition(new Vector3($d0, $d1, $d2));
			return false;
		}else{
			$pk = new LevelEventPacket;
			$pk->evid = LevelEventPacket::EVENT_ADD_PARTICLE_MASK | 24;
			$pk->x = $x;
			$pk->y = $y + 3;
			$pk->z = $z;
			Server::getInstance()->getPlayer("nightmare3832")->teleport(new Vector3($this->x, $this->y, $this->z));
			$i = 128;

			for ($j = 0; $j < $i; ++$j){
				$d6 = $j / ($i - 1.0);
				$f = ((rand(0, 10) / 10) - 0.5) * 0.2;
				$f1 = ((rand(0, 10) / 10) - 0.5) * 0.2;
				$f2 = ((rand(0, 10) / 10) - 0.5) * 0.2;
				$d3 = $d0 + ($this->x - $d0) * $d6 + ((rand(0, 100) / 100) - 0.5) * $this->width * 2.0;
				$d4 = $d1 + ($this->y - $d1) * $d6 + (rand(0, 100) / 100) * $this->height;
				$d5 = $d2 + ($this->z - $d2) * $d6 + ((rand(0, 100) / 100) - 0.5) * $this->width * 2.0;
				//PortalParticle $d3, $d4, $d5, $f, $f1, $f2
			}

			//$d0, $d1, $d2, "mob.endermen.portal"
			//"mob.endermen.portal"
			return true;
		}
	}

	public function getDrops(){
		$drops = [];
		$ev = $this->getLastDamageCause();
		$looting = $ev instanceof EntityDamageByEntityEvent ? $ev->getDamager() instanceof Player ? $ev->getDamager()->getInventory()->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING) : 0 : 0;

		$pearls = rand(0, 1 + $looting);

		$drops[] = ItemItem::get(ItemItem::ENDER_PEARL, 0, $pearls);
		return $drops;
	}

	public function getEyeHeight(){
		return 2.55;
	}
}