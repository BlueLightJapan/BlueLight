<?php
namespace pocketmine\entity\AI;

class EntityAIWander extends EntityAIBase{

	private $entity;
	private $xPosition;
	private $yPosition;
	private $zPosition;
	private $speed;
	private $executionChance;
	private $mustUpdate;

	public function __construct($creatureIn, $speedIn, $chance = 120){
		$this->entity = $creatureIn;
		$this->speed = $speedIn;
		$this->executionChance = $chance;
		$this->setMutexBits(1);
	}

	public function shouldExecute(){
		if (!$this->mustUpdate){
			if ($this->entity->getAge() >= 100){
				return false;
			}

			if (rand(0, $this->executionChance - 1) != 0){
				return false;
			}
		}

		$vec3 = RandomPositionGenerator::findRandomTarget($this->entity, 10, 7);

		if (vec3 == null){
			return false;
		}else{
			$this->xPosition = $vec3->x;
			$this->yPosition = $vec3->y;
			$this->zPosition = $vec3->z;
			$this->mustUpdate = false;
			return true;
		}
	}

	public function continueExecuting(){
		return !$this->entity->getNavigator()->noPath();
	}

	public function startExecuting(){
		$this->entity->getNavigator()->tryMoveToXYZ($this->xPosition, $this->yPosition, $this->zPosition, $this->speed);
	}

	public function makeUpdate(){
		$this->mustUpdate = true;
	}

	public function setExecutionChance($newchance){
		$this->executionChance = $newchance;
	}
}