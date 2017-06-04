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
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

class GameRulesChangedPacket extends DataPacket{
	const NETWORK_ID = Info::GAME_RULES_CHANGED_PACKET;

	public $rules = [];

	public function decode(){

		$count = $this->getVarInt();
		for ($i = 0; $i < $count; $i++){
			$this->rules[$i] = [];
			$this->rules[$i]["NAME"] = $this->getString();
			$this->rules[$i]["BOOL1"] = $this->getBool();
			$this->rules[$i]["BOOL2"] = $this->getBool();
		}
	}

	public function encode(){
		$this->reset();

		$this->putVarInt(count($this->rules));
		foreach($this->rules as $rule){
			$this->putString($rule["NAME"]);
			$this->putBool($rule["BOOL1"]);
			$this->putBool($rule["BOOL2"]);
		}
	}

}
