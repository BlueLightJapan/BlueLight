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

namespace pocketmine\entity\AI;

use pocketmine\entity\Attribute;
use pocketmine\math\Vector3;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class EntityAIAttackOnCollide extends EntityAIBase{

	public $worldObj;
	protected $attacker;
	public $attackTick;
	public $speedTowardsTarget;
	public $longMemory;
	public $entityPathEntity;
	public $classTarget;
	private $delayCounter;
	private $targetX = 0;
	private $targetY = 0;
	private $targetZ = 0;

	public function __construct($creature, string $targetClass, float $speedIn, bool $useLongMemory){
		$this->classTarget = $targetClass;
		$this->attacker = $creature;
		$this->worldObj = $creature->level;
		$this->speedTowardsTarget = $speedIn;
		$this->longMemory = $useLongMemory;
		$this->setMutexBits(3);
	}

	public function shouldExecute() : bool{
		$entitylivingbase = $this->attacker->getAttackTarget();

		if ($entitylivingbase == null){
			return false;
		}else if (!$entitylivingbase->isAlive()){
			return false;
		}else if ($this->classTarget != null && $this->classTarget != get_class($entitylivingbase)){
			return false;
		}else{
			$this->entityPathEntity = $this->attacker->getNavigator()->getPathToEntityLiving($entitylivingbase);
			return $this->entityPathEntity != null;
		}
	}

	public function continueExecuting() : bool{
		$entitylivingbase = $this->attacker->getAttackTarget();
		return $entitylivingbase == null ? false : (!$entitylivingbase->isAlive() ? false : (!$this->longMemory ? !$this->attacker->getNavigator()->noPath() : $this->attacker->isWithinHomeDistanceFromPosition($entitylivingbase)));
	}

	public function startExecuting(){
		$this->attacker->getNavigator()->setPath($this->entityPathEntity, $this->speedTowardsTarget);
		$this->delayCounter = 0;
	}

	public function resetTask(){
		$this->attacker->getNavigator()->clearPathEntity();
	}

	public function updateTask(){
		$entitylivingbase = $this->attacker->getAttackTarget();
		if($entitylivingbase == null){
			return;
		}
		$this->attacker->getLookHelper()->setLookPositionWithEntity($entitylivingbase, 30.0, 30.0);
		$d0 = $this->attacker->distanceSquared(new Vector3($entitylivingbase->x, $entitylivingbase->getBoundingBox()->minY, $entitylivingbase->z));
		$d1 = $this->getReachableDistance($entitylivingbase);
		--$this->delayCounter;

		if (($this->longMemory && $this->delayCounter <= 0 && ($this->targetX == 0.0 && $this->targetY == 0.0 && $this->targetZ == 0.0 || $entitylivingbase->distanceSquared(new Vector3($this->targetX, $this->targetY, $this->targetZ)) >= 1.0 || (rand(0, 100) / 100) < 0.05))){
			$this->targetX = $entitylivingbase->x;
			$this->targetY = $entitylivingbase->getBoundingBox()->minY;
			$this->targetZ = $entitylivingbase->z;
			$this->delayCounter = 4 + rand(0, 6);

			if ($d0 > 1024.0){
				$this->delayCounter += 10;
			}else if ($d0 > 256.0){
				$this->delayCounter += 5;
			}

			if (!$this->attacker->getNavigator()->tryMoveToEntityLiving($entitylivingbase, $this->speedTowardsTarget)){
				$this->delayCounter += 15;
			}
		}

		$this->attackTick = max($this->attackTick - 1, 0);

		if ($d0 <= $d1 && $this->attackTick <= 0){
			$this->attackTick = 20;

			$ev = new EntityDamageByEntityEvent($this->attacker, $entitylivingbase, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->attacker->getAttributeMap()->getAttribute(Attribute::ATTACK_DAMAGE)->getValue());
			$entitylivingbase->attack($ev->getFinalDamage(), $ev);
		}
	}

	protected function getReachableDistance($attackTarget) : float{
		return ($this->attacker->width * 2.0 * $this->attacker->width * 2.0 + $attackTarget->width);
	}
}