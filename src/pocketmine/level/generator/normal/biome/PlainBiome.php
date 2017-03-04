<?php
/*
Finish
*/
namespace pocketmine\level\generator\normal\biome;
use pocketmine\level\generator\populator\TallGrass;
use pocketmine\level\generator\populator\WaterPit;
use pocketmine\level\generator\populator\PopulatorTallGrass;
use pocketmine\block\Block;
use pocketmine\block\Flower as FlowerBlock;
use pocketmine\level\generator\populator\Flower;
use pocketmine\level\generator\populator\SugarCane;
use pocketmine\level\generator\populator\Pumpkin;
use pocketmine\level\generator\populator\Mushroom;
class PlainBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$sugarcane = new SugarCane();
		$sugarcane->setBaseAmount(6);
		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(25);
		$waterPit = new WaterPit();
		$waterPit->setBaseAmount(10);
		$populatorTallGrass = new PopulatorTallGrass();
		$populatorTallGrass->setBaseAmount(25);
		$mushroom = new Mushroom();
		$this->addPopulator($mushroom);
		$pumpkin = new Pumpkin();
		$this->addPopulator($pumpkin);
		$flower = new Flower();
		$flower->setBaseAmount(0);
		$flower->setRandomAmount(5);
		$flower->addType([Block::DANDELION, 0]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_POPPY]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_AZURE_BLUET]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_RED_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_ORANGE_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_WHITE_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_PINK_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_OXEYE_DAISY]);
		$this->addPopulator($flower);
		$this->addPopulator($sugarcane);
		$this->addPopulator($tallGrass);
		$this->addPopulator($waterPit);
		$this->addPopulator($populatorTallGrass);
		$this->setElevation(64, 72);
		$this->temperature = 0.8;
		$this->rainfall = (float) 0.05;
	}
	public function getName() : string{
		return "Plains";
	}
}
