<?php
namespace pocketmine\entity\AI;

class EntityAISwimming extends EntityAIBase{

	private $theEntity;

	public function __cnstruct($entitylivingIn){
		$this->theEntity = $entitylivingIn;
		$this->setMutexBits(4);
		$entitylivingIn->getNavigator()->setCanSwim(true);
	}

	public function shouldExecute(){
		return $this->theEntity->isInsideOfWater() || $this->theEntity->isInsideOfLava();
	}

	public function updateTask(){
		if (rand(0, 10) / 10 < 0.8){
			$this->theEntity->getJumpHelper()->setJumping();
		}
	}
}