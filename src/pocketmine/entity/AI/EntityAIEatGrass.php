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

use pocketmine\block\Block;
use pocketmine\math\Vector3;

class EntityAIEatGrass extends EntityAIBase{

	private $grassEaterEntity;
	private $entityWorld;
	public $eatingGrassTimer;

	public function __construct($grassEaterEntityIn){
		$this->grassEaterEntity = $grassEaterEntityIn;
		$this->entityWorld = $grassEaterEntityIn->level;
		$this->setMutexBits(7);
	}

	public function shouldExecute() : bool{
		if (rand(0, $this->grassEaterEntity->isBaby() ? 50 : 1000) != 0){
			return false;
		}else{
			$blockpos = new Vector3($this->grassEaterEntity->x, $this->grassEaterEntity->y, $this->grassEaterEntity->z);
			return $this->entityWorld->getBlock($blockpos->getSide(Vector3::SIDE_DOWN))->getId() == Block::GRASS;
		}
	}

	public function startExecuting(){
		$this->eatingGrassTimer = 40;
		$this->grassEaterEntity->doEatGrass();
		$this->grassEaterEntity->getNavigator()->clearPathEntity();
	}

	public function resetTask(){
		$this->eatingGrassTimer = 0;
	}

	public function continueExecuting() : bool{
		return $this->eatingGrassTimer > 0;
	}

	public function getEatingGrassTimer() : int{
		return $this->eatingGrassTimer;
	}

	public function updateTask(){
		$this->eatingGrassTimer = max(0, $this->eatingGrassTimer - 1);

		if ($this->eatingGrassTimer == 4){
			$blockpos = new Vector3($this->grassEaterEntity->x, $this->grassEaterEntity->y, $this->grassEaterEntity->z);
			$blockpos1 = $blockpos->getSide(Vector3::SIDE_DOWN);

			if ($this->entityWorld->getBlock($blockpos1)->getId() == Block::GRASS){
				$this->entityWorld->setBlock($blockpos1, Block::get(Block::DIRT));
			}

			$this->grassEaterEntity->eatGrassBonus();
                }
	}
}