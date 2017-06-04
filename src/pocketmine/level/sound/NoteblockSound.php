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

namespace pocketmine\level\sound;

use pocketmine\item\Tool;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class NoteblockSound extends GenericSound{

	protected $instrument;
	protected $pitch;

	const INSTRUMENT_PIANO = 0;
	const INSTRUMENT_BASS_DRUM = 1;
	const INSTRUMENT_CLICK = 2;
	const INSTRUMENT_TABOUR = 3;
	const INSTRUMENT_BASS = 4;

	public function getInstrument(){
		$below = $this->getSide(Vector3::SIDE_DOWN);
		switch($below->getId()){
			case self::WOODEN_PLANK:
			case self::NOTEBLOCK:
			case self::CRAFTING_TABLE:
				return NoteblockSound::INSTRUMENT_BASS;
			case self::SAND:
			case self::SANDSTONE:
			case self::SOUL_SAND:
				return NoteblockSound::INSTRUMENT_TABOUR;
			case self::GLASS:
			case self::GLASS_PANEL:
			case self::GLOWSTONE_BLOCK:
				return NoteblockSound::INSTRUMENT_CLICK;
			case self::COAL_ORE:
			case self::DIAMOND_ORE:
			case self::EMERALD_ORE:
			case self::GLOWING_REDSTONE_ORE:
			case self::GOLD_ORE:
			case self::IRON_ORE:
			case self::LAPIS_ORE:
			case self::LIT_REDSTONE_ORE:
			case self::NETHER_QUARTZ_ORE:
			case self::REDSTONE_ORE:
				return NoteblockSound::INSTRUMENT_BASS_DRUM;
			default:
				return NoteblockSound::INSTRUMENT_PIANO;
		}
	}

	public function __construct(Vector3 $pos, $instrument = self::INSTRUMENT_PIANO, $pitch = 0){
		parent::__construct($pos, $instrument, $pitch);
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

		$pk2 = new LevelSoundEventPacket();
		$pk2->sound = LevelSoundEventPacket::SOUND_NOTE;
		$pk2->x = $this->x;
		$pk2->y = $this->y;
		$pk2->z = $this->z;
		$pk2->pitch = $this->pitch;

		return array($pk,$pk2);
	}
}
