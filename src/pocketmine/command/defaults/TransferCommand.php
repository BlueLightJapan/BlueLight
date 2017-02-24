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
use pocketmine\command\Command;
use pocketmine\Server;
use pocketmine\Player;

class TransferCommand extends VanillaCommand{
	
	public function __construct($name){
		parent::__construct(
		$name,
		"%pocketmine.command.transfer.description",
		"%pocketmine.command.transfer.usage",
		["transfer","connect"]
		);
		$this->setPermission("pocketmine.command.transfer");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!($this->testPermission($sender))) return;
		if($sender instanceof Player){
			$port = 19132;
			if(!(empty($args[1]))) $port = $args[1];
			if(empty($args[0])) return $sender->sendMessage("Usage: /transfer <ip> [port]");

			$pk = new TransferPacket();
			$pk->address = $args[0];
			$pk->port = $port;
			$sender->dataPacket($pk);

			Command::broadcastCommandMessage($sender, "Transferred to " . $args[0] . ":" . $port);
		}else{
			$sender->sendMessage("Run this command in game!");
		}
	}
}
