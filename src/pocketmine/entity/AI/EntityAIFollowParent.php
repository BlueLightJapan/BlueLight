<?php
namespace pocketmine\entity\AI;

class EntityAIFollowParent extends EntityAIBase{

	public $childAnimal;
	public $childClass;
	public $parentAnimal;
	public $moveSpeed;
	public $delayCounter;

	public function __construct($animal, float $speed){
		$this->childAnimal = $animal;
		$this->childClass = get_class($animal);
		$this->moveSpeed = $speed;
	}

	public function shouldExecute() : bool{
		if ($this->childAnimal->getGrowingAge() >= 0){
			return false;
		}else{
			$bb = clone $this->theWatcher->getBoundingBox();
			$list = $this->theWatcher->level->getCollidingEntities($bb->expand($this->maxDistanceForPlayer, 3.0, $this->maxDistanceForPlayer), $this->theWatcher);
			$distance = 9999999;
			$entityanimal = null;
			foreach($list as $entity) {
				if(get_class($entity) != $this->childClass) continue;
					if ($entity->getGrowingAge() >= 0){
						$d1 = $entity->distance($this->childAnimal);
						if($d1 > $p2e_distance) {
							$entityanimal = $entity;
							$distance = $p1;
						}
					}
				}
			}

			if ($entityanimal == null){
				return false;
			}else if ($distance < 9.0){
				return false;
			}else{
				$this->parentAnimal = $entityanimal;
				return true;
			}
		}
	}

	public function continueExecuting() : bool{
		if ($this->childAnimal->getGrowingAge() >= 0){
			return false;
		}else if (!$this->parentAnimal->isAlive()){
			return false;
		}else{
			$d0 = $this->childAnimal->distanceSquared($this->parentAnimal);
			return $d0 >= 9.0 && $d0 <= 256.0;
		}
	}

	public function startExecuting(){
		$this->delayCounter = 0;
	}

	public function resetTask(){
		$this->parentAnimal = null;
	}

	public function updateTask(){
		if (--$this->delayCounter <= 0){
			$this->delayCounter = 10;
			$this->childAnimal->getNavigator()->tryMoveToEntityLiving($this->parentAnimal, $this->moveSpeed);
		}
	}
}