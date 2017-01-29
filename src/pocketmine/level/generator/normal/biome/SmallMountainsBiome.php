<?php

/*
Principal biome of small mountains biomes.
*/

namespace pocketmine\level\generator\normal\biome;
use pocketmine\block\Block;

class SmallMountainsBiome extends GrassyBiome{

	public function __construct(){
		parent::__construct();

		$this->setElevation(63, 97);
	}

	public function getName() : string{
		return "Small Mountains";
	}
}
