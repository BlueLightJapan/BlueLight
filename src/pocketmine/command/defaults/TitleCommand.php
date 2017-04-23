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
 
use pocketmine\network\protocol\SetTitlePacket;
use pocketmine\command\CommandSender;
use pocketmine\Player;
class TitleCommand extends VanillaCommand {

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.title.description",
			"%pocketmine.command.title.usage"
		);
		$this->setPermission("pocketmine.command.title");
	}
  
	public function execute(CommandSender $sender, $currentAlias, array $args){

		if(!($this->testPermission($sender))) return false;
		if(count($args) === 0) return $sender->sendMessage("Usage: /title <player> <title> <subtile>");
		if(!isset($args[1])) $args[1] = "";
		if(!isset($args[2])) $args[2] = "";
		$player = $sender->getServer()->getPlayer($args[0]);
		if($player instanceof Player){
			$player->sendTitle($args[1],$args[2]);
			if($sender instanceof Player){
				$sender->sendTitle($args[1],$args[2]);
			}
		}else{
			$sender->sendMessage($args[0]." not found");
		}
	}
}
