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

class AddPaintingPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::ADD_PAINTING_PACKET;

	public $entityUniqueId = null; //TODO
	public $entityRuntimeId;
	public $x;
	public $y;
	public $z;
	public $direction;
	public $title;

	public function decode(){
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->direction = $this->getVarInt();
		$this->title = $this->getString();
	}

	public function encode(){
		$this->reset();
		$this->putEntityUniqueId($this->entityUniqueId ?? $this->entityRuntimeId);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putVarInt($this->direction);
		$this->putString($this->title);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddPainting($this);
	}

}
