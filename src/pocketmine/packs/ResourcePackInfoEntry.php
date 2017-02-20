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

namespace pocketmine\packs;

class ResourcePackInfoEntry{
	protected $packId; //UUID
	protected $version;
	protected $packSize;
	protected $packData;

	public function __construct(string $packId, string $version ,$path){
		$this->packId = $packId;
		$this->version = $version;
		$this->path = $path;
		$this->packSize = filesize($path);

		$handle = fopen($path,"rb");
		$this->packData = fread($handle, $this->packSize);

	}

	public function getPackId(){
		return $this->packId;
	}

	public function getVersion(){
		return $this->version;
	}

	public function getPackSize(){
		return $this->packSize;
	}

	public function getPackData(){
		return $this->packData;
	}

}