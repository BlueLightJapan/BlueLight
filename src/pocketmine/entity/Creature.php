<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\entity;

use pocketmine\math\Vector3;

abstract class Creature extends Living{

	private $homePosition;
	private $maximumHomeDistance = 99999999;

	public function getBlockPathWeight($pos) : float{
		return 0.0;
	}

	public function isWithinHomeDistanceCurrentPosition() : bool{
		return $this->isWithinHomeDistanceFromPosition($this);
	}

	public function isWithinHomeDistanceFromPosition($pos) : bool{
		return $this->maximumHomeDistance == -1.0 ? true : $this->homePosition->distanceSquared($pos) < ($this->maximumHomeDistance * $this->maximumHomeDistance);
	}

	public function setHomePosAndDistance($pos, $distance){
		$this->homePosition = $pos;
		$this->maximumHomeDistance = $distance;
	}

	public function getHomePosition(){
		if(!($this->homePosition instanceof Vector3)) $this->homePosition = new Vector3($this->x, $this->y, $this->z);
		return $this->homePosition;
	}

	public function getMaximumHomeDistance() : float{
		return $this->maximumHomeDistance;
	}

	public function detachHome(){
		$this->maximumHomeDistance = -1.0;
	}

	public function hasHome() : bool{
		return $this->maximumHomeDistance != -1.0;
	}

}