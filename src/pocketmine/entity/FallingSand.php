<?php

/*
 *
 *    _______                    _
 *   |__   __|                  (_)
 *      | |_   _ _ __ __ _ _ __  _  ___
 *      | | | | | '__/ _` | '_ \| |/ __|
 *      | | |_| | | | (_| | | | | | (__
 *      |_|\__,_|_|  \__,_|_| |_|_|\___|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Turanic
 *
 */

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Fallable;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Position;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\item\Item as ItemItem;

class FallingSand extends Entity {

	public $width = 0.98;
	public $height = 0.98;

	protected $gravity = 0.04;
	protected $drag = 0.02;

    /** @var Block */
    protected $block;

    protected function initEntity(){
        parent::initEntity();

        $blockId = 0;

        //TODO: 1.8+ save format
        if($this->namedtag->hasTag("TileID", IntTag::class)){
            $blockId = $this->namedtag->getInt("TileID");
        }elseif($this->namedtag->hasTag("Tile", ByteTag::class)){
            $blockId = $this->namedtag->getByte("Tile");
            $this->namedtag->removeTag("Tile");
        }

        if($blockId === 0){
            $this->close();
            return;
        }

        $damage = $this->namedtag->getByte("Data", 0);

        $this->block = BlockFactory::get($blockId, $damage);

		$this->propertyManager->setInt(self::DATA_VARIANT, BlockFactory::toStaticRuntimeId($this->block->getId(), $this->block->getDamage()));
    }

	/**
	 * @param Entity $entity
	 *
	 * @return bool
	 */
	public function canCollideWith(Entity $entity) : bool{
		return false;
	}

    /**
     * @param EntityDamageEvent $source
     * @return bool|void
     * @internal param float $damage
     */
	public function attack(EntityDamageEvent $source){
		if($source->getCause() === EntityDamageEvent::CAUSE_VOID){
			parent::attack($source);
		}
	}

    public function entityBaseTick(int $tickDiff = 1) : bool{
        if($this->closed){
            return false;
        }

        $hasUpdate = parent::entityBaseTick($tickDiff);

        if(!$this->isFlaggedForDespawn()){
            $pos = Position::fromObject($this->add(-$this->width / 2, $this->height, -$this->width / 2)->floor(), $this->getLevel());

            $this->block->position($pos);

            $blockTarget = null;
            if($this->block instanceof Fallable){
                $blockTarget = $this->block->tickFalling();
            }

            if($this->onGround or $blockTarget !== null){
                $this->flagForDespawn();

                $block = $this->level->getBlock($pos);
                if($block->getId() > 0 and $block->isTransparent() and !$block->canBeReplaced()){
                    //FIXME: anvils are supposed to destroy torches
                    $this->getLevel()->dropItem($this, ItemItem::get($this->getBlock(), $this->getDamage()));
                }else{
                    $this->server->getPluginManager()->callEvent($ev = new EntityBlockChangeEvent($this, $block, $blockTarget ?? $this->block));
                    if(!$ev->isCancelled()){
                        $this->getLevel()->setBlock($pos, $ev->getTo(), true);
                    }
                }
                $hasUpdate = true;
            }
        }

        return $hasUpdate;
    }

    public function getBlock(){
        return $this->block->getId();
    }

    public function getDamage(){
        return $this->block->getDamage();
    }

    public function saveNBT(){
        $this->namedtag->setInt("TileID", $this->block->getId(), true);
        $this->namedtag->setByte("Data", $this->block->getDamage());
    }
}
