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

use pocketmine\math\Vector3;

class RandomPositionGenerator{

	private static $staticVector;

	public static function findRandomTarget($entitycreatureIn, $xz, $y){
		return RandomPositionGenerator::findRandomTargetBlock($entitycreatureIn, $xz, $y, null);
	}

	public static function findRandomTargetBlockTowards($entitycreatureIn, $xz, $y, $targetVec3){
		RandomPositionGenerator::$staticVector = $targetVec3->subtract($entitycreatureIn->x, $entitycreatureIn->y, $entitycreatureIn->z);
		return RandomPositionGenerator::findRandomTargetBlock($entitycreatureIn, $xz, $y, RandomPositionGenerator::$staticVector);
	}

	public static function findRandomTargetBlockAwayFrom($entitycreatureIn, $xz, $y, $targetVec3){
		RandomPositionGenerator::$staticVector = (new Vector3($entitycreatureIn->x, $entitycreatureIn->y, $entitycreatureIn->z))->subtract($targetVec3);
		return RandomPositionGenerator::findRandomTargetBlock($entitycreatureIn, $xz, $y, $staticVector);
	}

	private static function findRandomTargetBlock($entitycreatureIn, $xz, $y, $targetVec3){
		//Random random = entitycreatureIn.getRNG();
		$flag = false;
		$i = 0;
		$j = 0;
		$k = 0;
		$f = -99999.0;
		$flag1;

		if ($entitycreatureIn->hasHome()){
			$d0 = $entitycreatureIn->getHomePosition()->distanceSquared(new Vector3(floor($entitycreatureIn->x), floor($entitycreatureIn->y), floor($entitycreatureIn->z))) + 4.0;
			$d1 = $entitycreatureIn->getMaximumHomeDistance() + $xz;
			$flag1 = $d0 < $d1 * $d1;
		}else{
			$flag1 = false;
		}

		for ($j1 = 0; $j1 < 10; ++$j1){
			$l = rand(0, 2 * $xz) - $xz;
			$k1 = rand(0, 2 * $y) - $y;
			$i1 = rand(0, 2 * $xz) - $xz;

			if ($targetVec3 == null || $l * $targetVec3->x + $i1 * $targetVec3->z >= 0.0){
				if ($entitycreatureIn->hasHome() && $xz > 1){
					$blockpos = $entitycreatureIn->getHomePosition();

					if ($entitycreatureIn->x > $blockpos->x){
						$l -= rand(0, $xz / 2 - 1);
					}else{
						$l += rand(0, $xz / 2 - 1);
					}

					if ($entitycreatureIn->z > $blockpos->z){
						$i1 -= rand(0, $xz / 2 - 1);
					}else{
						$i1 += rand(0, $xz / 2 - 1);
					}
				}

				$l = $l + floor($entitycreatureIn->x);
				$k1 = $k1 + floor($entitycreatureIn->y);
				$i1 = $i1 + floor($entitycreatureIn->z);
				$blockpos1 = new Vector3($l, $k1, $i1);

				if (!$flag1 || $entitycreatureIn->isWithinHomeDistanceFromPosition($blockpos1)){
					$f1 = $entitycreatureIn->getBlockPathWeight($blockpos1);

					if ($f1 > $f){
						$f = $f1;
						$i = $l;
						$j = $k1;
						$k = $i1;
						$flag = true;
					}
				}
			}
		}

		if ($flag){
			return new Vector3($i, $j, $k);
		}else{
			return null;
		}
	}
}