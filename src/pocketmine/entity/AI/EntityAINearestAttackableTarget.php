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

use pocketmine\entity\Entity;

class EntityAINearestAttackableTarget extends EntityAITarget{

	protected $targetClass;
	private $targetChance;
	protected $targetEntity;

	public function __construct($creature, string $classTarget, bool $checkSight, int $chance = 10, bool $onlyNearby = false, $targetSelector = null){
		parent::__construct($creature, $checkSight, $onlyNearby);
		$this->targetClass = $classTarget;
		$this->targetChance = $chance;
		$this->setMutexBits(1);
	}

	public function shouldExecute() : bool{
		if ($this->targetChance > 0 && rand(0, $this->targetChance - 1) != 0){
			return false;
		}else{
			$d0 = $this->getTargetDistance();
			$bb = clone $this->taskOwner->getBoundingBox();
			$list = $this->taskOwner->level->getCollidingEntities($bb->expand($d0, 4.0, $d0), $this->taskOwner);
			foreach ($list as $index => $entity){
				if(get_class($entity) != $this->targetClass){
					unset($list[$index]);
				}
			}
			if(count($list) == 0){
				return false;
			}else{
				$target = $this->getNearestAttackableTarget($list);
				if($target instanceof Entity){
					$this->targetEntity = $target;
					return true;
				}
				return false;
			}
		}
	}

	public function startExecuting(){
		$this->taskOwner->setAttackTarget($this->targetEntity);
		parent::startExecuting();
	}

	public function getNearestAttackableTarget($list){
		$result = null;
		$distance = null;
		$owner = $this->taskOwner->getPosition();
		foreach ($list as $entity){
			$d = $entity->distance($owner);
			if($distance == null || $distance > $d){
				$distance = $d;
				$result = $entity;
			}
		}
		return $result;
	}
}