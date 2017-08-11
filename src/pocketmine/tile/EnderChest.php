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
 * @author Tesseract Team
 * @link http://www.github.com/TesseractTeam/Tesseract
 * 
 *
 */

namespace pocketmine\tile;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class EnderChest extends Spawnable implements Nameable{


	public function getName() : string{
		return isset($this->namedtag->CustomName) ? $this->namedtag->CustomName->getValue() : "Ender Chest";
	}

	public function hasName() : bool{
		return isset($this->namedtag->CustomName);
	}

	public function setName(string $str){
		if($str === ""){
			unset($this->namedtag->CustomName);
			return;
		}

		$this->namedtag->CustomName = new StringTag("CustomName", $str);
	}
	public function addAdditionalSpawnData(CompoundTag $nbt){
		if($this->hasName()){
			$nbt->CustomName = $this->namedtag->CustomName;
		}
	}
}
