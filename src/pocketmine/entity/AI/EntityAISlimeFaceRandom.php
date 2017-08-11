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

class EntityAISlimeFaceRandom extends EntityAIBase{

	private $slime;
	private $randomYaw;
	private $field_179460_c;

	public function __construct($slime){
		$this->slime = $slime;
		$this->setMutexBits(2);
	}

	public function shouldExecute() : bool{
		return $this->slime->getAttackTarget() == null && ($this->slime->onGround || $this->slime->isInsideOfWater() || $this->slime->isInsideOfLava());
	}

	public function updateTask(){
		if (--$this->field_179460_c <= 0){
			$this->field_179460_c = 40 + rand(0, 59);
			$this->randomYaw = rand(0, 359);
                }

		$this->slime->getMoveHelper()->func_179920_a($this->randomYaw, false);
            }
}