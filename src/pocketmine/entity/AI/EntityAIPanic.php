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

class EntityAIPanic extends EntityAIBase{

	private $theEntityCreature;
	protected $speed;
	private $randPosX;
	private $randPosY;
	private $randPosZ;

	public function __construct($creature, float $speedIn){
		$this->theEntityCreature = $creature;
		$this->speed = $speedIn;
		$this->setMutexBits(1);
	}

	public function shouldExecute() : bool{
		if ($this->theEntityCreature->getAITarget() == null && !$this->theEntityCreature->isOnFire()){
			return false;
		}else{
			$vec3 = RandomPositionGenerator::findRandomTarget($this->theEntityCreature, 5, 4);

			if ($vec3 == null){
				return false;
			}else{
				$this->randPosX = $vec3->x;
				$this->randPosY = $vec3->y;
				$this->randPosZ = $vec3->z;
				return true;
			}
		}
	}

	public function startExecuting(){
		$this->theEntityCreature->getNavigator()->tryMoveToXYZ($this->randPosX, $this->randPosY, $this->randPosZ, $this->speed);
	}

	public function continueExecuting() : bool{
		return !$this->theEntityCreature->getNavigator()->noPath();
	}
}