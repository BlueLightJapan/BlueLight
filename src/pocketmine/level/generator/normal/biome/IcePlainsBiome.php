<?php
/*
Finish
*/
namespace pocketmine\level\generator\normal\biome;
use pocketmine\level\generator\populator\TallGrass;
use pocketmine\level\generator\populator\Tree;
use pocketmine\block\Sapling;
class IcePlainsBiome extends PlainBiome{
	public function __construct(){
		parent::__construct();
		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(5);
		$trees = new Tree(Sapling::SPRUCE);
		$trees->setBaseAmount(1);
		$this->addPopulator($trees);
		$this->addPopulator($tallGrass);
		$this->setElevation(63, 74);
		$this->temperature = 0.05;
		$this->rainfall = 0.80;
	}
	public function getName() : string{
		return "Ice Plains";
	}
}
