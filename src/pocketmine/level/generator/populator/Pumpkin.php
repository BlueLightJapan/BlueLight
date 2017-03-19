<?php
/*
Pumpkin populator
 */
namespace pocketmine\level\generator\populator;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
class Pumpkin extends VariableAmountPopulator{
	/** @var ChunkManager */
	private $level;
	public function __construct(){
		parent::__construct(1, 0, 64);
	}
	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		if(!$this->checkOdd($random)){
			return;
		}
		$this->level = $level;
		$amount = $this->getAmount($random);
		for($i = 0; $i < $amount; ++$i){
			$x = $chunkX * 16;
			$z = $chunkZ * 16;
			for($size = 6; $size > 0; $size--){
				$xx = $x - 7 + $random->nextRange(0, 15);
				$zz = $z - 7 + $random->nextRange(0, 15);
				$yy = $this->getHighestWorkableBlock($xx, $zz);
				if($yy !== -1 and $this->canPumpkinStay($xx, $yy, $zz)){
					$this->level->setBlockIdAt($xx, $yy, $zz, (($random->nextRange(0, 4)) == 0 ? Block::PUMPKIN : Block::BROWN_MUSHROOM));
				}
			}
		}
	}
	private function canPumpkinStay($x, $y, $z){
		$c = $this->level->getBlockIdAt($x, $y, $z);
		$b = $this->level->getBlockIdAt($x, $y - 1, $z);
		return ($c === Block::AIR or $c === Block::SNOW_LAYER) and ($b === Block::MYCELIUM or (!Block::$transparent[$b]));
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
