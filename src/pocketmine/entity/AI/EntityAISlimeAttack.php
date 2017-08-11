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

use pocketmine\Player;

class EntityAISlimeAttack extends EntityAIBase{

	private $slime;
	private $field_179465_b;

	public function __construct($slime){
		$this->slime = $slime;
		$this->setMutexBits(2);
	}

	public function shouldExecute() : bool{
		$entitylivingbase = $this->slime->getAttackTarget();
		return $entitylivingbase == null ? false : (!$entitylivingbase->isAlive() ? false : !($entitylivingbase instanceof Player) || !$entitylivingbase->isCreative());
	}

	public function startExecuting(){
		$this->field_179465_b = 300;
		parent::startExecuting();
	}

	public function continueExecuting() : bool{
		$entitylivingbase = $this->slime->getAttackTarget();
		return $entitylivingbase == null ? false : (!$entitylivingbase->isAlive() ? false : ($entitylivingbase instanceof Player && $entitylivingbase->isCreative() ? false : --$this->field_179465_b > 0));
	}

	public function updateTask(){
		$this->slime->faceEntity($this->slime->getAttackTarget(), 10.0, 10.0);
		$this->slime.getMoveHelper()->func_179920_a($this->slime->yaw, $this->slime->canDamagePlayer());
	}
}