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

namespace pocketmine\block;

use pocketmine\inventory\BeaconInventory;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\item\Tool;
use pocketmine\tile\Beacon as BeaconTile;
use pocketmine\tile\Tile;

class Beacon extends Solid{

	protected $id = self::BEACON;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function canBeActivated() : bool{
		return true;
	}

	public function getLightLevel(){
		return 15;
	}

	public function getHardness(){
		return 3;
	}

	public function getName(){
		return "Beacon";
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$tile = $this->getLevel()->getTile($this);

			if($tile instanceof BeaconTile){
				$player->addWindow($tile->getInventory());
				return true;
			}else{

				$nbt = new CompoundTag("", [
					new ListTag("Items", []),
					new StringTag("id", Tile::BEACON),
					new IntTag("x", $this->x),
					new IntTag("y", $this->y),
					new IntTag("z", $this->z)]);

				$nbt->Items->setTagType(NBT::TAG_Compound);
				$beacon = Tile::createTile("Beacon", $this->getLevel()->getChunk($this->x >> 4, $this->z >> 4), $nbt);
			}

			if(isset($beacon->namedtag->Lock) and $beacon->namedtag->Lock instanceof StringTag){
				if($beacon->namedtag->Lock->getValue() !== $item->getCustomName()){
					return true;
				}
			}
			if($tile instanceof BeaconTile){

			$player->addWindow($beacon->getInventory());

			}
		}

		return true;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$this->getLevel()->setBlock($block, Block::get(Block::BEACON, 0), true, true);
		$nbt = new CompoundTag("", [
			new ListTag("Items", []),
			new StringTag("id", Tile::BEACON),
			new IntTag("x", $block->x),
			new IntTag("y", $block->y),
			new IntTag("z", $block->z)
		]);
		$nbt->Items->setTagType(NBT::TAG_Compound);

		if($item->hasCustomBlockData()){
			foreach($item->getCustomBlockData() as $key => $v){
				$nbt->{$key} = $v;
			}
		}

		Tile::createTile("Beacon", $this->getLevel()->getChunk($this->x >> 4, $this->z >> 4), $nbt);

		return true;
	}

	public function onBreak(Item $item){
		$this->getLevel()->setBlock($this, new Air(), true, true);

		return true;
	}

}