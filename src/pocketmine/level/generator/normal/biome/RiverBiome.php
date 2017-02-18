<?php
/*
Finish
*/
namespace pocketmine\level\generator\normal\biome;
use pocketmine\level\generator\populator\SugarCane;
use pocketmine\level\generator\populator\TallGrass;
use pocketmine\level\generator\populator\Mushroom;
use pocketmine\block\Block;
class RiverBiome extends WateryBiome{
	public function __construct(){
		parent::__construct();
		$sugarcane = new SugarCane();
		$sugarcane->setBaseAmount(6);
		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(25);
		$mushroom = new Mushroom();
		$this->addPopulator($mushroom);
		$this->addPopulator($sugarcane);
		$this->addPopulator($tallGrass);
		$this->setElevation(58, 62);
		$this->temperature = 0.50;
		$this->rainfall = 0.70;
	}
	public function getName() : string{
		return "River";
	}
}
