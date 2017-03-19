<?php
/*
Finish
*/
namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Block;
use pocketmine\block\StainedClay;
use pocketmine\level\generator\populator\Cactus;
use pocketmine\level\generator\populator\DeadBush;

class MesaBiome extends SandyBiome{

	public function __construct(){
		parent::__construct();
		
		$cactus = new Cactus();
		$cactus->setBaseAmount(0);
		$cactus->setRandomAmount(2);
		$deadBush = new DeadBush();
		$cactus->setBaseAmount(2);
		$deadBush->setRandomAmount(5);

		$this->addPopulator($cactus);
		$this->addPopulator($deadBush);

		$this->setElevation(63, 81);
		
		$this->temperature = 2.00;
		$this->rainfall = 0.80;
		$this->setGroundCover([
			Block::get(Block::HARDENED_CLAY, 0),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_PINK),
			Block::get(Block::HARDENED_CLAY, 0),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_ORANGE),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_BLACK),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_GRAY),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_WHITE),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_ORANGE),
			Block::get(Block::HARDENED_CLAY, 0),
			Block::get(Block::HARDENED_CLAY, 0),
			Block::get(Block::HARDENED_CLAY, 0),
			Block::get(Block::HARDENED_CLAY, 0),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_YELLOW),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_BLACK),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_PINK),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_PINK),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::STAINED_CLAY, StainedClay::CLAY_WHITE),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
		]);
	}
		
	public function getName() : string{
		return "Mesa";
	}
}
