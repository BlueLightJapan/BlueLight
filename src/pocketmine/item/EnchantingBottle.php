<?php

/*
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLight Team
 * 
 *
*/

namespace pocketmine\item;


use pocketmine\Server;
use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\event\entity\EntityDrinkPotionEvent;

class EnchantingBottle extends Item{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::ENCHANTING_BOTTLE, $meta, $count, "EnchantingBottle");
	}
	
	public function getMaxStackSize() : int{
		return 1;
	}
	
	public function onConsume(Entity $human){
		$pk = new EntityEventPacket();
		$pk->eid = $human->getId();
		$pk->event = EntityEventPacket::USE_ITEM;
		if($human instanceof Player){
			$human->dataPacket($pk);
		}
		Server::broadcastPacket($human->getViewers(), $pk);
		
		Server::getInstance()->getPluginManager()->callEvent($ev = new EntityDrinkPotionEvent($human, $this));
		
		if(!$ev->isCancelled()){
			foreach($ev->getEffects() as $effect){
				$human->addEffect($effect);
			}
			//Don't set the held item to glass bottle if we're in creative
			if($human instanceof Player){
				if($human->getGamemode() === 1){
					return;
				}
			}
			$human->getInventory()->setItemInHand(Item::get(self::GLASS_BOTTLE));
		}

		
	}
}

