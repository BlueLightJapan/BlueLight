<?php
namespace pocketmine\entity\AI;

use pocketmine\math\Vector3;

class EntityAIFleeSun extends EntityAIBase{

	private $theCreature;
	private $shelterX;
	private $shelterY;
	private $shelterZ;
	private $movementSpeed;
	private $theWorld;

	public function __construct($theCreatureIn, float $movementSpeedIn){
		$this->theCreature = $theCreatureIn;
		$this->movementSpeed = $movementSpeedIn;
		$this->theWorld = $theCreatureIn->level;
		$this->setMutexBits(1);
	}

	public function shouldExecute() : bool{
		if (!$this->theWorld->isDaytime()){
			return false;
		}else if (!this.theCreature->isOnFire()){
			return false;
		}else if (!$this->theWorld->canSeeSky(new Vector3($this->theCreature->x, $this->theCreature->getBoundingBox()->minY, $this->theCreature->z))){
			return false;
		}else{
			$vec3 = $this->findPossibleShelter();

			if ($vec3 == null){
				return false;
			}else{
				$this->shelterX = $vec3->x;
				$this->shelterY = $vec3->y;
				$this->shelterZ = $vec3->z;
				return true;
			}
		}
	}

	public function continueExecuting() : bool{
		return !$this->theCreature->getNavigator()->noPath();
	}

	public function startExecuting(){
		$this->theCreature->getNavigator()->tryMoveToXYZ($this->shelterX, $this->shelterY, $this->shelterZ, $this->movementSpeed);
	}

	private function findPossibleShelter(){
		$blockpos = new Vector3($this->theCreature->x, $this->theCreature->getBoundingBox()->minY, $this->theCreature->z);

		for ($i = 0; $i < 10; ++$i){
			$blockpos1 = $blockpos->add(rand(0, 19) - 10, rand(0, 5) - 3, rand(0, 19) - 10);

			if (!$this->theWorld->canSeeSky($blockpos1) && $this->theCreature->getBlockPathWeight($blockpos1) < 0.0){
				return new Vector3($blockpos1->getX(), $blockpos1->getY(), $blockpos1->getZ());
			}
		}

		return null;
	}
}