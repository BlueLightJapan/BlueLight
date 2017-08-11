<?php
namespace pocketmine\entity\AI;

use pocketmine\math\Vector3;

class EntityAIMate extends EntityAIBase{

	private $theAnimal;
	public $theWorld;
	private $targetMate;
	public $spawnBabyDelay;
	public $moveSpeed;

	public function __construct($animal, float $speedIn){
		$this->theAnimal = $animal;
		$this->theWorld = $animal->level;
		$this->moveSpeed = $speedIn;
		$this->setMutexBits(3);
	}

	public function shouldExecute() : bool{
		if (!$this->theAnimal->isInLove()){
			return false;
		}else{
			$this->targetMate = $this->getNearbyMate();
			return $this->targetMate != null;
		}
	}

	public function continueExecuting() : bool{
		if($this->targetMate == null) return false;
		return $this->targetMate->isAlive() && $this->targetMate->isInLove() && $this->spawnBabyDelay < 60;
	}

	public function resetTask(){
		$this->targetMate = null;
		$this->spawnBabyDelay = 0;
	}

	public function updateTask(){
		if($this->targetMate == null) return;
		$this->theAnimal->getLookHelper()->setLookPositionWithEntity($this->targetMate, 10.0, $this->theAnimal->getVerticalFaceSpeed());
		$this->theAnimal->getNavigator()->tryMoveToEntityLiving($this->targetMate, $this->moveSpeed);
		++$this->spawnBabyDelay;

		if ($this->spawnBabyDelay >= 60 && $this->theAnimal->distanceSquared($this->targetMate) < 9.0){
			$this->spawnBaby();
		}
	}

	private function getNearbyMate(){
		$f = 8.0;
		$bb = clone $this->theAnimal->getBoundingBox();
		$list = $this->theWorld->getCollidingEntities($bb->expand($f, $f, $f), $this->theAnimal);
		foreach($list as $index => $entity){
			if(get_class($this->theAnimal) != get_class($entity)){
				unset($list[$index]);
			}
		}
		$d0 = PHP_INT_MAX;
		$entityanimal = null;
		foreach($list as $entityanimal1){
			if ($this->theAnimal->canMateWith($entityanimal1) && $this->theAnimal->distanceSquared($entityanimal1) < $d0){
				$entityanimal = $entityanimal1;
				$d0 = $this->theAnimal->distanceSquared($entityanimal1);
			}
		}
		return $entityanimal;
	}

	private function spawnBaby(){
		$entityageable = $this->theAnimal->createChild($this->targetMate);

		if ($entityageable != null){
			$entityplayer = $this->theAnimal->getPlayerInLove();

			if ($entityplayer == null && $this->targetMate->getPlayerInLove() != null){
				$entityplayer = $this->targetMate->getPlayerInLove();
			}

			$this->theAnimal->setGrowingAge(6000);
			$this->targetMate->setGrowingAge(6000);
			$this->theAnimal->resetInLove();
			$this->targetMate->resetInLove();
			$entityageable->setGrowingAge(-24000);
			$entityageable->setPositionAndRotation(new Vector3($this->theAnimal->x, $this->theAnimal->y, $this->theAnimal->z), 0.0, 0.0);
			//spawnExp
		}
	}
}