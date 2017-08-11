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

use pocketmine\entity\Living;

class EntityLookHelper{

	private $entity;
	private $deltaLookYaw;
	private $deltaLookPitch;
	private $isLooking;
	private $posX;
	private $posY;
	private $posZ;

	public function __construct($entitylivingIn){
		$this->entity = $entitylivingIn;
	}

	public function setLookPositionWithEntity($entityIn, $deltaYaw, $deltaPitch){
		if($entityIn == null){
			$this->isLooking = false;
			return;
		}
		$this->posX = $entityIn->x;

		if ($entityIn instanceof Living){
			$this->posY = $entityIn->y + $entityIn->getEyeHeight();
		}else{
			$this->posY = ($entityIn->getBoundingBox()->minY + $entityIn->getBoundingBox()->maxY) / 2.0;
		}

		$this->posZ = $entityIn->z;
		$this->deltaLookYaw = $deltaYaw;
		$this->deltaLookPitch = $deltaPitch;
		$this->isLooking = true;
	}

	public function setLookPosition(float $x, float $y, float $z, float $deltaYaw, float $deltaPitch){
		$this->posX = $x;
		$this->posY = $y;
		$this->posZ = $z;
		$this->deltaLookYaw = $deltaYaw;
		$this->deltaLookPitch = $deltaPitch;
		$this->isLooking = true;
	}

	public function onUpdateLook(){
		$this->entity->pitch = 0.0;

		if ($this->isLooking){
			$this->isLooking = false;
			$d0 = $this->posX - $this->entity->x;
			$d1 = $this->posY - ($this->entity->y + $this->entity->getEyeHeight());
			$d2 = $this->posZ - $this->entity->z;
			$d3 = sqrt($d0 * $d0 + $d2 * $d2);
			$f = (atan2($d2, $d0) * 180.0 / M_PI) - 90.0;
			$f1 = -(atan2($d1, $d3) * 180.0 / M_PI);
			$this->entity->pitch = $this->updateRotation($this->entity->pitch, $f1, $this->deltaLookPitch);
			$this->entity->headYaw = $this->updateRotation($this->entity->headYaw, $f, $this->deltaLookYaw);
		}else{
			$this->entity->headYaw = $this->updateRotation($this->entity->headYaw, $this->entity->renderYawOffset, 10.0);//
		}

		$f2 = self::wrapAngleTo180($this->entity->headYaw - $this->entity->renderYawOffset);//

		if (!$this->entity->getNavigator()->noPath()){
			if ($f2 < -75.0){
				$this->entity->headYaw = $this->entity->renderYawOffset - 75.0;//
			}

			if ($f2 > 75.0){
				$this->entity->headYaw = $this->entity->renderYawOffset + 75.0;//
			}
		}
	}

	public function wrapAngleTo180(float $value) : float{
		$value = $value % 360;

		if ($value >= 180.0){
			$value -= 360;
		}

		if ($value < -180.0){
			$value += 360;
		}

		return $value;
	}

	private function updateRotation($p_75652_1_, $p_75652_2_, $p_75652_3_) : float{
		$f = self::wrapAngleTo180($p_75652_2_ - $p_75652_1_);

		if ($f > $p_75652_3_){
			$f = $p_75652_3_;
		}

		if ($f < -$p_75652_3_){
			$f = -$p_75652_3_;
		}

		return $p_75652_1_ + $f;
	}

	public function getIsLooking() : bool{
		return $this->isLooking;
	}

	public function getLookPosX() : float{
		return $this->posX;
	}

	public function getLookPosY() : float{
		return $this->posY;
	}

	public function getLookPosZ() : float{
		return $this->posZ;
	}
}