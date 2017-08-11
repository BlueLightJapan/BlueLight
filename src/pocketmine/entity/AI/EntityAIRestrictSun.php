<?php
namespace pocketmine\entity\AI;

class EntityAIRestrictSun extends EntityAIBase{

	private $theEntity;

	public function __construct($creture){
		$this->theEntity = $creture;
		$this->setMutexBits(0);
	}

	public function shouldExecute() : bool{
		return $this->theEntity->level->isDaytime();
	}

	public function startExecuting(){
		$this->theEntity->getNavigator()->setAvoidSun(true);
	}

	public function resetTask(){
		$this->theEntity->getNavigator()->setAvoidSun(false);
	}
}