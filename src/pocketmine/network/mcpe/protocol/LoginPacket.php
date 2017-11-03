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


use pocketmine\network\mcpe\NetworkSession;
use pocketmine\utils\Utils;

class LoginPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::LOGIN_PACKET;

	const EDITION_POCKET = 0;

	/** @var string */
	public $username;
	/** @var int */
	public $protocol;
	/** @var string */
	public $clientUUID;
	/** @var int */
	public $clientId;
	/** @var string */
	public $identityPublicKey;
	/** @var string */
	public $serverAddress;

	/** @var string */
	public $skinId;
	/** @var string */
	public $skin = "";

	public $deviceModel;
	public $deviceOS;
	public $ui = -1;
	public $xuid = "";

	public $languageCode = "";
	public $clientVersion = "";
	public $skinGeometryName = "";
	public $skinGeometryData = "";
	public $capeData = "";

	/** @var array (the "chain" index contains one or more JWTs) */
	public $chainData = [];
	/** @var string */
	public $clientDataJwt;
	/** @var array decoded payload of the clientData JWT */
	public $clientData = [];

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	protected function decodePayload(){
		$this->protocol = $this->getInt();

		if($this->protocol !== ProtocolInfo::CURRENT_PROTOCOL){
			$this->buffer = null;
			return; //Do not attempt to decode for non-accepted protocols
		}

		$this->setBuffer($this->getString(), 0);

		$this->chainData = json_decode($this->get($this->getLInt()), true);
		foreach($this->chainData["chain"] as $chain){
			$webtoken = Utils::decodeJWT($chain);
			if(isset($webtoken["extraData"])){
				if(isset($webtoken["extraData"]["displayName"])){
					$this->username = $webtoken["extraData"]["displayName"];
				}
				if(isset($webtoken["extraData"]["identity"])){
					$this->clientUUID = $webtoken["extraData"]["identity"];
				}
				if(isset($webtoken["extraData"]["XUID"])){
					$this->xuid = $webtoken["extraData"]["XUID"];
				}
				if(isset($webtoken["identityPublicKey"])){
					$this->identityPublicKey = $webtoken["identityPublicKey"];
				}
			}
		}

		$this->clientDataJwt = $this->get($this->getLInt());
		$this->clientData = Utils::decodeJWT($this->clientDataJwt);

		$this->clientId = $this->clientData["ClientRandomId"] ?? null;
		$this->serverAddress = $this->clientData["ServerAddress"] ?? null;
		$this->skinId = $this->clientData["SkinId"] ?? null;

		if(isset($this->clientData["SkinData"])){
			$this->skin = base64_decode($this->clientData["SkinData"]);
		} 
		if(isset($this->clientData["DeviceModel"])){
			$this->deviceModel = $this->clientData["DeviceModel"];
		}
		if(isset($this->clientData["DeviceOS"])) {
			$this->deviceOS = $this->clientData["DeviceOS"];
		}
		if(isset($this->clientData["SkinGeometryName"])){
			$this->skinGeometryName = $this->clientData["SkinGeometryName"];    
		}
		if(isset($this->clientData["SkinGeometry"])){
			$this->skinGeometryData = base64_decode($this->clientData["SkinGeometry"]);  
		}
		if(isset($this->clientData["UIProfile"])){
			$this->ui = $this->clientData["UIProfile"];
		}
		if(isset($this->clientData["LanguageCode"])){
			$this->languageCode = $this->clientData["LanguageCode"];
		}
		if(isset($this->clientData["GameVersion"])){
			$this->clientVersion = $this->clientData["GameVersion"];
		}
		if(isset($this->clientData["CapeData"])){
			$this->capeData = base64_decode($this->clientData["CapeData"]);
		}
	}

	protected function encodePayload(){
		//TODO
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLogin($this);
	}
}