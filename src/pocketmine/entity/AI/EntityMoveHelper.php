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
use pocketmine\math\Vector3;

class EntityMoveHelper{

	protected $entity;
	protected $posX;
	protected $posY;
	protected $posZ;
	protected $speed;
	public $update = false;

	public function __construct($entitylivingIn){
		$this->entity = $entitylivingIn;
		$this->posX = $entitylivingIn->x;
		$this->posY = $entitylivingIn->y;
		$this->posZ = $entitylivingIn->z;
		$this->update = false;
	}

	public function isUpdating() : bool{
		return $this->update;
	}

	public function getSpeed() : float{
		return $this->speed;
	}

	public function setMoveTo(float $x, float $y, float $z, float $speedIn){
		$this->posX = $x;
		$this->posY = $y + 1;
		$this->posZ = $z;
		$this->speed = $speedIn;
		$this->update = true;
	}

	public function onUpdateMoveHelper(){
		$this->entity->setMoveForward(0.0);

		if ($this->update){
			$this->update = false;
			$i = floor($this->entity->getBoundingBox()->minY + 0.5);
			$d0 = $this->posX - $this->entity->x;
			$d1 = $this->posZ - $this->entity->z;
			$d2 = $this->posY - $i;
			$d3 = $d0 * $d0 + $d2 * $d2 + $d1 * $d1;

			if ($d3 >= 2.500000277905201E-7){
				$f = (atan2($d1, $d0) * 180.0 / M_PI) - 90.0;
				$this->entity->yaw = $this->limitAngle($this->entity->yaw, $f, 30.0);
				$this->entity->setAIMoveSpeed($this->speed * $this->entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->getValue());

				if ($d2 > 0.0 && $d0 * $d0 + $d1 * $d1 < 1.0){
					$this->entity->getJumpHelper()->setJumping();
				}
			}
		}
	}

	public function wrapAngleTo180(float $value) : float{
		$value = $value % 360.0;

		if ($value >= 180.0){
			$value -= 360.0;
		}

		if ($value < -180.0){
			$value += 360.0;
		}

		return $value;
	}

	protected function limitAngle(float $p_75639_1_, float $p_75639_2_, float $p_75639_3_) : float{
		$f = self::wrapAngleTo180($p_75639_2_ - $p_75639_1_);

		if ($f > $p_75639_3_){
			$f = $p_75639_3_;
		}

		if ($f < -$p_75639_3_){
			$f = -$p_75639_3_;
		}

		$f1 = $p_75639_1_ + $f;

		if ($f1 < 0.0){
			$f1 += 360.0;
		}else if ($f1 > 360.0){
			$f1 -= 360.0;
		}

		return $f1;
	}

	public function getX() : float{
		return $this->posX;
	}

	public function getY() : float{
		return $this->posY;
	}

	public function getZ() : float{
		return $this->posZ;
	}
}