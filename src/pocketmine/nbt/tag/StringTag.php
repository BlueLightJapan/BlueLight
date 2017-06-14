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

declare(strict_types=1);

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;

#include <rules/NBT.h>

class StringTag extends NamedTag{

	/**
	 * StringTag constructor.
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function __construct(string $name = "", string $value = ""){
		parent::__construct($name, $value);
	}

	public function getType(){
		return NBT::TAG_String;
	}

	public function read(NBT $nbt, bool $network = false){
		$this->value = $nbt->getString($network);
	}

	public function write(NBT $nbt, bool $network = false){
		$nbt->putString($this->value, $network);
	}

	/**
	 * @return string
	 */
	public function &getValue() : string{
		return parent::getValue();
	}

	/**
	 * @param string $value
	 *
	 * @throws \TypeError
	 */
	public function setValue($value){
		if(!is_string($value)){
			throw new \TypeError("ShortTag value must be of type int, " . gettype($value) . " given");
		}
		parent::setValue($value);
	}
}