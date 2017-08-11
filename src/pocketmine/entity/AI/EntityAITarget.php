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
use pocketmine\Player;

abstract class EntityAITarget extends EntityAIBase{

	protected $taskOwner;
	protected $shouldCheckSight;
	private $nearbyOnly;
	private $targetSearchStatus;
	private $targetSearchDelay;
	private $targetUnseenTicks;

	public function __construct($creature, bool $checkSight, bool $onlyNearby = false){
		$this->taskOwner = $creature;
		$this->shouldCheckSight = $checkSight;
		$this->nearbyOnly = $onlyNearby;
	}

	public function continueExecuting() : bool{
		$entitylivingbase = $this->taskOwner->getAttackTarget();

		if ($entitylivingbase == null){
			return false;
		}else if (!$entitylivingbase->isAlive()){
			return false;
		}else{
			/*$team = $this->taskOwner->getTeam();
			$team1 = $entitylivingbase->getTeam();

			if ($team != null && $team1 == $team){
				return false;
			}else{*/
				$d0 = $this->getTargetDistance();

				if ($this->taskOwner->distanceSquared($entitylivingbase) > $d0 * $d0){
					return false;
				}/*else{
					if ($this->shouldCheckSight){
						$this->targetUnseenTicks = 0;
					}
				}*/

				return true;//!($entitylivingbase instanceof Player);
			//}
		}
	}

	protected function getTargetDistance() : float{
		return $this->taskOwner->getAttributeMap()->getAttribute(Attribute::FOLLOW_RANGE)->getValue();
	}

	public function startExecuting(){
		$this->targetSearchStatus = 0;
		$this->targetSearchDelay = 0;
		$this->targetUnseenTicks = 0;
	}

	public function resetTask(){
		$this->taskOwner->setAttackTarget(null);
	}

	public function isSuitableTarget($a1, $a2, $a3 = null, $a4 = null) : bool{
		if($a3 == null){
			$target = $a1;
			$includeInvincibles = $a2;
			if (!$this->isSuitableTarget($this->taskOwner, $target, $includeInvincibles, $this->shouldCheckSight)){
				return false;
			}else if (!$this->taskOwner->isWithinHomeDistanceFromPosition(new Vector3($target))){
				return false;
			}else{
				if ($this->nearbyOnly){
					if (--$this->targetSearchDelay <= 0){
						$this->targetSearchStatus = 0;
					}

					if ($this->targetSearchStatus == 0){
						$this->targetSearchStatus = $this->canEasilyReach($target) ? 1 : 2;
					}

					if ($this->targetSearchStatus == 2){
						return false;
					}
				}

				return true;
			}
		}else{
			$attacker = $a1;
			$target = $a2;
			$includeInvincibles = $a3;
			$checkSight = $a4;
			if ($target == null){
				return false;
			}else if ($target == $attacker){
				return false;
			}else if (!$target->isAlive()){
				return false;
			}/*else if (!attacker->canAttackClass(get_class($target))){
				return false;
			}*/else{
				/*$team = $attacker->getTeam();
				$team1 = $target->getTeam();

				if ($team != null && $team1 == $team){
					return false;
				}else{*/
					/*if (attacker instanceof Ownable && StringUtils.isNotEmpty(((IEntityOwnable)attacker).getOwnerId())){
						if (target instanceof Ownable && ((IEntityOwnable)attacker).getOwnerId().equals(((IEntityOwnable)target).getOwnerId())){
							return false;
						}

						if (target == ((IEntityOwnable)attacker).getOwner()){
							return false;
						}
					}else */if ($target instanceof Player && !$includeInvincibles){
						return false;
					}

					return true;
				//}
			}
		}
	}

	private function canEasilyReach($livingEntity) : bool{
		$this->targetSearchDelay = 10 + rand(0, 4);
		$pathentity = $this->taskOwner->getNavigator()->getPathToEntityLiving($livingEntity);

		if ($pathentity == null){
			return false;
		}else{
			$pathpoint = $pathentity->getFinalPathPoint();

			if ($pathpoint == null){
				return false;
			}else{
				$i = $pathpoint->xCoord - floor($livingEntity->x);
				$j = $pathpoint->zCoord - floor($livingEntity->z);
				return ($i * $i + $j * $j) <= 2.25;
			}
		}
	}
}