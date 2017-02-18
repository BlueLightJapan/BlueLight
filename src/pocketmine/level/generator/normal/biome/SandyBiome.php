<?php
/*
Finish. Principal biome of cold biomes.
*/
namespace pocketmine\level\generator\normal\biome;
use pocketmine\block\Block;
use pocketmine\level\generator\populator\Cactus;
use pocketmine\level\generator\populator\DeadBush;
use pocketmine\level\generator\populator\Sugarcane;
class SandyBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$cactus = new Cactus();
		$cactus->setBaseAmount(2);
		$cactus->setRandomAmount(1);
		$deadBush = new DeadBush();
		$deadBush->setBaseAmount(1);
		$sugarcane = new Sugarcane();
		$sugarcane->setBaseAmount(6);
                $this->addPopulator($sugarcane);
		$this->addPopulator($cactus);
		$this->addPopulator($deadBush);
		$this->setElevation(63, 81);
		$this->temperature = 0.05;
		$this->rainfall = 0.80;
		$this->setGroundCover([
			Block::get(Block::SAND, 0),
			Block::get(Block::SAND, 0),
			Block::get(Block::SAND, 0),
			Block::get(Block::SAND, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
		]);
	}
	public function getName() : string{
		return "Sandy";
	}
}
