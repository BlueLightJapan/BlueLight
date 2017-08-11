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

abstract class NodeProcessor{

	protected $worldObj;
	protected $pointMap = [];
	protected $entitySizeX;
	protected $entitySizeY;
	protected $entitySizeZ;

	public function initProcessor($world, $entityIn){
		$this->worldObj = $world;
		$this->pointMap = [];
		$this->entitySizeX = floor($entityIn->width + 1.0);
		$this->entitySizeY = floor($entityIn->height + 1.0);
		$this->entitySizeZ = floor($entityIn->width + 1.0);
	}

	public function postProcess(){
	}

	protected function openPoint($x, $y, $z){
		$i = PathPoint::makeHash($x, $y, $z);

		if (empty($this->pointMap[$i])){
			$pathpoint = new PathPoint($x, $y, $z);
			$this->pointMap[$i] = $pathpoint;
		}else{
			$pathpoint = $this->pointMap[$i];
		}

		return $pathpoint;
	}

	public abstract function getPathPointTo($entityIn);

	public abstract function getPathPointToCoords($entityIn, $x, $y, $target);

	public abstract function findPathOptions($pathOptions, $entityIn, $currentPoint, $targetPoint, $maxDistance);
}