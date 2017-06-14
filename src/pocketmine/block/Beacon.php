<?php

/*
 *
 *   _____       _             _   _____  ______      ____       _        
 *  / ____|     (_)           | | |  __ \|  ____|    |  _ \     | |       
 * | (___  _ __  _  __ _  ___ | |_| |__) | |__ ______| |_) | ___| |_ __ _ 
 *  \___ \| '_ \| |/ _` |/ _ \| __|  ___/|  __|______|  _ < / _ \ __/ _` |
 *  ____) | |_) | | (_| | (_) | |_| |    | |____     | |_) |  __/ || (_| |
 * |_____/| .__/|_|\__, |\___/ \__|_|    |______|    |____/ \___|\__\__,_|
 *        | |       __/ |                                                 
 *        |_|      |___/      
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author SpigotPE-Beta Team
 * @link http://github.com/SpigotPE-Beta
 *
 *
*/

namespace pocketmine\block;

use pocketmine\inventory\BeaconInventory;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\tile\Beacon as TileBeacon;

class Beacon extends Solid {

	protected $id = self::BEACON;

	public function __construct($meta = 0) {
		$this->meta = $meta;
	}

	public function getLightLevel() {
		return 15;
	}

	public function getResistance() {
		return 15;
	}

	public function getHardness() {
		return 3;
	}

	public function canBeActivated() {
		return true;
	}

	public function getName() {
		return "Beacon";
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null) {
		$this->getLevel()->setBlock($this, $this, true, true);
		$nbt = new CompoundTag("", [
			new StringTag("id", Tile::BEACON),
			new ByteTag("isMovable", (bool) false),
			new IntTag("primary", 0),
			new IntTag("secondary", 0),
			new IntTag("x", $block->x),
			new IntTag("y", $block->y),
			new IntTag("z", $block->z)
		]);
		Tile::createTile(Tile::BEACON, $this->getLevel(), $nbt);
		return true;
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$top = $this->getSide(1);
			if($top->isTransparent() !== true){
				return true;
			}

			$t = $this->getLevel()->getTile($this);
			$beacon = null;
			if($t instanceof TileBeacon){
				$beacon = $t;
			}else{
				$nbt = new CompoundTag("", [
					new StringTag("id", Tile::BEACON),
					new ByteTag("isMovable", (bool) false),
					new IntTag("primary", 0),
					new IntTag("secondary", 0),
					new IntTag("x", $this->x),
					new IntTag("y", $this->y),
					new IntTag("z", $this->z)
				]);
				Tile::createTile(Tile::BEACON, $this->getLevel(), $nbt);
			}

			if($beacon instanceof TileBeacon){
				$player->addWindow($beacon->getInventory());
			}
		}

		return true;
	}
}
