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

#include <rules/DataPacket.h>

class ResourcePackChunkRequestPacket extends DataPacket{
	const NETWORK_ID = Info::RESOURCE_PACK_CHUNK_REQUEST_PACKET;
	public $packid;
	public $int;

	public function decode(){
		$this->packid = $this->getString();
		$this->int = $this->getLInt();
	}

	public function encode(){
	}
}