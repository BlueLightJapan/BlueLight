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

class CompoundTag extends NamedTag implements \ArrayAccess{

	/**
	 * CompoundTag constructor.
	 *
	 * @param string     $name
	 * @param NamedTag[] $value
	 */
	public function __construct(string $name = "", array $value = []){
		parent::__construct($name, $value);
	}

	public function getCount(){
		$count = 0;
		foreach($this as $tag){
			if($tag instanceof Tag){
				++$count;
			}
		}

		return $count;
	}

	/**
	 * @param NamedTag[] $value
	 *
	 * @throws \TypeError
	 */
	public function setValue($value){
		if(is_array($value)){
			foreach($value as $name => $tag){
				if($tag instanceof NamedTag){
					$this->{$tag->getName()} = $tag;
				}else{
					throw new \TypeError("CompoundTag members must be NamedTags, got " . gettype($tag) . " in given array");
				}
			}
		}else{
			throw new \TypeError("CompoundTag value must be NamedTag[], " . gettype($value) . " given");
		}
	}

	public function offsetExists($offset){
		return isset($this->{$offset}) and $this->{$offset} instanceof Tag;
	}

	public function offsetGet($offset){
		if(isset($this->{$offset}) and $this->{$offset} instanceof Tag){
			if($this->{$offset} instanceof \ArrayAccess){
				return $this->{$offset};
			}else{
				return $this->{$offset}->getValue();
			}
		}

		assert(false, "Offset $offset not found");

		return null;
	}

	public function offsetSet($offset, $value){
		if($value instanceof Tag){
			$this->{$offset} = $value;
		}elseif(isset($this->{$offset}) and $this->{$offset} instanceof Tag){
			$this->{$offset}->setValue($value);
		}
	}

	public function offsetUnset($offset){
		unset($this->{$offset});
	}

	public function getType(){
		return NBT::TAG_Compound;
	}

	public function read(NBT $nbt, bool $network = false){
		$this->value = [];
		do{
			$tag = $nbt->readTag($network);
			if($tag instanceof NamedTag and $tag->getName() !== ""){
				$this->{$tag->getName()} = $tag;
			}
		}while(!($tag instanceof EndTag) and !$nbt->feof());
	}

	public function write(NBT $nbt, bool $network = false){
		foreach($this as $tag){
			if($tag instanceof Tag and !($tag instanceof EndTag)){
				$nbt->writeTag($tag, $network);
			}
		}
		$nbt->writeTag(new EndTag, $network);
	}

	public function __toString(){
		$str = get_class($this) . "{\n";
		foreach($this as $tag){
			if($tag instanceof Tag){
				$str .= get_class($tag) . ":" . $tag->__toString() . "\n";
			}
		}
		return $str . "}";
	}

	public function __clone(){
		foreach($this as $key => $tag){
			if($tag instanceof Tag){
				$this->{$key} = clone $tag;
			}
		}
	}
}