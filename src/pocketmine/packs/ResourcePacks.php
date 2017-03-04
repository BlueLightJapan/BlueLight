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

namespace pocketmine\packs;

use pocketmine\network\protocol\ResourcePacksInfoPacket;
use pocketmine\network\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\protocol\ResourcePackStackPacket;
use pocketmine\network\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\protocol\ResourcePackChunkDataPacket;

class ResourcePacks{

	public $mustAccept = false;

	/** @var ResourcePackInfoEntry */
	public $behaviourPackEntries = [];
	/** @var ResourcePackInfoEntry */
	public $resourcePackEntries = [];
	/** @var ResourcePackInfoEntry */
	public $packEntries = [];

	public function __construct(){
	}

	public function sendPacksInfo($player){
		$info = new ResourcePacksInfoPacket();
		$info->mustAccept = $this->mustAccept;
		$info->behaviourPackEntries = $this->behaviourPackEntries;
		$info->resourcePackEntries = $this->resourcePackEntries;

		$player->dataPacket($info);
	}

	public function sendPackDataInfo($player, $packid){
		$datainfo = new ReourcePackDataInfoPacket();
		$datainfo->packid = $packid;
		$datainfo->int1 = 0;
		$datainfo->int2 = 1;
		$datainfo->size = $packEntries[$packid]->getPackSize();
		$datainfo->pack = $packEntries[$packid]->getPackData();

		$player->dataPacket($datainfo);
	}

	public function sendPackStack($player){
		$stack = new ResourcePackStackPacket();
		$stack->mustAccept = $this->mustAccept;
		$stack->behaviourPackEntries = $this->behaviourPackEntries;
		$stack->resourcePackEntries = $this->resourcePackEntries;

		$player->dataPacket($stack);
	}

	public function sendPackChunkData($player, $packid){
		$chunkdata = new ReourcePackDataChunkPacket();
		$chunkdata->packid = $packid;
		$chunkdata->int1 = 0;
		$chunkdata->size = $packEntries[$packid]->getPackSize();
		$chunkdata->int2 = 1;
		//$chunkdata->payload = $packEntries[$packid]->getPackData();
		$chunkdata->byte = 0;

		$player->dataPacket($chunkdata);
	}
}