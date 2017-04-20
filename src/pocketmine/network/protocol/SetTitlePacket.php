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


namespace pocketmine\network\protocol;

#include <rules/DataPacket.h>

class SetTitlePacket extends DataPacket{
	const NETWORK_ID = Info::SET_TITLE_PACKET;

	const TYPE_CLEAR_TITLE = 0;
	const TYPE_RESET_TITLE = 1;
	const TYPE_SET_TITLE = 2;
	const TYPE_SET_SUBTITLE = 3;
	const TYPE_SET_ACTIONBAR_MESSAGE = 4;
	const TYPE_SET_ANIMATION_TIMES = 5;

	public $type;
	public $text;
	public $fadeInTime;
	public $stayTime;
	public $fadeOutTime;

	public function decode(){
		$this->type = $this->getVarInt();
		$this->text = $this->getString();
		$this->fadeInTime = $this->getVarInt();
		$this->stayTime = $this->getVarInt();
		$this->fadeOutTime = $this->getVarInt();
	}

	public function encode(){
		$this->reset();
		$this->putVarInt($this->type);
		$this->putString($this->text);
		$this->putVarInt($this->fadeInTime);
		$this->putVarInt($this->stayTime);
		$this->putVarInt($this->fadeOutTime);
	}
        /**
	 * @return PacketName|string
         */
	public function getName(){
		return "SetTitlePacket";
	}

}
