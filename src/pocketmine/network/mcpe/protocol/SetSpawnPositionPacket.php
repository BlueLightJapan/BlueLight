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

class SetSpawnPositionPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::SET_SPAWN_POSITION_PACKET;

	const TYPE_PLAYER_SPAWN = 0;
	const TYPE_WORLD_SPAWN = 1;

	public $spawnType;
	public $x;
	public $y;
	public $z;
	public $spawnForced;

	public function decode(){
		$this->spawnType = $this->getVarInt();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->spawnForced = $this->getBool();
	}

	public function encode(){
		$this->reset();
		$this->putVarInt($this->spawnType);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putBool($this->spawnForced);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetSpawnPosition($this);
	}

}
