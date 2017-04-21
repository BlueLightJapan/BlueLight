<?php

/*
 *
 *    _______                                _
 *   |__   __|                              | |
 *      | | ___  ___ ___  ___ _ __ __ _  ___| |_
 *      | |/ _ \/ __/ __|/ _ \  __/ _` |/ __| __|
 *      | |  __/\__ \__ \  __/ | | (_| | (__| |_
 *      |_|\___||___/___/\___|_|  \__,_|\___|\__|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Tessetact Team
 * @link http://www.github.com/TesseractTeam/Tesseract
 * 
 *
 */
 
namespace pocketmine\tile;

use pocketmine\inventory\BeaconInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class Beacon extends Spawnable implements Nameable, InventoryHolder {
	
	private $inventory;
	
	public function __construct(Level $level, CompoundTag $nbt) {
		if (!isset($nbt->primary)) {
			$nbt->primary = new IntTag("primary", 0);
		}
		if (!isset($nbt->secondary)) {
			$nbt->secondary = new IntTag("secondary", 0);
		}
		$this->inventory = new BeaconInventory($this);
		parent::__construct($level, $nbt);
	}
	
	public function saveNBT() {
		parent::saveNBT();
	}
	
	public function getSpawnCompound() {
		$c = new CompoundTag("", [
			new StringTag("id", Tile::BEACON),
			new ByteTag("isMovable", (bool)true),
			new IntTag("x", (int)$this->x),
			new IntTag("y", (int)$this->y),
			new IntTag("z", (int)$this->z)
		]);
		if ($this->hasName()) {
			$c->CustomName = $this->namedtag->CustomName;
		}
		return $c;
	}
	
	public function getName() : string{
		return $this->hasName() ? $this->namedtag->CustomName->getValue() : "Beacon";
	}
	
	public function hasName() {
		return isset($this->namedtag->CustomName);
	}
	
	public function setName($str) {
		if ($str === "") {
			unset($this->namedtag->CustomName);
			return;
		}
		$this->namedtag->CustomName = new StringTag("CustomName", $str);
	}
	
	public function getInventory() {
		return $this->inventory;
	}
}
