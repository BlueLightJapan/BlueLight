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
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/

namespace pocketmine\entity;

use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class Ghast extends Monster{
	const NETWORK_ID = 41;

	public $width = 6;
	public $length = 6;
	public $height = 6;
	public $maxhealth = 10;

	public function getName() : string{
		return "Ghast";
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->type = self::NETWORK_ID;
	
		$pk->position = $this->asVector3();

		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	public function getDrops() : array{
		$drops = [];
		$ev = $this->getLastDamageCause();
		$looting = 0;
		if($ev instanceof EntityDamageByEntityEvent){
			if($ev->getDamager() instanceof Player){
				if(($lootingobj = $ev->getDamager()->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING)) != null){
					$looting = $lootingobj->getLevel();
				}
			}
		}
		$tears = rand(0, 1) + rand(0, $looting);

		$drops[] = ItemItem::get(ItemItem::GHAST_TEAR, 0, $tears);

		$gunpowers = rand(0, 2) + rand(0, $looting);

		$drops[] = ItemItem::get(ItemItem::GUNPOWDER, 0, $gunpowers);
		return $drops;
	}
}