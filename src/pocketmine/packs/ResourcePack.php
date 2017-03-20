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
use pocketmine\network\protocol\ResourcePackChunkDataPacket;

class ResourcePack {

	public $mustAccept = false;

	protected $packId;
	protected $version;
	protected $packSize;
	protected $packData;

	public $sha256;

	public function __construct(string $packId, string $version, string $path){

		$this->packId = $packId;
		$this->version = $version;
		$this->packData = file_get_contents($path);
		$this->sha256 = hash("sha256", $this->packData);
		$this->packSize = strlen($this->packData);

	}

	public function sendPacksInfo($player){
		$info = new ResourcePacksInfoPacket();
		$info->mustAccept = $this->mustAccept;
		$info->behaviourPackEntries[] = [];
		$info->resourcePackEntries[] = $this;
		$player->dataPacket($info);
	}

	public function sendPackDataInfo($player){
		$datainfo = new ResourcePackDataInfoPacket();
		$datainfo->packId = $this->getPackId();
		$datainfo->int1 = 1048576;
		$datainfo->int2 = 1;
		$datainfo->size = $this->getPackSize();
		$datainfo->sha256 = $this->sha256;
		$player->dataPacket($datainfo);
	}

	public function sendPackStack($player){
		$stack = new ResourcePackStackPacket();
		$stack->mustAccept = $this->mustAccept;
		$stack->behaviourPackEntries = [];
		$stack->resourcePackEntries[] = $this;

		$player->dataPacket($stack);
	}

	public function sendPackChunkData($player){
		$chunkdata = new ResourcePackChunkDataPacket();
		$chunkdata->packId = $this->getPackId();
		$chunkdata->int = 0;
		$chunkdata->long = 0;
		$chunkdata->length = $this->getPackSize();
		$chunkdata->payload = $this->getPackData();
		//var_dump($chunkdata);
		$player->dataPacket($chunkdata);
	}

	public function getPackId() : string {
		return $this->packId;
	}

	public function getVersion() : string {
		return $this->version;
	}

	public function getPackSize() : int {
		return $this->packSize;
	}

	public function getPackData() : string {
		return $this->packData;
	}
}