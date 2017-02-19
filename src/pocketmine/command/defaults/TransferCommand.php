<?php

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
