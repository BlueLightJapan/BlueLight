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


namespace pocketmine\entity\AI\pathfinding;

class PathPoint{

	public $xCoord;
	public $yCoord;
	public $zCoord;
	private $hash;
	public $index = -1;
	public $totalPathDistance;
	public $distanceToNext;
	public $distanceToTarget;
	public $previous;
	public $visited;

	public function __construct($x, $y, $z){
		$this->xCoord = $x;
		$this->yCoord = $y;
		$this->zCoord = $z;
		$this->hash = PathPoint::makeHash($x, $y, $z);
	}

	public static function makeHash($x, $y, $z){
		return $y & 255 | ($x & 32767) << 8 | ($z & 32767) << 24 | ($x < 0 ? -2147483648 : 0) | ($z < 0 ? 32768 : 0);
	}

	public function distanceTo($pathpointIn){
		$f = $pathpointIn->xCoord - $this->xCoord;
		$f1 = $pathpointIn->yCoord - $this->yCoord;
		$f2 = $pathpointIn->zCoord - $this->zCoord;
		return sqrt($f * $f + $f1 * $f1 + $f2 * $f2);
	}

	public function distanceToSquared($pathpointIn){
		$f = $pathpointIn->xCoord - $this->xCoord;
		$f1 = $pathpointIn->yCoord - $this->yCoord;
		$f2 = $pathpointIn->zCoord - $this->zCoord;
		return $f * $f + $f1 * $f1 + $f2 * $f2;
	}

	public function equals($p_equals_1_){
		if (!($p_equals_1_ instanceof PathPoint)){
			return false;
		}else{
			$pathpoint = $p_equals_1_;
			return $this->hash == $pathpoint->hash && $this->xCoord == $pathpoint->xCoord && $this->yCoord == $pathpoint->yCoord && $this->zCoord == $pathpoint->zCoord;
		}
	}

	public function hashCode(){
		return $this->hash;
	}

	public function isAssigned(){
		return $this->index >= 0;
	}

	public function toString(){
		return $this->xCoord + ", " + $this->yCoord + ", " + $this->zCoord;
	}
}