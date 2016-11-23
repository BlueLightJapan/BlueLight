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

namespace pocketmine\utils;

class YamlManager extends DataBase{


	public function Connect(){
		$this->config = new Config($this->getDataFolder(). "players.yml", Config::YAML,array('!!!!notice!!!!' => '"thisiscreatedby BlueLightTeam(haniokasai)'));
	}

	public function loadInventory($player){
		$i =1;
		while($this->config->exists(array($name=>$i))){
			$dbitem = $this->config->get(array($name=>$i));
			$player->getInventory()->setItem($i,Item::get($dbitem["id"],$dbitem["meta"],$dbitem["count"]));
			++$i;
		}
	}

	public function saveInventory($player){
		$name = strtolower($player->getName());
		$inventory = $player->getInventory();
		$stack = array();
		$i=1;
		foreach ($inventory->getContents() as $slot=>&$item){
			$id = $item->getId();
			$meta = $item->getDamage();
			$count = $item->getCount();
			array_push($stack,array($name=>array($i=>array("id"=>$id,"meta"=>$meta,"count"=>$count))));
			++$i;
		}
		$this->config->set($stack);
		$this->config->save();
	}

	public function registerPlayer($name){
			$stack=array($name=>array(`gamemode`=>$GameType,`lastplayed`=>$lastPlayed,`hunger`=>$Hunger,`health`=>$Health,`maxhealth`=>$MaxHealth,`exp`=>$Experience,`explevel`=>$ExpLevel));
			$this->config->set($stack);
			$this->config->save();
	}

	public function savePlayer($player){
		$GameType = $player->gamemode;
		$lastPlayed = time();
		$Hunger = $player->food;
		$Health = $player->getHealth();
		$MaxHealth = $player->getMaxHealth();
		$Experience = $player->exp;
		$ExpLevel = $player->expLevel;
	}

}