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

class BossEventPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::BOSS_EVENT_PACKET;

	/* S2C: Shows the boss-bar to the player. */
	const TYPE_SHOW = 0;
	/* C2S: Registers a player to a boss fight. */
	const TYPE_REGISTER_PLAYER = 1;
	/* S2C: Removes the boss-bar from the client. */
	const TYPE_HIDE = 2;
	/* C2S: Unregisters a player from a boss fight. */
	const TYPE_UNREGISTER_PLAYER = 3;
	/* S2C: Appears not to be implemented. Currently bar percentage only appears to change in response to the target entity's health. */
	const TYPE_HEALTH_PERCENT = 4;
	/* S2C: Also appears to not be implemented. Title client-side sticks as the target entity's nametag, or their entity type name if not set. */
	const TYPE_TITLE = 5;
	/* S2C: Not sure on this. Includes color and overlay fields, plus an unknown short. TODO: check this */
	const TYPE_UNKNOWN_6 = 6;
	/* S2C: Not implemented :( Intended to alter bar appearance, but these currently produce no effect on client-side whatsoever. */
	const TYPE_TEXTURE = 7;

	public $bossEid;
	public $eventType;

	/** @var int (long) */
	public $playerEid;
	/** @var float */
	public $healthPercent;
	/** @var string */
	public $title;
	/** @var int */
	public $unknownShort;
	/** @var int */
	public $color;
	/** @var int */
	public $overlay;

	public function decode(){
		$this->bossEid = $this->getEntityUniqueId();
		$this->eventType = $this->getUnsignedVarInt();
		switch($this->eventType){
			case self::TYPE_REGISTER_PLAYER:
			case self::TYPE_UNREGISTER_PLAYER:
				$this->playerEid = $this->getEntityUniqueId();
				break;
			case self::TYPE_SHOW:
				$this->title = $this->getString();
				$this->healthPercent = $this->getLFloat();
			case self::TYPE_UNKNOWN_6:
				$this->unknownShort = $this->getLShort();
			case self::TYPE_TEXTURE:
				$this->color = $this->getUnsignedVarInt();
				$this->overlay = $this->getUnsignedVarInt();
				break;
			case self::TYPE_HEALTH_PERCENT:
				$this->healthPercent = $this->getLFloat();
				break;
			case self::TYPE_TITLE:
				$this->title = $this->getString();
			default:
				break;
		}
	}

	public function encode(){
		$this->reset();
		$this->putEntityUniqueId($this->bossEid);
		$this->putUnsignedVarInt($this->eventType);
		switch($this->eventType){
			case self::TYPE_REGISTER_PLAYER:
			case self::TYPE_UNREGISTER_PLAYER:
				$this->putEntityUniqueId($this->playerEid);
				break;
			case self::TYPE_SHOW:
				$this->putString($this->title);
				$this->putLFloat($this->healthPercent);
			case self::TYPE_UNKNOWN_6:
				$this->putLShort($this->unknownShort);
			case self::TYPE_TEXTURE:
				$this->putUnsignedVarInt($this->color);
				$this->putUnsignedVarInt($this->overlay);
				break;
			case self::TYPE_HEALTH_PERCENT:
				$this->putLFloat($this->healthPercent);
				break;
			case self::TYPE_TITLE:
				$this->putString($this->title);
				break;
			default:
				break;
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleBossEvent($this);
	}

}