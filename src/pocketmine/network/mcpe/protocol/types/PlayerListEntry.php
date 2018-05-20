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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\entity\Skin;
use pocketmine\utils\UUID;

class PlayerListEntry{
	/** @var UUID */
	public $uuid;
	/** @var int */
	public $entityUniqueId;
	/** @var string */
	public $username;
	/** @var string */
	public $thirdPartyName = "";
	/** @var int */
	public $platform = 0;
	/** @var string */
	public $platformChatId = "";
	/** @var Skin */
	public $skin;
	/** @var string */
	public $xboxUserId; //TODO
	public static function createRemovalEntry(UUID $uuid) : PlayerListEntry{
		$entry = new PlayerListEntry();
		$entry->uuid = $uuid;
		return $entry;
	}
public static function createAdditionEntry(
		UUID $uuid,
		int $entityUniqueId,
		string $username,
		string $skinId,
		string $skinData,
		string $thirdPartyName,
		int $platform,
		string $capeData = "",
		string $geometryModel = "",
		string $geometryData = "",
		string $xboxUserId = ""
	) : PlayerListEntry{
		$entry = new PlayerListEntry();
		$entry->uuid = $uuid;
		$entry->entityUniqueId = $entityUniqueId;
		$entry->username = $username;
		$entry->skinId = $skinId;
		$entry->skinData = $skinData;
		$entry->capeData = $capeData;
		$entry->geometryModel = $geometryModel;
		$entry->geometryData = $geometryData;
		$entry->xboxUserId = $xboxUserId;
		$entry->thirdPartyName = $thirdPartyName;
		$entry->platform = $platform;
		return $entry;
	}

}