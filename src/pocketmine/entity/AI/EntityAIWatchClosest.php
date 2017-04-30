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

class EntityAIWatchClosest extends EntityAIBase{

	protected $theWatcher;
	protected $closestEntity;
	protected $maxDistanceForPlayer;
	private $lookTime;
	private $chance;
	protected $watchedClass;

	public function __construct($entitylivingIn, string $watchTargetClass, float $maxDistance, float $chanceIn = 0.02){
		$this->theWatcher = $entitylivingIn;
		$this->watchedClass = $watchTargetClass;
		$this->maxDistanceForPlayer = $maxDistance;
		$this->chance = $chanceIn;
		$this->setMutexBits(2);
	}

	public function shouldExecute() : bool{
		if (rand(0, 100) / 100 >= $this->chance){
			return false;
		}else{
			if ($this->theWatcher->getAttackTarget() != null){
				$this->closestEntity = $this->theWatcher->getAttackTarget();
			}

			if ($this->watchedClass == "pocketmine\Player"){
				$distance = $this->maxDistanceForPlayer;
				$target = null;
				foreach($this->theWatcher->level->getPlayers() as $player) {

					$p2e_distance = $player->distance($this->theWatcher);
					if($distance > $p2e_distance and !$player->isCreative()) {
						$target = $player;
						$distance = $p2e_distance;
					}
				}
				$this->closestEntity = $target;
			}else{
				$bb = clone $this->theWatcher->getBoundingBox();
				$list = $this->theWatcher->level->getCollidingEntities($bb->expand($this->maxDistanceForPlayer, 3.0, $this->maxDistanceForPlayer), $this->theWatcher);
				$distance = $this->maxDistanceForPlayer;
				$target = null;
				foreach($list as $entity) {
					if(get_class($entity) != $this->watchedClass) continue;
					$p2e_distance = $entity->distance($this->theWatcher);
					if($distance > $p2e_distance) {
						$target = $entity;
						$distance = $p2e_distance;
					}
				}
				$this->closestEntity = $target;
			}

			return $this->closestEntity != null;
		}
	}

	public function continueExecuting() : bool{
		if(!($this->closestEntity instanceof Entity)) return false;
		return !$this->closestEntity->isAlive() ? false : ($this->theWatcher->distanceSquared($this->closestEntity) > ($this->maxDistanceForPlayer * $this->maxDistanceForPlayer) ? false : $this->lookTime > 0);
	}

	public function startExecuting(){
		$this->lookTime = 40 + rand(0, 4);
	}

	public function resetTask(){
		$this->closestEntity = null;
	}

	public function updateTask(){
		if($this->closestEntity instanceof Entity) 
		$this->theWatcher->getLookHelper()->setLookPosition($this->closestEntity->x, $this->closestEntity->y + $this->closestEntity->getEyeHeight(), $this->closestEntity->z, 10.0, 40);
		--$this->lookTime;
	}
}