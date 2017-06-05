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
use pocketmine\entity\AI\EntityAISwimming;
use pocketmine\entity\AI\EntityAIMate;
use pocketmine\entity\AI\EntityAIWatchClosest;
use pocketmine\entity\AI\EntityAILookIdle;
use pocketmine\entity\AI\EntityAIWander;
use pocketmine\entity\AI\EntityAIPanic;
use pocketmine\entity\AI\EntityAITempt;
use pocketmine\item\Item as ItemItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\Player;

class Cow extends Animal{
	const NETWORK_ID = 11;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;
	public $maxhealth = 10;

	public function getName(){
		return "Cow";
	}

	public function initEntity(){
		$this->getNavigator()->setAvoidsWater(true);
		$this->tasks->addTask(0, new EntityAISwimming($this));
		$this->tasks->addTask(1, new EntityAIPanic($this, 2.0));
		$this->tasks->addTask(2, new EntityAIMate($this, 1.0));
		$this->tasks->addTask(3, new EntityAITempt($this, 1.25, ItemItem::WHEAT, false));
		//$this->tasks->addTask(4, new EntityAIFollowParent($this, 1.25));
		$this->tasks->addTask(5, new EntityAIWander($this, 1.0));
		$this->tasks->addTask(6, new EntityAIWatchClosest($this, "pocketmine\Player", 6.0));
		$this->tasks->addTask(7, new EntityAILookIdle($this));
		$this->setMaxHealth(20);
		parent::initEntity();
	}

	protected function addAttributes(){
		parent::addAttributes();
		$this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue(0.20000000298023224);
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Cow::NETWORK_ID;
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

	public function onRightClick(Player $player){
		$item = $player->getInventory()->getItemInHand();
		if($item->getId() == ItemItem::BUCKET && $item->getDamage() == 0 && !$player->isCreative() && !$this->isBaby()){
			$milk = ItemItem::get(ItemItem::BUCKET, 1, 1);
			if($item->count-- == 1){
				$player->getInventory()->setItemInHand($milk);
			}else if(!$player->getInventory()->canAddItem($milk)){
				$motion = $player->getDirectionVector()->multiply(0.4);
				$player->level->dropItem($player->add(0, 1.3, 0), $milk, $motion, 40);
			}
		}
		parent::onRightClick($player);
	}

	public function getDrops(){
		$drops = [];
		$ev = $this->getLastDamageCause();
		$looting = $ev instanceof EntityDamageByEntityEvent ? $ev->getDamager() instanceof Player ? $ev->getDamager()->getInventory()->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING) : 0 : 0;

		$leathers = rand(0, 2) + 1 + rand(0, $looting);

		$drops[] = ItemItem::get(ItemItem::LEATHER, 0, $leathers);

		$beefs = rand(0, 2) + 1 + rand(0, $looting);

		if ($this->isOnFire()){
			$drops[] = ItemItem::get(ItemItem::COOKED_BEEF, 0, $beefs);
		}else{
			$drops[] = ItemItem::get(ItemItem::RAW_BEEF, 0, $beefs);
		}
		return $drops;
	}

	public function onUpdate($currentTick) {
		parent::onUpdate($currentTick);
		return true;
	}

	public function createChild($ageable){

		$nbt = new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $this->getX()),
				new DoubleTag("", $this->getY()),
				new DoubleTag("", $this->getZ())
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0)
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", lcg_value() * 360),
				new FloatTag("", 0)
			]),
		]);

		$entity = Entity::createEntity("Cow", $this->level, $nbt);

		if($entity instanceof Entity){
			return $entity;
		}
	}
}