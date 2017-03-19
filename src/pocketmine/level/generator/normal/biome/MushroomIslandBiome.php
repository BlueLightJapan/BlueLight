<?php

/*
Need add mushroom here.
*/

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Block;

class MushroomIslandBiome extends GrassyBiome{

	public function __construct(){
		parent::__construct();
		$this->setGroundCover([
			Block::get(Block::MYCELIUM, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0),
		]);
		$this->temperature = 0.90;
		$this->rainfall = 0.20;
	}
	public function getName() : string{
		return "Mushroom Island";
	}
}
