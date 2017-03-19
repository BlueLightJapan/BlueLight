<?php

/*
 *   ____  _            _      _       _     _
 *  |  _ \| |          | |    (_)     | |   | |
 *  | |_) | |_   _  ___| |     _  __ _| |__ | |_
 *  |  _ <| | | | |/ _ \ |    | |/ _` | '_ \| __|
 *  | |_) | | |_| |  __/ |____| | (_| | | | | |_
 *  |____/|_|\__,_|\___|______|_|\__, |_| |_|\__|
 *                                __/ |
 *                               |___/
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/

namespace pocketmine\item;

use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\CompoundTag;

class FilledMap extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::FILLED_MAP, $meta, $count, "Filled Map");
	}

	public function getMaxStackSize() : int {
		return 1;
	}

	public function setMapId($id){

		$tag = new CompoundTag("", [
			"map_uuid" => new StringTag("map_uuid", $id),
		]);

		$this->setNamedTag($tag);
	}

	public function getMapId() : string {
		return $this->getNamedTagEntry("map_uuid");
	}
}

