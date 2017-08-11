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

declare(strict_types=1);

namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
class MakeServerCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"Create a Phar server from src",
			"/makeserver"
		);
		$this->setPermission("pocketmine.command.makeserver");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return false;
		}

		$server = $sender->getServer();
		$pharPath = Server::getInstance()->getPluginPath() . DIRECTORY_SEPARATOR . "BDevtools" . DIRECTORY_SEPARATOR . $server->getName() . "_" . $server->getPocketMineVersion() . ".phar";
		if(file_exists($pharPath)){
			$sender->sendMessage("Phar file already exists, overwriting...");
			@unlink($pharPath);
		}
		$phar = new \Phar($pharPath);
		$phar->setMetadata([
			"name" => $server->getName(),
			"version" => $server->getPocketMineVersion(),
			"api" => $server->getApiVersion(),
			"minecraft" => $server->getVersion(),
			"protocol" => ProtocolInfo::CURRENT_PROTOCOL,
			"creationDate" => time()
		]);
		$phar->setStub('<?php define("pocketmine\\\\PATH", "phar://". __FILE__ ."/"); require_once("phar://". __FILE__ ."/src/pocketmine/PocketMine.php");  __HALT_COMPILER();');
		$phar->setSignatureAlgorithm(\Phar::SHA1);
		$phar->startBuffering();

		$filePath = substr(\pocketmine\PATH, 0, 7) === "phar://" ? \pocketmine\PATH : realpath(\pocketmine\PATH) . "/";
		$filePath = rtrim(str_replace("\\", "/", $filePath), "/") . "/";
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filePath . "src")) as $file){
			$path = ltrim(str_replace(["\\", $filePath], ["/", ""], $file), "/");
			if($path{0} === "." or strpos($path, "/.") !== false or (substr($path, 0, 4) !== "src/" and substr($path, 0, 7) !== "vendor/")){
				continue;
			}
			$phar->addFile($file, $path);
			$sender->sendMessage("[BDevtools] Adding $path");
		}
		foreach($phar as $file => $finfo){
			/** @var \PharFileInfo $finfo */
			if($finfo->getSize() > (1024 * 512)){
				$finfo->compress(\Phar::GZ);
			}
		}
		if(!isset($args[0]) or (isset($args[0]) and $args[0] != "nogz")){
			$phar->compressFiles(\Phar::GZ);
		}
		$phar->stopBuffering();

		$sender->sendMessage($server->getName() . " " . $server->getPocketMineVersion() . " Phar file has been created on " . $pharPath);

		return true;
	}
}