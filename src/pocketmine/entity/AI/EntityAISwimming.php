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

class EntityAISwimming extends EntityAIBase{

	private $theEntity;

	public function __construct($entitylivingIn){
		$this->theEntity = $entitylivingIn;
		$this->setMutexBits(4);
		$entitylivingIn->getNavigator()->setCanSwim(true);
	}

	public function shouldExecute() : bool{
		return $this->theEntity->isInsideOfWater() || $this->theEntity->isInsideOfLava();
	}

	public function updateTask(){
		if (rand(0, 10) / 10 < 0.8){
			$this->theEntity->getJumpHelper()->setJumping();
		}
	}
}