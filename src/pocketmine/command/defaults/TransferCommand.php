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

namespace pocketmine\command\defaults;

use pocketmine\network\protocol\TransferPacket;
use pocketmine\command\CommandSender;
use pocketmine\{Player, Server};

class TransferCommand extends VanillaCommand{
	
	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.transfer.description",
			"%pocketmine.command.transfer.usage",
			["transfer"]
		);
		$this->setPermission("pocketmine.command.transfer");
	}
	
	public function execute(CommandSender $sender, $currentAlias, array $args){
		$address = null;
		$port = null;
		$player = null;
		if($sender instanceof Player){
			if(!$this->testPermission($sender)){
				return true;
			}
			if(count($args) <= 0){
				$sender->sendMessage("Usage: /transferserver <address> [port]");
				return false;
			}
			$address = strtolower($args[0]);
			$port = (isset($args[1]) && is_numeric($args[1]) ? $args[1] : 19132);
			$sender->transfer($address, $port);
			return false;
		}
		if(count($args) <= 1){
			$sender->sendMessage("Usage: /transferserver <player> <address> [port]");
			return false;
		}
		if(!($player = Server::getInstance()->getPlayer($args[0])) instanceof Player){
			$sender->sendMessage("Player specified not found!");
			return false;
		}
		$address = strtolower($args[1]);
		$port = (isset($args[2]) && is_numeric($args[2]) ? $args[2] : 19132);
		
		$sender->sendMessage("Sending ".$player->getName()." to ".$address.":".$port);
		$player->transfer($address, $port);
	}
}
