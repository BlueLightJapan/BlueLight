<?php

namespace pocketmine\entity\AI;

abstract class EntityAIBase {

	private $mutexBits;

	public abstract function shouldExecute();

	public function continueExecuting(){
		return $this->shouldExecute();
	}

	public function isInterruptible(){
		return true;
	}

	public function startExecuting(){
	}

	public function resetTask(){
	}

	public function updateTask(){
	}

	public function setMutexBits($mutexBitsIn){
		$this->mutexBits = $mutexBitsIn;
	}

	public function getMutexBits(){
		return $this->mutexBits;
	}
}