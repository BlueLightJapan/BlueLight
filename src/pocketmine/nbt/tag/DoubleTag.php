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

class DoubleTag extends NamedTag{

	/**
	 * DoubleTag constructor.
	 *
	 * @param string $name
	 * @param float  $value
	 */
	public function __construct(string $name = "", float $value = 0.0){
		parent::__construct($name, $value);
	}

	public function getType(){
		return NBT::TAG_Double;
	}

	public function read(NBT $nbt, bool $network = false){
		$this->value = $nbt->getDouble();
	}

	public function write(NBT $nbt, bool $network = false){
		$nbt->putDouble($this->value);
	}

	/**
	 * @return float
	 */
	public function &getValue() : float{
		return parent::getValue();
	}

	/**
	 * @param float $value
	 *
	 * @throws \TypeError
	 */
	public function setValue($value){
		if(!is_float($value) and !is_int($value)){
			throw new \TypeError("DoubleTag value must be of type double, " . gettype($value) . " given");
		}
		parent::setValue((float) $value);
	}
}