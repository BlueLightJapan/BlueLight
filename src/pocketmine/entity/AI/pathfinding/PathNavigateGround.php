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

use pocketmine\block\Block;
use pocketmine\math\Vector3;

class PathNavigateGround extends PathNavigate{

	protected $nodeProcessor;
	private $shouldAvoidSun;

	public function __construct($entitylivingIn, $worldIn){
		parent::__construct($entitylivingIn, $worldIn);
	}

	protected function getPathFinder(){
		$this->nodeProcessor = new WalkNodeProcessor();
		$this->nodeProcessor->setEnterDoors(true);
		return new PathFinder($this->nodeProcessor);
	}

	protected function canNavigate(){
		return $this->theEntity->onGround || $this->getCanSwim() && $this->isInLiquid();
	}

	protected function getEntityPosition(){
		return new Vector3($this->theEntity->x, $this->getPathablePosY(), $this->theEntity->z);
	}

	private function getPathablePosY(){
		if ($this->theEntity->isInsideOfWater() && this.getCanSwim()){
			$i = $this->theEntity->getBoundingBox()->minY;
			$block = $this->worldObj.getBlock(new Vector3(floor($this->theEntity->x), $i, floor($this->theEntity->z)));
			$j = 0;

			while ($block->getId() == Block::WATER || $block->getId() == Block::STILL_WATER){
				++$i;
				$block = $this->worldObj->getBlock(new Vector3(floor($this->theEntity->x), $i, floor($this->theEntity->z)));
				++$j;

				if ($j > 16){
					return $this->theEntity->getBoundingBox()->minY;
				}
			}

			return i;
		}else{
			return $this->theEntity->getBoundingBox()->minY + 0.5;
		}
	}

	protected function removeSunnyPath(){
		parent::removeSunnyPath();

		/*if ($this->shouldAvoidSun){
			if ($this->worldObj->canSeeSky(new BlockPos(floor($this->theEntity->x), $this->theEntity->getBoundingBox()->minY + 0.5, floor($this->theEntity->z)))){
				return;
			}

			for ($i = 0; $i < $this->currentPath->getCurrentPathLength(); ++$i){
				$pathpoint = $this->currentPath->getPathPointFromIndex($i);

				if ($this->worldObj->canSeeSky(new Vector3($pathpoint->x, $pathpoint->y, $pathpoint->z))){
					$this->currentPath->setCurrentPathLength($i - 1);
					return;
				}
			}
		}*/
	}

	protected function isDirectPathBetweenPoints($posVec31, $posVec32, $sizeX, $sizeY, $sizeZ){
		$i = floor($posVec31->x);
		$j = floor($posVec31->z);
		$d0 = $posVec32->x - $posVec31->x;
		$d1 = $posVec32->z - $posVec31->z;
		$d2 = $d0 * $d0 + $d1 * $d1;

		if ($d2 < 1.0E-8){
			return false;
		}else{
			$d3 = 1.0 / sqrt($d2);
			$d0 = $d0 * $d3;
			$d1 = $d1 * $d3;
			$sizeX = $sizeX + 2;
			$sizeZ = $sizeZ + 2;

			if (!$this->isSafeToStandAt($i, $posVec31->y, $j, $sizeX, $sizeY, $sizeZ, $posVec31, $d0, $d1)){
				return false;
			}else{
				$sizeX = $sizeX - 2;
				$sizeZ = $sizeZ - 2;
				$d4 = 1.0 / abs($d0);
				$d5 = 1.0 / abs($d1);
				$d6 = ($i * 1) - $posVec31->x;
				$d7 = ($j * 1) - $posVec31->z;

				if ($d0 >= 0.0){
					++$d6;
				}

				if ($d1 >= 0.0){
					++$d7;
				}

				$d6 = $d6 / $d0;
				$d7 = $d7 / $d1;
				$k = $d0 < 0.0 ? -1 : 1;
				$l = $d1 < 0.0 ? -1 : 1;
				$i1 = floor($posVec32->x);
				$j1 = floor($posVec32->z);
				$k1 = $i1 - $i;
				$l1 = $j1 - $j;

				while ($k1 * $k > 0 || $l1 * $l > 0){
					if ($d6 < $d7){
						$d6 += $d4;
						$i += $k;
						$k1 = $i1 - $i;
					}else{
						$d7 += $d5;
						$j += $l;
						$l1 = $j1 - $j;
					}

					if (!$this->isSafeToStandAt($i, $posVec31->y, $j, $sizeX, $sizeY, $sizeZ, $posVec31, $d0, $d1)){
						return false;
					}
				}

				return true;
			}
		}
	}

	private function isSafeToStandAt($x, $y, $z, $sizeX, $sizeY, $sizeZ, $vec31, $p_179683_8_, $p_179683_10_){
		$i = $x - $sizeX / 2;
		$j = $z - $sizeZ / 2;

		if (!$this->isPositionClear($i, $y, $j, $sizeX, $sizeY, $sizeZ, $vec31, $p_179683_8_, $p_179683_10_)){
			return false;
		}else{
			for ($k = $i; $k < $i + $sizeX; ++$k){
				for ($l = $j; $l < $j + $sizeZ; ++$l){
					$d0 = $k + 0.5 - $vec31->x;
					$d1 = $l + 0.5 - $vec31->z;

					if ($d0 * $p_179683_8_ + $d1 * $p_179683_10_ >= 0.0){
						$block = $this->worldObj->getBlock(new Vector3($k, $y - 1, $l));
						if ($block->getId() == Block::AIR){
							return false;
						}

						if ($block->getId() == Block::WATER && !$this->theEntity->isInsideOfWater()){
							return false;
						}

						if ($block->getId() == Block::LAVA){
							return false;
						}
					}
				}
			}

			return true;
		}
	}

	private function isPositionClear($p_179692_1_, $p_179692_2_, $p_179692_3_, $p_179692_4_, $p_179692_5_, $p_179692_6_, $p_179692_7_, $p_179692_8_, $p_179692_10_){
		/*for (BlockPos blockpos : BlockPos.getAllInBox(new BlockPos(p_179692_1_, p_179692_2_, p_179692_3_), new BlockPos(p_179692_1_ + p_179692_4_ - 1, p_179692_2_ + p_179692_5_ - 1, p_179692_3_ + p_179692_6_ - 1))){
			$d0 = $blockpos->getX() + 0.5 - $p_179692_7_->xCoord;
			$d1 = $blockpos.getZ() + 0.5 - $p_179692_7_->z;

			if ($d0 * $p_179692_8_ + $d1 * $p_179692_10_ >= 0.0){
				$block = $this->worldObj->getBlock($blockpos);

				if (!$block->isTransparent()){
					return false;
				}
			}
		}*/

		return true;
	}

	public function setAvoidsWater($avoidsWater){
		$this->nodeProcessor->setAvoidsWater($avoidsWater);
	}

	public function getAvoidsWater(){
		return $this->nodeProcessor->getAvoidsWater();
	}

	public function setBreakDoors($canBreakDoors){
		$this->nodeProcessor->setBreakDoors($canBreakDoors);
	}

	public function setEnterDoors($par1){
		$this->nodeProcessor->setEnterDoors($par1);
	}

	public function getEnterDoors(){
		return $this->nodeProcessor->getEnterDoors();
	}

	public function setCanSwim($canSwim){
		$this->nodeProcessor->setCanSwim($canSwim);
	}

	public function getCanSwim(){
		return $this->nodeProcessor->getCanSwim();
	}

	public function setAvoidSun($par1){
		$this->shouldAvoidSun = $par1;
	}
}