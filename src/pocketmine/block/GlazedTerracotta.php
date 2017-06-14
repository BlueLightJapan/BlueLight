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
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\Player;

class GlazedTerracotta extends Solid{

	public function getHardness(){
		return 1.4;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($player !== null){
			$faces = [
				0 => 4,
				1 => 3,
				2 => 5,
				3 => 2
			];
			$this->meta = $faces[(~($player->getDirection() - 1)) & 0x03];
		}

		return $this->getLevel()->setBlock($block, $this, true, true);
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= TieredTool::TIER_WOODEN){
			return [
				Item::get($this->getId(), 0, 1)
			];
		}else{
			return [];
		}
	}
}