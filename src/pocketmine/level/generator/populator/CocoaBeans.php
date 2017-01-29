<?php
/*
CocoaBeans populator
*/
namespace pocketmine\level\generator\populator;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use pocketmine\level\generator\populator\VariableAmountPopulator;
class CocoaBeans extends VariableAmountPopulator{
	/** @var ChunkManager */
	private $level;

	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		$this->level = $level;
		$amount = $this->getAmount($random);
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y !== -1 and $this->canCocoaBeansStay($x, $y, $z)){
				$this->level->setBlockIdAt($x, $y, $z, Block::COCOA);
				$this->level->setBlockDataAt($x, $y, $z, 1);
			}
		}
	}
	private function canCocoaBeansStay($x, $y, $z){
		$b = $this->level->getBlockIdAt($x, $y, $z);
		return $this->level->getBlockIdAt($x, $y, $z) === Block::WOOD;
	}
	private function getHighestWorkableBlock($x, $z){
		for($y = 127; $y >= 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b !== Block::AIR and $b !== Block::LEAVES and $b !== Block::LEAVES2 and $b !== Block::SNOW_LAYER){
				break;
			}
		}
		return $y === 0 ? -1 : ++$y;
	}
}
