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

namespace pocketmine\network\protocol;

use pocketmine\utils\Binary;

class ShowCreditsPacket extends DataPacket{
	const NETWORK_ID = Info::SHOW_CREDITS_PACKET;

	const TYPE_ADD = 0;
	const TYPE_REMOVE = 1;

	public $eid;
	public $type;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putEntityId($this->eid);
		$this->putVarInt($this->type);
	}
}
