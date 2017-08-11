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

use pocketmine\level\Position;
use pocketmine\level\Level;

use pocketmine\block\Transparent;
use pocketmine\block\Stair;

class EntityAIMoveTowardsRestriction extends EntityAIBase {

	private $theEntity;
	private $movePosX;
	private $movePosY;
	private $movePosZ;
	private $movementSpeed;

	public function __construct($creatureIn, float $speedIn) {
		$this->theEntity = $creatureIn;
		$this->movementSpeed = $speedIn;
		$this->setMutexBits(1);
	}

	public function shouldExecute() : bool{
		//if ($this->theEntity->isWithinHomeDistanceCurrentPosition()){
		//	return false;
		//}else{
			$blockpos = $this->theEntity->getHomePosition();
			$vec3 = RandomPositionGenerator::findRandomTargetBlockTowards($this->theEntity, 16, 7, clone $blockpos);

			if ($vec3 == null){
				return false;
			}else{
				$this->movePosX = $vec3->x;
				$this->movePosY = $vec3->y;
				$this->movePosZ = $vec3->z;
				return true;
			}
		//}
	}

	public function continueExecuting() : bool{
		return !$this->theEntity->getNavigator()->noPath();
	}

	public function startExecuting(){
		$this->theEntity->getNavigator()->tryMoveToXYZ($this->movePosX, $this->movePosY, $this->movePosZ, $this->movementSpeed);
	}
}