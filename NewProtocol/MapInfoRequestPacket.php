<?php

/*
 *			    ________
 *                         |____   /
 *   _______ _                  / /  __  __ _
 *  |__   __(_) _____ ____     / /  |  \/  (_)_ __   ___ 
 *     | |  | ||     \  __ \  / /   | |\/| | | '_ \ / _ \
 *     | |  | || | | \ /__\ |/ /___ | |  | | | | | |  __/
 *     |_|  |_||_|_|_|\____/_______||_|  |_|_|_| |_|\___|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TimeZMine Team
 * @link http://www.kameta.tokyo/
 * 
 *
*/

namespace pocketmine\network\protocol;


class MapInfoRequestPacket extends DataPacket{
	const NETWORK_ID = Info::MAP_INFO_REQUEST_PACKET;
//	const NETWORK_ID = 0x42;

	public $mapid;

	public function decode(){
		$this->mapid = $this->getLong();
	}

	public function encode(){
	}
}
