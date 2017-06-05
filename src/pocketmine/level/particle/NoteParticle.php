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
 * @link http://bluelight.cf
 * 
*/

namespace pocketmine\level\particle;

use pocketmine\math\Vector3;
use pocketmine\network\protocol\BlockEventPacket;

class NoteParticle extends GenericParticle{
	protected $instrument;
	protected $pitch;

	public function __construct(Vector3 $pos, $instrument, $pitch){
		parent::__construct($pos, Particle::TYPE_NOTE, $this->getColorByPitch($pitch));
		$this->instrument = $instrument;
		$this->pitch = $pitch;
	}

	public function encode(){
		$pk = new BlockEventPacket();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->case1 = $this->instrument;
		$pk->case2 = $this->pitch;

		return $pk;
	}

	public function getColorByPitch($pitch){
		return $pitch * 100;
		$a = 255;
		switch($pitch){
			case 0:
				$r = 80;
				$g = 213;
				$b = 0;
			break;
			case 1:
				$r = 138;
				$g = 213;
				$b = 0;
			break;
			case 2:
				$r = 213;
				$g = 213;
				$b = 0;
			break;
			case 3:
				$r = 213;
				$g = 138;
				$b = 0;
			break;
			case 4:
				$r = 213;
				$g = 80;
				$b = 0;
			break;
			case 5:
				$r = 213;
				$g = 37;
				$b = 0;
			break;
			case 6:
				$r = 204;
				$g = 0;
				$b = 0;
			break;
			case 7:
				$r = 213;
				$g = 0;
				$b = 37;
			break;
			case 8:
				$r = 213;
				$g = 0;
				$b = 80;
			break;
			case 9:
				$r = 213;
				$g = 0;
				$b = 133;
			break;
			case 10:
				$r = 213;
				$g = 0;
				$b = 213;
			break;
			case 11:
				$r = 138;
				$g = 0;
				$b = 213;
			break;
			case 12:
				$r = 80;
				$g = 0;
				$b = 213;
			break;
			case 13:
				$r = 48;
				$g = 0;
				$b = 254;
			break;
			case 14:
				$r = 0;
				$g = 0;
				$b = 245;
			break;
			case 15:
				$r = 0;
				$g = 48;
				$b = 254;
			break;
			case 16:
				$r = 0;
				$g = 96;
				$b = 255;
			break;
			case 17:
				$r = 0;
				$g = 147;
				$b = 234;
			break;
			case 18:
				$r = 0;
				$g = 213;
				$b = 213;
			break;
			case 19:
				$r = 0;
				$g = 213;
				$b = 138;
			break;
			case 20:
				$r = 0;
				$g = 213;
				$b = 80;
			break;
			case 21:
				$r = 0;
				$g = 213;
				$b = 37;
			break;
			case 22:
				$r = 0;
				$g = 204;
				$b = 0;
			break;
			case 23:
				$r = 41;
				$g = 234;
				$b = 0;
			break;
			case 24:
				$r = 80;
				$g = 213;
				$b = 0;
			break;
		}
		return (($a & 0xff) << 24) | (($r & 0xff) << 16) | (($g & 0xff) << 8) | ($b & 0xff);
	}
}
