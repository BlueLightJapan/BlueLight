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
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/


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