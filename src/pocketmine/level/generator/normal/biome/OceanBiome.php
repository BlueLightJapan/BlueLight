<?php
/*
Need change. Islands is biomes ,no need add populators here
*/
namespace pocketmine\level\generator\normal\biome;
use pocketmine\level\generator\populator\SugarCane;
use pocketmine\level\generator\populator\TallGrass;
class OceanBiome extends WateryBiome{
	public function __construct(){
		parent::__construct();
		$sugarcane = new SugarCane();
		$sugarcane->setBaseAmount(6);
		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(5);
		$this->addPopulator($sugarcane);
		$this->addPopulator($tallGrass);
		$this->setElevation(46, 68);
		$this->temperature = 0.5;
		$this->rainfall = (float) 0.1;
	}
	public function getName() : string{
		return "Ocean";
	}
}
