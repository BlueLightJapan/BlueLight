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

class ShowCreditsPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::SHOW_CREDITS_PACKET;

	const STATUS_START_CREDITS = 0;
	const STATUS_END_CREDITS = 1;

	public $playerEid;
	public $status;

	public function decode(){
		$this->playerEid = $this->getEntityRuntimeId();
		$this->status = $this->getVarInt();
	}

	public function encode(){
		$this->reset();
		$this->putEntityRuntimeId($this->playerEid);
		$this->putVarInt($this->status);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleShowCredits($this);
	}
}