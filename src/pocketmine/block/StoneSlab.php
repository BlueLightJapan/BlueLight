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

class StoneSlab extends WoodenSlab{
	const STONE = 0;
	const SANDSTONE = 1;
	const WOODEN = 2;
	const COBBLESTONE = 3;
	const BRICK = 4;
	const STONE_BRICK = 5;
	const QUARTZ = 6;
	const NETHER_BRICK = 7;

	protected $id = self::STONE_SLAB;

	protected $doubleId = self::DOUBLE_STONE_SLAB;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 2;
	}

	public function getName(){
		static $names = [
			self::STONE => "Stone",
			self::SANDSTONE => "Sandstone",
			self::WOODEN => "Wooden",
			self::COBBLESTONE => "Cobblestone",
			self::BRICK => "Brick",
			self::STONE_BRICK => "Stone Brick",
			self::QUARTZ => "Quartz",
			self::NETHER_BRICK => "Nether Brick",
		];
		return (($this->meta & 0x08) > 0 ? "Upper " : "") . $names[$this->meta & 0x07] . " Slab";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= TieredTool::TIER_WOODEN){
			return [
				Item::get($this->getId(), $this->getDamage() & 0x07, 1)
			];
		}else{
			return [];
		}
	}
}