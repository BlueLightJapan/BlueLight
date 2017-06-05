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


namespace pocketmine\entity;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class Witch extends Monster{
	const NETWORK_ID = 45;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	public $maxhealth = 26;
	public $witchDrops = [ItemItem::GLOWSTONE_DUST, ItemItem::SUGAR, ItemItem::REDSTONE_DUST, ItemItem::SPIDER_EYE, ItemItem::GLASS_BOTTLE, ItemItem::GUNPOWDER, ItemItem::STICK, ItemItem::STICK];

	public function getName(){
		return "Witch";
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	public function getDrops(){
		$drops = [];
		$ev = $this->getLastDamageCause();
		$looting = $ev instanceof EntityDamageByEntityEvent ? $ev->getDamager() instanceof Player ? $ev->getDamager()->getInventory()->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING) : 0 : 0;

		$tears = rand(0, 1) + rand(0, $looting);

		$drops[] = ItemItem::get(ItemItem::GHAST_TEAR, 0, $tears);

		$gunpowers = rand(0, 2) + rand(0, $looting);

		$drops[] = ItemItem::get(ItemItem::GUNPOWDER, 0, $gunpowers);
		$count = rand(1, 3);

		for ($j = 0; $j < $i; ++$j){
			$k = rand(0, 2);
			$item = $this->witchDrops[rand(0, count($this->witchDrops))];

			if ($looting > 0){
				$k += rand(0, $looting);
			}

			$drops[] = ItemItem::get($item, 0, $k);
		}
		return $drops;
	}
}