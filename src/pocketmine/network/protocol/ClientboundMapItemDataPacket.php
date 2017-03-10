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
	public $decorators;
	public $x;
	public $z;
	public $scale;
	public $col;
	public $row;
	public $xoffset;
	public $zoffset;
	public $data;

	public function decode(){

	}

	public function encode(){
		$this->reset();

		$this->putLong($this->mapid);
		$this->putByte($this->updatetype);
		$this->putDecorators($this->decorators);
		$this->putByte($this->x);
		$this->putByte($this->z);
		$this->putVarInt($this->col);
		$this->putVarInt($this->row);
		$this->putVarInt($this->xoffset);
		$this->putVarInt($this->zoffset);
		$this->putString($this->data);


	}

	public function putDecorators(){
		$decorators = $this->decorators;

		foreach($decorators as $decorator){var_dump($decorator);
			$this->putByte($decorator->rotation);
			$this->putByte($decorator->icon);
			$this->putByte($decorator->x);
			$this->putByte($decorator->z);
			$this->putString($decorator->label);
			$this->putLong($decorator->color);
		}
	}

}