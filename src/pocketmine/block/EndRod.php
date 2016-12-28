<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;
use pocketmine\item\Tool;

class EndRod extends Flowable{

	protected $id = self::END_ROD;

	public function __construct(){

	}

	public function getName(){
		return "End Rod";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getHardness(){
		return 2;
	}

	protected function recalculateBoundingBox(){
		return new AxisAlignedBB(
			$this->x + 0.375,
			$this->y,
			$this->z + 0.375,
			$this->x + 0.625,
			$this->y + 1,
			$this->z + 0.625
		);
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$below = $this->getSide(0);
		if($target->isTransparent() === false){
			$faces = [
				0 => 0,
				1 => 1,
				2 => 3,
				3 => 2,
				4 => 5,
				5 => 4
			];
			$this->meta = $faces[$face];
			$this->getLevel()->setBlock($block, $this, true, true);
			return true;
		}
		return false;
	}
	public function getDrops(Item $item) : array {
		return [
			[$this->id, 0, 1],
		];
	}
}