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

namespace pocketmine\level\map;

class Decorator{

	/**
	 * @param int    $rotation
	 * @param int    $icon
	 * @param int    $x
	 * @param int    $z
	 * @param string $label
	 * @param long    $color
	 */
	public function __construct($rotation, $icon, $x, $z, $label, $color){
		$this->rotation = $rotation;
		$this->icon = $icon;
		$this->x = $x;
		$this->z = $z;
		$this->label = $label;
		$this->color = $color;

	}
}
