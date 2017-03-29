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

class PlaySoundPacket extends DataPacket{
	const NETWORK_ID = Info::PLAY_SOUND_PACKET;

	public $sound;
	public $x;
	public $y;
	public $z;
	public $volume;
	public $float2;

	public function decode(){
		$this->sound = $this->getString();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->volume = $this->getLFloat();
		$this->float2 = $this->getLFloat();
	}

	public function encode(){
		$this->reset();
		$this->putString($this->sound);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putLFloat($this->volume);
		$this->putLFloat($this->float2);
	}

}