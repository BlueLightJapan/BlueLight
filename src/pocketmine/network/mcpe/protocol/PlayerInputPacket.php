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

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\mcpe\NetworkSession;

class PlayerInputPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::PLAYER_INPUT_PACKET;

	public $motionX;
	public $motionY;
	public $unknownBool1;
	public $unknownBool2;

	public function decode(){
		$this->motionX = $this->getLFloat();
		$this->motionY = $this->getLFloat();
		$this->unknownBool1 = $this->getBool();
		$this->unknownBool2 = $this->getBool();
	}

	public function encode(){
		$this->reset();
		$this->putLFloat($this->motionX);
		$this->putLFloat($this->motionY);
		$this->putBool($this->unknownBool1);
		$this->putBool($this->unknownBool2);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerInput($this);
	}

}
