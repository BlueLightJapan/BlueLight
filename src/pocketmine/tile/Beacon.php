<?php

/*
 *
 *   _____       _             _   _____  ______      ____       _        
 *  / ____|     (_)           | | |  __ \|  ____|    |  _ \     | |       
 * | (___  _ __  _  __ _  ___ | |_| |__) | |__ ______| |_) | ___| |_ __ _ 
 *  \___ \| '_ \| |/ _` |/ _ \| __|  ___/|  __|______|  _ < / _ \ __/ _` |
 *  ____) | |_) | | (_| | (_) | |_| |    | |____     | |_) |  __/ || (_| |
 * |_____/| .__/|_|\__, |\___/ \__|_|    |______|    |____/ \___|\__\__,_|
 *        | |       __/ |                                                 
 *        |_|      |___/      
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author SpigotPE-Beta Team
 * @link http://github.com/SpigotPE-Beta
 *
 *
*/

namespace pocketmine\tile;

use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\inventory\BeaconInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\Server;

class Beacon extends Spawnable implements Nameable, InventoryHolder {

	/**
	 * @var BeaconInventory
	 */
	private $inventory;

	public function __construct(Level $level, CompoundTag $nbt) {
  
		if (!isset($nbt->primary)) {
			$nbt->primary = new IntTag("primary", 0);
		}
    
		if (!isset($nbt->secondary)) {
			$nbt->secondary = new IntTag("secondary", 0);
		}
    
		$this->inventory = new BeaconInventory($this);
		parent::__construct($level, $nbt);
		$this->scheduleUpdate();
	} //setname

	public function saveNBT() {
		parent::saveNBT();
	}

	public function getSpawnCompound() {
		$c = new CompoundTag("", [
			new StringTag("id", Tile::BEACON),
			new ByteTag("isMovable", (bool)true),
			$this->namedtag->primary,
			$this->namedtag->secondary,
			new IntTag("x", (int)$this->x),
			new IntTag("y", (int)$this->y),
			new IntTag("z", (int)$this->z)
		]);

		if ($this->hasName()) {
			$c->CustomName = $this->namedtag->CustomName;
		}
		return $c;
	}

     public function getName() : string{
		return $this->hasName() ? $this->namedtag->CustomName->getValue() : "Beacon";
	}

	public function hasName() : bool{
		return isset($this->namedtag->CustomName);
	}

	public function setName(string $str) {
		if ($str === "") {
			unset($this->namedtag->CustomName);
			return;
		}

		$this->namedtag->CustomName = new StringTag("CustomName", $str);
	}

	public function updateCompoundTag(CompoundTag $nbt, Player $player): bool {
		$this->setPrimaryEffect($nbt->primary->getValue());
		$this->setSecondaryEffect($nbt->secondary->getValue());
		return true;
	}

	public function isPaymentItem(Item $item) {//TODO: When FloatingInv implemented, remove item
		return in_array($item->getId(), [Item::DIAMOND, Item::IRON_INGOT, Item::GOLD_INGOT, Item::EMERALD]);
	}

	public function setPrimaryEffect(int $effectId) {
		$this->namedtag->primary->setValue($effectId);
	}

	public function setSecondaryEffect(int $effectId) {
		$this->namedtag->secondary->setValue($effectId);
	}

	public function getPrimaryEffect() {
		return $this->namedtag->primary->getValue();
	}

	public function getSecondaryEffect() {
		return $this->namedtag->secondary->getValue();
	}

	public function getBeaconData() {
		return $this->namedtag;
	}

	public function getEffects() {
		return [$this->namedtag->primary->getValue(), $this->namedtag->secondary->getValue()];
	}

	public function getLayers() {
		$layers = 0;
		if ($this->checkShape($this->getSide(0), 1)) $layers++;
		else
			return $layers;
		if ($this->checkShape($this->getSide(0, 2), 2)) $layers++;
		else
			return $layers;
		if ($this->checkShape($this->getSide(0, 3), 3)) $layers++;
		else
			return $layers;
		if ($this->checkShape($this->getSide(0, 4), 4)) $layers++;
		return $layers;
	}

	public function isSecondaryAvailable() {
		return $this->isEffectAvailable(Effect::REGENERATION);//What a hack xD
	}

	public function isEffectAvailable(int $effectId) {
		switch ($effectId) {
			case Effect::SPEED:
			case Effect::HASTE:
				return $this->getLayers() >= 1 && !$this->solidAbove();
				break;
			case Effect::DAMAGE_RESISTANCE:
			case Effect::JUMP:
				return $this->getLayers() >= 2 && !$this->solidAbove();
				break;
			case Effect::STRENGTH:
				return $this->getLayers() >= 3 && !$this->solidAbove();
				break;
			case Effect::REGENERATION:
				//this case is for secondary effect only
				return $this->getLayers() >= 4 && !$this->solidAbove();
				break;
			default:
				return false;
		}
	}

	public function solidAbove() {
		if ($this->y === $this->getLevel()->getHighestBlockAt($this->x, $this->z)) return false;
		for ($i = $this->y; $i < Level::Y_MAX; $i++) {
			if (($block = $this->getLevel()->getBlock(new Vector3($this->x, $i, $this->z)))->isSolid() && !$block->getId() === Block::BEACON) return true;
		}
	}

	public function isActive() {
		return !empty($this->getEffects()) && $this->checkShape($this->getSide(0), 1);
	}

	public function checkShape(Vector3 $pos, $layer = 1) {
		for ($x = $pos->x - $layer; $x <= $pos->x + $layer; $x++)
			for ($z = $pos->z - $layer; $z <= $pos->z + $layer; $z++)
				if (!in_array($this->getLevel()->getBlockIdAt($x, $pos->y, $z), [Block::DIAMOND_BLOCK, Block::IRON_BLOCK, Block::EMERALD_BLOCK, Block::GOLD_BLOCK])) return false;
		return true;
	}

	public function applyEffects(Vector3 $pos) {
		//TODO: Apply stronger effects on secondary.
		$layers = $this->getLayers();
		foreach ($this->getLevel()->getCollidingEntities(new AxisAlignedBB($pos->x - (10 + 10 * $layers), 0, $pos->z - (10 + 10 * $layers), $pos->x + (10 + 10 * $layers), Level::Y_MAX, $pos->z + (10 + 10 * $layers))) as $player)
			foreach ($this->getEffects() as $effectId) {
				if ($this->isEffectAvailable($effectId)) {
					$player->removeEffect($effectId);//Pretty hacky..
					$effect = Effect::getEffect($effectId)->setDuration(20 * 9 + $layers * 2 * 20);
					if ($this->getSecondaryEffect() !== 0 && $this->getSecondaryEffect() !== Effect::REGENERATION)
						$effect->setAmplifier(1);
					$player->addEffect($effect);
				}
			}
	}

	public function getTierEffects() { }

	public function getEffectTier(int $tier) { }

	public function onUpdate() {
		if (!empty($this->getEffects())) {
			$this->applyEffects($this);
		}
		$this->getLevel()->getServer()->getScheduler()->scheduleDelayedTask(new BeaconDelayedCheckTask($this, $this->getLevel()->getId()), 20 * 4);//4 seconds
	}
#public function getItem(int $slot){}//Hopper
#public function setItem(int $slot,Item $item){}//Thats all for hoppers 'filtering' the items
#public function removeItem(int,int){}//Hopper
#public function getContainerSize(){}//1
#public function getMaxStackSize(){}//1
	/**
	 * Get the object related inventory
	 *
	 * @return BeaconInventory
	 */
	public function getInventory() {
		return $this->inventory;
	}
}
