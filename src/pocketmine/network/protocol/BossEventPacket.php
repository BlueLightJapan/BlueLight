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


class BossEventPacket extends DataPacket{
	const NETWORK_ID = Info::BOSS_EVENT_PACKET;

	const TYPE_ADD = 0;
	const TYPE_UPDATE = 1;
	const TYPE_REMOVE = 2;

	public $eid;
	public $type;

	public $int2;
	public $int3;
	public $float1;
	public $float2;
	public $short;
	public $string1;
	public $string2;

	public function decode(){
	}

	public function encode(){
		$this->reset();

		$this->putVarInt($this->eid);
		$this->putUnsignedVarInt($this->type);

		$this->putString($this->string1);
 		$this->putLFloat($this->float1);
		$this->putLShort($this->short);
		$this->putUnsignedVarInt($this->int2);
		$this->putUnsignedVarInt($this->int3);
 		$this->putLFloat($this->float2);
		$this->putString($this->string2);

	}

}
