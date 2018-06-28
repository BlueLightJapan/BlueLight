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

class PlayerActionPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::PLAYER_ACTION_PACKET;

	const ACTION_START_BREAK = 0;
	const ACTION_ABORT_BREAK = 1;
	const ACTION_STOP_BREAK = 2;
	const ACTION_GET_UPDATED_BLOCK = 3;
	const ACTION_DROP_ITEM = 4;
	const ACTION_START_SLEEPING = 5;
	const ACTION_STOP_SLEEPING = 6;
	const ACTION_RESPAWN = 7;
	const ACTION_JUMP = 8;
	const ACTION_START_SPRINT = 9;
	const ACTION_STOP_SPRINT = 10;
	const ACTION_START_SNEAK = 11;
	const ACTION_STOP_SNEAK = 12;
	const ACTION_DIMENSION_CHANGE_REQUEST = 13; //sent when dying in different dimension
	const ACTION_DIMENSION_CHANGE_ACK = 14; //sent when spawning in a different dimension to tell the server we spawned
	const ACTION_START_GLIDE = 15;
	const ACTION_STOP_GLIDE = 16;
	const ACTION_BUILD_DENIED = 17;
	const ACTION_CONTINUE_BREAK = 18;

	const ACTION_SET_ENCHANTMENT_SEED = 20;
	const ACTION_START_SWIMMING = 21;
	const ACTION_STOP_SWIMMING = 22;
	const ACTION_START_SPIN_ATTACK = 23;
	const ACTION_STOP_SPIN_ATTACK = 24;
	/** @var int */
	public $entityRuntimeId;
	/** @var int */
	public $action;
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $face;

	protected function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->action = $this->getVarInt();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->face = $this->getVarInt();
	}

	protected function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVarInt($this->action);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putVarInt($this->face);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerAction($this);
	}

}
