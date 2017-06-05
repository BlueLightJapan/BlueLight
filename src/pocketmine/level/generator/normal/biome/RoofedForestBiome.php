<?php
/*
Need add red flower
*/
namespace pocketmine\level\generator\normal\biome;
use pocketmine\level\generator\populator\Sugarcane;
use pocketmine\level\generator\populator\TallGrass;
use pocketmine\level\generator\populator\Tree;
use pocketmine\block\Block;
use pocketmine\level\generator\populator\Flower;
use pocketmine\block\Sapling;
class RoofedForestBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$sugarcane = new Sugarcane();
		$sugarcane->setBaseAmount(6);
		$tallGrass = new TallGrass(9);
		$tallGrass->setBaseAmount(1);
		$trees = new Tree(Sapling::DARK_OAK);
		$tallGrass->setBaseAmount(9);
		$flower = new Flower();
		$flower->setBaseAmount(0);
		$flower->setRandomAmount(5);
		$flower->addType([Block::DANDELION, 0]);
		$this->addPopulator($flower);
		$this->addPopulator($sugarcane);
		$this->addPopulator($tallGrass);
		$this->addPopulator($trees);
		//$this->setElevation(66, 84); this is correct,but trees is not generated with that,need fix
		$this->setElevation(63, 68);
		$this->temperature = 0.70;
		$this->rainfall = 0.90;
	}
	public function getName() : string{
		return "Roofed Forest";
	}
}
