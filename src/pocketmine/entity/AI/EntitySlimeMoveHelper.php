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

use pocketmine\entity\Attribute;

class EntitySlimeMoveHelper extends EntityMoveHelper{

	private $field_179922_g = 0;
	private $field_179924_h = 0;
	private $slime;
	private $field_179923_j = false;

	public function __construct($slime){
		parent::__construct($slime);
		$this->slime = $slime;
	}

	public function func_179920_a(float $p_179920_1_, bool $p_179920_2_){
		$this->field_179922_g = $p_179920_1_;
		$this->field_179923_j = $p_179920_2_;
	}

	public function setSpeed(float $speedIn){
		$this->speed = $speedIn;
		$this->update = true;
	}

	public function onUpdateMoveHelper() {
		$this->entity->yaw = $this->limitAngle($this->entity->yaw, $this->field_179922_g, 30.0);
		$this->entity->headYaw = $this->entity->yaw;
		$this->entity->renderYawOffset = $this->entity->yaw;

		if (!$this->update){
			$this->entity->setMoveForward(0.0);
		}else{
			$this->update = false;

			if ($this->entity->onGround){
				$this->entity->setAIMoveSpeed($this->speed * $this->entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->getValue());

				if ($this->field_179924_h-- <= 0){
					$this->field_179924_h = $this->slime->getJumpDelay();

					if ($this->field_179923_j){
						$this->field_179924_h /= 3;
					}

					$this->slime->getJumpHelper()->setJumping();
				}else{
					$this->slime->moveStrafing = $this->slime->moveForward = 0.0;
					$this->entity->setAIMoveSpeed(0.0);
				}
			}else{
				$this->entity->setAIMoveSpeed($this->speed * $this->entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->getValue());
			}
		}
	}
}