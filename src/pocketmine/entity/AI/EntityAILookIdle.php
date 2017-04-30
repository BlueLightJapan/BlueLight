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

class EntityAILookIdle extends EntityAIBase{

	private $idleEntity;
	private $lookX;
	private $lookZ;
	private $idleTime;

	public function __construct($entitylivingIn){
		$this->idleEntity = $entitylivingIn;
		$this->setMutexBits(3);
	}

	public function shouldExecute() : bool{
		return rand(0, 100) / 100 < 0.02;
	}

	public function continueExecuting() : bool{
		return $this->idleTime >= 0;
	}

	public function startExecuting(){
		$d0 = (M_PI * 2) * rand(0, 100) / 100;
		$this->lookX = cos($d0);
		$this->lookZ = sin($d0);
		$this->idleTime = 20 + rand(0, 20);
	}

	public function updateTask(){
		--$this->idleTime;
		$this->idleEntity->getLookHelper()->setLookPosition($this->idleEntity->x + $this->lookX, $this->idleEntity->y + $this->idleEntity->getEyeHeight(), $this->idleEntity->z + $this->lookZ, 10.0, 40);
	}
}