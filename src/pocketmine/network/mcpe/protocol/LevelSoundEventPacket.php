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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;

class LevelSoundEventPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::LEVEL_SOUND_EVENT_PACKET;

	const SOUND_ITEM_USE_ON = 0;
	const SOUND_HIT = 1;
	const SOUND_STEP = 2;
	const SOUND_FLY = 3;
	const SOUND_JUMP = 4;
	const SOUND_BREAK = 5;
	const SOUND_PLACE = 6;
	const SOUND_HEAVY_STEP = 7;
	const SOUND_GALLOP = 8;
	const SOUND_FALL = 9;
	const SOUND_AMBIENT = 10;
	const SOUND_AMBIENT_BABY = 11;
	const SOUND_AMBIENT_IN_WATER = 12;
	const SOUND_BREATHE = 13;
	const SOUND_DEATH = 14;
	const SOUND_DEATH_IN_WATER = 15;
	const SOUND_DEATH_TO_ZOMBIE = 16;
	const SOUND_HURT = 17;
	const SOUND_HURT_IN_WATER = 18;
	const SOUND_MAD = 19;
	const SOUND_BOOST = 20;
	const SOUND_BOW = 21;
	const SOUND_SQUISH_BIG = 22;
	const SOUND_SQUISH_SMALL = 23;
	const SOUND_FALL_BIG = 24;
	const SOUND_FALL_SMALL = 25;
	const SOUND_SPLASH = 26;
	const SOUND_FIZZ = 27;
	const SOUND_FLAP = 28;
	const SOUND_SWIM = 29;
	const SOUND_DRINK = 30;
	const SOUND_EAT = 31;
	const SOUND_TAKEOFF = 32;
	const SOUND_SHAKE = 33;
	const SOUND_PLOP = 34;
	const SOUND_LAND = 35;
	const SOUND_SADDLE = 36;
	const SOUND_ARMOR = 37;
	const SOUND_MOB_ARMOR_STAND_PLACE = 38;
	const SOUND_ADD_CHEST = 39;
	const SOUND_THROW = 40;
	const SOUND_ATTACK = 41;
	const SOUND_ATTACK_NODAMAGE = 42;
	const SOUND_ATTACK_STRONG = 43;
	const SOUND_WARN = 44;
	const SOUND_SHEAR = 45;
	const SOUND_MILK = 46;
	const SOUND_THUNDER = 47;
	const SOUND_EXPLODE = 48;
	const SOUND_FIRE = 49;
	const SOUND_IGNITE = 50;
	const SOUND_FUSE = 51;
	const SOUND_STARE = 52;
	const SOUND_SPAWN = 53;
	const SOUND_SHOOT = 54;
	const SOUND_BREAK_BLOCK = 55;
	const SOUND_LAUNCH = 56;
	const SOUND_BLAST = 57;
	const SOUND_LARGE_BLAST = 58;
	const SOUND_TWINKLE = 59;
	const SOUND_REMEDY = 60;
	const SOUND_UNFECT = 61;
	const SOUND_LEVELUP = 62;
	const SOUND_BOW_HIT = 63;
	const SOUND_BULLET_HIT = 64;
	const SOUND_EXTINGUISH_FIRE = 65;
	const SOUND_ITEM_FIZZ = 66;
	const SOUND_CHEST_OPEN = 67;
	const SOUND_CHEST_CLOSED = 68;
	const SOUND_SHULKERBOX_OPEN = 69;
	const SOUND_SHULKERBOX_CLOSED = 70;
	const SOUND_ENDERCHEST_OPEN = 71;
	const SOUND_ENDERCHEST_CLOSED = 72;
	const SOUND_POWER_ON = 73;
	const SOUND_POWER_OFF = 74;
	const SOUND_ATTACH = 75;
	const SOUND_DETACH = 76;
	const SOUND_DENY = 77;
	const SOUND_TRIPOD = 78;
	const SOUND_POP = 79;
	const SOUND_DROP_SLOT = 80;
	const SOUND_NOTE = 81;
	const SOUND_THORNS = 82;
	const SOUND_PISTON_IN = 83;
	const SOUND_PISTON_OUT = 84;
	const SOUND_PORTAL = 85;
	const SOUND_WATER = 86;
	const SOUND_LAVA_POP = 87;
	const SOUND_LAVA = 88;
	const SOUND_BURP = 89;
	const SOUND_BUCKET_FILL_WATER = 90;
	const SOUND_BUCKET_FILL_LAVA = 91;
	const SOUND_BUCKET_EMPTY_WATER = 92;
	const SOUND_BUCKET_EMPTY_LAVA = 93;
	const SOUND_ARMOR_EQUIP_CHAIN = 94;
	const SOUND_ARMOR_EQUIP_DIAMOND = 95;
	const SOUND_ARMOR_EQUIP_GENERIC = 96;
	const SOUND_ARMOR_EQUIP_GOLD = 97;
	const SOUND_ARMOR_EQUIP_IRON = 98;
	const SOUND_ARMOR_EQUIP_LEATHER = 99;
	const SOUND_ARMOR_EQUIP_ELYTRA = 100;
	const SOUND_RECORD_13 = 101;
	const SOUND_RECORD_CAT = 102;
	const SOUND_RECORD_BLOCKS = 103;
	const SOUND_RECORD_CHIRP = 104;
	const SOUND_RECORD_FAR = 105;
	const SOUND_RECORD_MALL = 106;
	const SOUND_RECORD_MELLOHI = 107;
	const SOUND_RECORD_STAL = 108;
	const SOUND_RECORD_STRAD = 109;
	const SOUND_RECORD_WARD = 110;
	const SOUND_RECORD_11 = 111;
	const SOUND_RECORD_WAIT = 112;
	const SOUND_GUARDIAN_FLOP = 114;
	const SOUND_ELDERGUARDIAN_CURSE = 115;
	const SOUND_MOB_WARNING = 116;
	const SOUND_MOB_WARNING_BABY = 117;
	const SOUND_TELEPORT = 118;
	const SOUND_SHULKER_OPEN = 119;
	const SOUND_SHULKER_CLOSE = 120;
	const SOUND_HAGGLE = 121;
	const SOUND_HAGGLE_YES = 122;
	const SOUND_HAGGLE_NO = 123;
	const SOUND_HAGGLE_IDLE = 124;
	const SOUND_CHORUSGROW = 125;
	const SOUND_CHORUSDEATH = 126;
	const SOUND_GLASS = 127;
	const SOUND_POTION_BREWED = 128;
	const SOUND_CAST_SPELL = 129;
	const SOUND_PREPARE_ATTACK = 130;
	const SOUND_PREPARE_SUMMON = 131;
	const SOUND_PREPARE_WOLOLO = 132;
	const SOUND_FANG = 133;
	const SOUND_CHARGE = 134;
	const SOUND_CAMERA_TAKE_PICTURE = 135;
	const SOUND_LEASHKNOT_PLACE = 136;
	const SOUND_LEASHKNOT_BREAK = 137;
	const SOUND_GROWL = 138;
	const SOUND_WHINE = 139;
	const SOUND_PANT = 140;
	const SOUND_PURR = 141;
	const SOUND_PURREOW = 142;
	const SOUND_DEATH_MIN_VOLUME = 143;
	const SOUND_DEATH_MID_VOLUME = 144;
	const SOUND_IMITATE_BLAZE = 145;
	const SOUND_IMITATE_CAVE_SPIDER = 146;
	const SOUND_IMITATE_CREEPER = 147;
	const SOUND_IMITATE_ELDER_GUARDIAN = 148;
	const SOUND_IMITATE_ENDER_DRAGON = 149;
	const SOUND_IMITATE_ENDERMAN = 150;
	const SOUND_IMITATE_EVOCATION_ILLAGER = 152;
	const SOUND_IMITATE_GHAST = 153;
	const SOUND_IMITATE_HUSK = 154;
	const SOUND_IMITATE_ILLUSION_ILLAGER = 155;
	const SOUND_IMITATE_MAGMA_CUBE = 156;
	const SOUND_IMITATE_POLAR_BEAR = 157;
	const SOUND_IMITATE_SHULKER = 158;
	const SOUND_IMITATE_SILVERFISH = 159;
	const SOUND_IMITATE_SKELETON = 160;
	const SOUND_IMITATE_SLIME = 161;
	const SOUND_IMITATE_SPIDER = 162;
	const SOUND_IMITATE_STRAY = 163;
	const SOUND_IMITATE_VEX = 164;
	const SOUND_IMITATE_VINDICATION_ILLAGER = 165;
	const SOUND_IMITATE_WITCH = 166;
	const SOUND_IMITATE_WITHER = 167;
	const SOUND_IMITATE_WITHER_SKELETON = 168;
	const SOUND_IMITATE_WOLF = 169;
	const SOUND_IMITATE_ZOMBIE = 170;
	const SOUND_IMITATE_ZOMBIE_PIGMAN = 171;
	const SOUND_IMITATE_ZOMBIE_VILLAGER = 172;
	const SOUND_BLOCK_END_PORTAL_FRAME_FILL = 173;
	const SOUND_BLOCK_END_PORTAL_SPAWN = 174;
	const SOUND_RANDOM_ANVIL_USE = 175;
	const SOUND_BOTTLE_DRAGONBREATH = 176;
	const SOUND_PORTAL_TRAVEL = 177;
	const SOUND_DEFAULT = 178;
	const SOUND_UNDEFINED = 179;
	

	/** @var int */
	public $sound;
	/** @var Vector3 */
	public $position;
	/** @var int */
	public $extraData = -1;
	/** @var int */
	public $pitch = 1;
	/** @var bool */
	public $unknownBool = false;
	/** @var bool */
	public $disableRelativeVolume = false;

	protected function decodePayload(){
		$this->sound = $this->getByte();
		$this->position = $this->getVector3Obj();
		$this->extraData = $this->getVarInt();
		$this->pitch = $this->getVarInt();
		$this->unknownBool = $this->getBool();
		$this->disableRelativeVolume = $this->getBool();
	}

	protected function encodePayload(){
		if(isset($this->x)) $this->position = new Vector3($this->x, $this->y, $this->z);
		$this->putByte($this->sound);
		$this->putVector3Obj($this->position);
		$this->putVarInt($this->extraData);
		$this->putVarInt($this->pitch);
		$this->putBool($this->unknownBool);
		$this->putBool($this->disableRelativeVolume);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLevelSoundEvent($this);
	}
}