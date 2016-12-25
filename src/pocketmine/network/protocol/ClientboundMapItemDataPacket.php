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


namespace pocketmine\network\protocol;

#include <rules/DataPacket.h>


class ClientboundMapItemDataPacket extends DataPacket{
	const NETWORK_ID = Info::CLIENTBOUND_MAP_ITEM_DATA_PACKET;

	public $mapid;
	public $updatetype;
	public $direction;
	public $x;
	public $z;
	public $col;
	public $row;
	public $xoffset;
	public $zoffset;
	public $data;
 	public $int6;
	public $int7;
	public $int8;
	public $int9;
	public $int10;
	public $int11;

	public function getName(){
		return "ClientboundMapItemDataPacket";
	}

	public function decode(){

	}

	public function encode(){
		$this->reset();

		$this->putLong($this->mapid);
		$this->putUnsignedVarInt($this->updatetype);
		$this->putUnsignedVarInt($this->direction);
		$this->putVarInt($this->x);
		$this->putByte($this->z);
		$this->putUnsignedVarInt($this->col);
		$this->putVarInt($this->row);
		$this->putByte($this->xoffset);
		$this->putByte($this->zoffset);
		$this->putString($this->data);
		$this->putUnsignedVarInt($this->int6);
		$this->putVarInt($this->int7);
		$this->putVarInt($this->int8);
		$this->putVarInt($this->int9);
		$this->putVarInt($this->int10);
		$this->putUnsignedVarInt($this->int11);

	}

}