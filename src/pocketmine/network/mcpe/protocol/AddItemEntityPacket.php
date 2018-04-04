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

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;

class AddItemEntityPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::ADD_ITEM_ENTITY_PACKET;

	/** @var int|null */
	public $entityUniqueId = null; //TODO
	/** @var int */
	public $entityRuntimeId;
	/** @var Item */
	public $item;
	/** @var Vector3 */
	public $position;
	/** @var Vector3|null */
	public $motion;
	/** @var array */
	public $metadata = [];
	/** @var bool */
	public $isFromFishing = false;
	
	protected function decodePayload(){
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->item = $this->getSlot();
		$this->position = $this->getVector3Obj();
		$this->motion = $this->getVector3Obj();
		$this->metadata = $this->getEntityMetadata();
		$this->isFromFishing = $this->getBool();
	}

	protected function encodePayload(){
		if(isset($this->x)) $this->position = new Vector3($this->x, $this->y, $this->z);
		$this->putEntityUniqueId($this->entityUniqueId ?? $this->entityRuntimeId);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putSlot($this->item);
		$this->putVector3Obj($this->position);
		$this->putVector3ObjNullable($this->motion);
		$this->putEntityMetadata($this->metadata);
		$this->putBool($this->isFromFishing);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddItemEntity($this);
	}

}
