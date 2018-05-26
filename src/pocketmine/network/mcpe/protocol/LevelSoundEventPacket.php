<?php

/*
 *               _ _
 *         /\   | | |
 *        /  \  | | |_ __ _ _   _
 *       / /\ \ | | __/ _` | | | |
 *      / ____ \| | || (_| | |_| |
 *     /_/    \_|_|\__\__,_|\__, |
 *                           __/ |
 *                          |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Altay
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;

class LevelSoundEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_SOUND_EVENT_PACKET;

	public const SOUND_ITEM_USE_ON = 0;
	public const SOUND_HIT = 1;
	public const SOUND_STEP = 2;
	public const SOUND_FLY = 3;
	public const SOUND_JUMP = 4;
	public const SOUND_BREAK = 5;
	public const SOUND_PLACE = 6;
	public const SOUND_HEAVY_STEP = 7;
	public const SOUND_GALLOP = 8;
	public const SOUND_FALL = 9;
	public const SOUND_AMBIENT = 10;
	public const SOUND_AMBIENT_BABY = 11;
	public const SOUND_AMBIENT_IN_WATER = 12;
	public const SOUND_BREATHE = 13;
	public const SOUND_DEATH = 14;
	public const SOUND_DEATH_IN_WATER = 15;
	public const SOUND_DEATH_TO_ZOMBIE = 16;
	public const SOUND_HURT = 17;
	public const SOUND_HURT_IN_WATER = 18;
	public const SOUND_MAD = 19;
	public const SOUND_BOOST = 20;
	public const SOUND_BOW = 21;
	public const SOUND_SQUISH_BIG = 22;
	public const SOUND_SQUISH_SMALL = 23;
	public const SOUND_FALL_BIG = 24;
	public const SOUND_FALL_SMALL = 25;
	public const SOUND_SPLASH = 26;
	public const SOUND_FIZZ = 27;
	public const SOUND_FLAP = 28;
	public const SOUND_SWIM = 29;
	public const SOUND_DRINK = 30;
	public const SOUND_EAT = 31;
	public const SOUND_TAKEOFF = 32;
	public const SOUND_SHAKE = 33;
	public const SOUND_PLOP = 34;
	public const SOUND_LAND = 35;
	public const SOUND_SADDLE = 36;
	public const SOUND_ARMOR = 37;
	public const SOUND_MOB_ARMOR_STAND_PLACE = 38;
	public const SOUND_ADD_CHEST = 39;
	public const SOUND_THROW = 40;
	public const SOUND_ATTACK = 41;
	public const SOUND_ATTACK_NODAMAGE = 42;
	public const SOUND_ATTACK_STRONG = 43;
	public const SOUND_WARN = 44;
	public const SOUND_SHEAR = 45;
	public const SOUND_MILK = 46;
	public const SOUND_THUNDER = 47;
	public const SOUND_EXPLODE = 48;
	public const SOUND_FIRE = 49;
	public const SOUND_IGNITE = 50;
	public const SOUND_FUSE = 51;
	public const SOUND_STARE = 52;
	public const SOUND_SPAWN = 53;
	public const SOUND_SHOOT = 54;
	public const SOUND_BREAK_BLOCK = 55;
	public const SOUND_LAUNCH = 56;
	public const SOUND_BLAST = 57;
	public const SOUND_LARGE_BLAST = 58;
	public const SOUND_TWINKLE = 59;
	public const SOUND_REMEDY = 60;
	public const SOUND_UNFECT = 61;
	public const SOUND_LEVELUP = 62;
	public const SOUND_BOW_HIT = 63;
	public const SOUND_BULLET_HIT = 64;
	public const SOUND_EXTINGUISH_FIRE = 65;
	public const SOUND_ITEM_FIZZ = 66;
	public const SOUND_CHEST_OPEN = 67;
	public const SOUND_CHEST_CLOSED = 68;
	public const SOUND_SHULKERBOX_OPEN = 69;
	public const SOUND_SHULKERBOX_CLOSED = 70;
	public const SOUND_ENDERCHEST_OPEN = 71;
	public const SOUND_ENDERCHEST_CLOSED = 72;
	public const SOUND_POWER_ON = 73;
	public const SOUND_POWER_OFF = 74;
	public const SOUND_ATTACH = 75;
	public const SOUND_DETACH = 76;
	public const SOUND_DENY = 77;
	public const SOUND_TRIPOD = 78;
	public const SOUND_POP = 79;
	public const SOUND_DROP_SLOT = 80;
	public const SOUND_NOTE = 81;
	public const SOUND_THORNS = 82;
	public const SOUND_PISTON_IN = 83;
	public const SOUND_PISTON_OUT = 84;
	public const SOUND_PORTAL = 85;
	public const SOUND_WATER = 86;
	public const SOUND_LAVA_POP = 87;
	public const SOUND_LAVA = 88;
	public const SOUND_BURP = 89;
	public const SOUND_BUCKET_FILL_WATER = 90;
	public const SOUND_BUCKET_FILL_LAVA = 91;
	public const SOUND_BUCKET_EMPTY_WATER = 92;
	public const SOUND_BUCKET_EMPTY_LAVA = 93;
	public const SOUND_ARMOR_EQUIP_CHAIN = 94;
	public const SOUND_ARMOR_EQUIP_DIAMOND = 95;
	public const SOUND_ARMOR_EQUIP_GENERIC = 96;
	public const SOUND_ARMOR_EQUIP_GOLD = 97;
	public const SOUND_ARMOR_EQUIP_IRON = 98;
	public const SOUND_ARMOR_EQUIP_LEATHER = 99;
	public const SOUND_ARMOR_EQUIP_ELYTRA = 100;
	public const SOUND_RECORD_13 = 101;
	public const SOUND_RECORD_CAT = 102;
	public const SOUND_RECORD_BLOCKS = 103;
	public const SOUND_RECORD_CHIRP = 104;
	public const SOUND_RECORD_FAR = 105;
	public const SOUND_RECORD_MALL = 106;
	public const SOUND_RECORD_MELLOHI = 107;
	public const SOUND_RECORD_STAL = 108;
	public const SOUND_RECORD_STRAD = 109;
	public const SOUND_RECORD_WARD = 110;
	public const SOUND_RECORD_11 = 111;
	public const SOUND_RECORD_WAIT = 112;
	public const SOUND_GUARDIAN_FLOP = 114;
	public const SOUND_ELDERGUARDIAN_CURSE = 115;
	public const SOUND_MOB_WARNING = 116;
	public const SOUND_MOB_WARNING_BABY = 117;
	public const SOUND_TELEPORT = 118;
	public const SOUND_SHULKER_OPEN = 119;
	public const SOUND_SHULKER_CLOSE = 120;
	public const SOUND_HAGGLE = 121;
	public const SOUND_HAGGLE_YES = 122;
	public const SOUND_HAGGLE_NO = 123;
	public const SOUND_HAGGLE_IDLE = 124;
	public const SOUND_CHORUSGROW = 125;
	public const SOUND_CHORUSDEATH = 126;
	public const SOUND_GLASS = 127;
	public const SOUND_POTION_BREWED = 128;
	public const SOUND_CAST_SPELL = 129;
	public const SOUND_PREPARE_ATTACK = 130;
	public const SOUND_PREPARE_SUMMON = 131;
	public const SOUND_PREPARE_WOLOLO = 132;
	public const SOUND_FANG = 133;
	public const SOUND_CHARGE = 134;
	public const SOUND_CAMERA_TAKE_PICTURE = 135;
	public const SOUND_LEASHKNOT_PLACE = 136;
	public const SOUND_LEASHKNOT_BREAK = 137;
	public const SOUND_GROWL = 138;
	public const SOUND_WHINE = 139;
	public const SOUND_PANT = 140;
	public const SOUND_PURR = 141;
	public const SOUND_PURREOW = 142;
	public const SOUND_DEATH_MIN_VOLUME = 143;
	public const SOUND_DEATH_MID_VOLUME = 144;
	public const SOUND_IMITATE_BLAZE = 145;
	public const SOUND_IMITATE_CAVE_SPIDER = 146;
	public const SOUND_IMITATE_CREEPER = 147;
	public const SOUND_IMITATE_ELDER_GUARDIAN = 148;
	public const SOUND_IMITATE_ENDER_DRAGON = 149;
	public const SOUND_IMITATE_ENDERMAN = 150;
	public const SOUND_IMITATE_EVOCATION_ILLAGER = 152;
	public const SOUND_IMITATE_GHAST = 153;
	public const SOUND_IMITATE_HUSK = 154;
	public const SOUND_IMITATE_ILLUSION_ILLAGER = 155;
	public const SOUND_IMITATE_MAGMA_CUBE = 156;
	public const SOUND_IMITATE_POLAR_BEAR = 157;
	public const SOUND_IMITATE_SHULKER = 158;
	public const SOUND_IMITATE_SILVERFISH = 159;
	public const SOUND_IMITATE_SKELETON = 160;
	public const SOUND_IMITATE_SLIME = 161;
	public const SOUND_IMITATE_SPIDER = 162;
	public const SOUND_IMITATE_STRAY = 163;
	public const SOUND_IMITATE_VEX = 164;
	public const SOUND_IMITATE_VINDICATION_ILLAGER = 165;
	public const SOUND_IMITATE_WITCH = 166;
	public const SOUND_IMITATE_WITHER = 167;
	public const SOUND_IMITATE_WITHER_SKELETON = 168;
	public const SOUND_IMITATE_WOLF = 169;
	public const SOUND_IMITATE_ZOMBIE = 170;
	public const SOUND_IMITATE_ZOMBIE_PIGMAN = 171;
	public const SOUND_IMITATE_ZOMBIE_VILLAGER = 172;
	public const SOUND_BLOCK_END_PORTAL_FRAME_FILL = 173;
	public const SOUND_BLOCK_END_PORTAL_SPAWN = 174;
	public const SOUND_RANDOM_ANVIL_USE = 175;
	public const SOUND_BOTTLE_DRAGONBREATH = 176;
	public const SOUND_PORTAL_TRAVEL = 177;
	public const SOUND_DEFAULT = 178;
	public const SOUND_UNDEFINED = 179;

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
		$this->position = $this->getVector3();
		$this->extraData = $this->getVarInt();
		$this->pitch = $this->getVarInt();
		$this->unknownBool = $this->getBool();
		$this->disableRelativeVolume = $this->getBool();
	}

	protected function encodePayload(){
		$this->putByte($this->sound);
		$this->putVector3($this->position);
		$this->putVarInt($this->extraData);
		$this->putVarInt($this->pitch);
		$this->putBool($this->unknownBool);
		$this->putBool($this->disableRelativeVolume);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLevelSoundEvent($this);
	}
}
