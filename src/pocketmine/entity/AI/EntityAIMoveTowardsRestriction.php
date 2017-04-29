<?php

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

	public function __construct($creatureIn, $speedIn) {
		$this->theEntity = $creatureIn;
		$this->movementSpeed = $speedIn;
		$this->setMutexBits(1);
	}

	public function shouldExecute(){
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

	public function continueExecuting(){
		return !$this->theEntity->getNavigator()->noPath();
	}

	public function startExecuting(){
		$this->theEntity->getNavigator()->tryMoveToXYZ($this->movePosX, $this->movePosY, $this->movePosZ, $this->movementSpeed);
	}
}