<?php

/*
 *
 *  ____          
 * |  __|_              _
 * | |__| |      _    _(_)_ __   ___
 * |  __| |_   _| |  | | | '_ \ / _ \
 * | |__| | | | | |/\| | | | | | (_) |
 * |____|_|\ \/ \__/\__/_|_| |_|\___ |
 *         _|  /                 __| |
 *        |___/                 |____/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author H4PM Team
 * @link http://www.github.net/H4PM  
 * 
 *
*/

namespace pocketmine\block;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\level\particle\DestroyBlockParticle;

class Sponge extends Solid{

	protected $id = self::SPONGE;
	protected $r = 6;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}
	public function getHardness() {
		return 0.6;
	}
	public function absorption($block = null){
		if ($block == null) $block = $this;
		$range = $this->r / 2;
		for ($xx = -$range; $xx <= $range; $xx++){
			for ($yy = -$range; $yy <= $range; $yy++){
				for ($zz = -$range; $zz <= $range; $zz++){
					$block = $this->getLevel()->getBlock(new Vector3($this->x + $xx, $this->y + $yy, $this->z + $zz));
					if ($block->getId() === 8) $this->getLevel()->setBlock($block, Block::get(Block::AIR), true, true);
					if ($block->getId() === 9) $this->getLevel()->setBlock($block, Block::get(Block::AIR), true, true);
				}
			}
		}
	}

	public function onUpdate($type){
		if ($this->meta == 0) {
			if($type === Level::BLOCK_UPDATE_NORMAL){
				$blockAbove = $this->getSide(Vector3::SIDE_UP)->getId();
				$blockBeneath = $this->getSide(Vector3::SIDE_DOWN)->getId();
				$blockNorth = $this->getSide(Vector3::SIDE_NORTH)->getId();
				$blockSouth = $this->getSide(Vector3::SIDE_SOUTH)->getId();
				$blockEast = $this->getSide(Vector3::SIDE_EAST)->getId();
				$blockWest = $this->getSide(Vector3::SIDE_WEST)->getId();
				if($blockAbove === 8 or $blockBeneath === 8 or $blockNorth === 8 or $blockSouth === 8 or $blockEast === 8 or $blockWest === 8 or $blockAbove === 9 or $blockBeneath === 9 or $blockNorth === 9 or $blockSouth === 9 or $blockEast === 9 or $blockWest === 9){
					$this->absorption($this);
					$this->getLevel()->setBlock($this, Block::get(Block::SPONGE, 1), true, true);
					
					$this->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($this->x,$this->y,$this->z), Block::get(Block::WATER)));
					return Level::BLOCK_UPDATE_NORMAL;
				}
			}
			return false;
		}
	}
	public function getName(){
		static $names = [
			0 => "Sponge",
			1 => "Wet Sponge",
		];
		return $names[$this->meta & 0x0f];
	}
	public function getDrops(Item $item){
		return [[$this->id, $this->meta & 0x0f, 1],];
	}
}
