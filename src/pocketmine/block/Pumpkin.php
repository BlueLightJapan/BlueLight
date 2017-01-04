<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\Server;

class Pumpkin extends Solid{

	protected $id = self::PUMPKIN;

	public function __construct(){

	}

	public function getHardness(){
		return 1;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	public function getName(){
		return "Pumpkin";
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($player instanceof Player){
			$this->meta = ((int) $player->getDirection() + 5) % 4;
		}
		$this->getLevel()->setBlock($block, $this, true, true);

		if(Server::getInstance()->getProperty("GolemSpawn", false)){
			$down = $this->getLevel()->getBlock($this->add(0, -1, 0))->getId();
			$tdown = $this->getLevel()->getBlock($this->add(0, -2, 0))->getId();
			if($down == Block::IRON_BLOCK && $tdown == Block::IRON_BLOCK){
				if($this->meta == 0 || $this->meta == 2){
					if($this->getLevel()->getBlock($this->add(1, -1, 0))->getId() == Block::IRON_BLOCK && $this->getLevel()->getBlock($this->add(-1, -1, 0))->getId() == Block::IRON_BLOCK){
						$this->getLevel()->setBlock($this, new Air(), true, true);
						$this->getLevel()->setBlock($this->add(0, -1, 0), new Air(), true, true);
						$this->getLevel()->setBlock($this->add(0, -2, 0), new Air(), true, true);
						$this->getLevel()->setBlock($this->add(1, -1, 0), new Air(), true, true);
						$this->getLevel()->setBlock($this->add(-1, -1, 0), new Air(), true, true);
						$yaw = 0;
						if($this->meta == 0) $yaw = 0;
						if($this->meta == 2) $yaw = 180;
						$nbt = new CompoundTag("", [
							"Pos" => new ListTag("Pos", [
								new DoubleTag("", $this->getX() + 0.5),
								new DoubleTag("", $this->getY()),
								new DoubleTag("", $this->getZ() + 0.5)
							]),
							"Motion" => new ListTag("Motion", [
								new DoubleTag("", 0),
								new DoubleTag("", 0),
								new DoubleTag("", 0)
							]),
							"Rotation" => new ListTag("Rotation", [
								new FloatTag("", $yaw),
								new FloatTag("", 0)
							]),
						]);

						$chunk = $this->getLevel()->getChunk($this->getX() >> 4, $this->getZ() >> 4);

						$entity = Entity::createEntity("IronGolem", $chunk, $nbt);
						$entity->spawnToAll();
					}
				}else if($this->meta == 1 || $this->meta == 3){
					if($this->getLevel()->getBlock($this->add(0, -1, 1))->getId() == Block::IRON_BLOCK && $this->getLevel()->getBlock($this->add(0, -1, -1))->getId() == Block::IRON_BLOCK){
						$this->getLevel()->setBlock($this, new Air(), true, true);
						$this->getLevel()->setBlock($this->add(0, -1, 0), new Air(), true, true);
						$this->getLevel()->setBlock($this->add(0, -2, 0), new Air(), true, true);
						$this->getLevel()->setBlock($this->add(0, -1, 1), new Air(), true, true);
						$this->getLevel()->setBlock($this->add(0, -1, -1), new Air(), true, true);
						$yaw = 0;
						if($this->meta == 1) $yaw = 90;
						if($this->meta == 3) $yaw = 270;
						$nbt = new CompoundTag("", [
							"Pos" => new ListTag("Pos", [
								new DoubleTag("", $this->getX() + 0.5),
								new DoubleTag("", $this->getY()),
								new DoubleTag("", $this->getZ() + 0.5)
							]),
							"Motion" => new ListTag("Motion", [
								new DoubleTag("", 0),
								new DoubleTag("", 0),
								new DoubleTag("", 0)
							]),
							"Rotation" => new ListTag("Rotation", [
								new FloatTag("", $yaw),
								new FloatTag("", 0)
							]),
						]);

						$chunk = $this->getLevel()->getChunk($this->getX() >> 4, $this->getZ() >> 4);

						$entity = Entity::createEntity("IronGolem", $chunk, $nbt);
						$entity->spawnToAll();
					}
				}
			}else if($down == Block::SNOW_BLOCK && $tdown == Block::SNOW_BLOCK){
				$this->getLevel()->setBlock($this, new Air(), true, true);
				$this->getLevel()->setBlock($this->add(0, -1, 0), new Air(), true, true);
				$this->getLevel()->setBlock($this->add(0, -2, 0), new Air(), true, true);
				$yaw = 0;
				if($this->meta == 0) $yaw = 0;
				if($this->meta == 1) $yaw = 90;
				if($this->meta == 2) $yaw = 180;
				if($this->meta == 3) $yaw = 270;
				$nbt = new CompoundTag("", [
					"Pos" => new ListTag("Pos", [
						new DoubleTag("", $this->getX() + 0.5),
						new DoubleTag("", $this->getY()),
						new DoubleTag("", $this->getZ() + 0.5)
					]),
					"Motion" => new ListTag("Motion", [
						new DoubleTag("", 0),
						new DoubleTag("", 0),
						new DoubleTag("", 0)
					]),
					"Rotation" => new ListTag("Rotation", [
						new FloatTag("", $yaw),
						new FloatTag("", 0)
					]),
				]);

				$chunk = $this->getLevel()->getChunk($this->getX() >> 4, $this->getZ() >> 4);

				$entity = Entity::createEntity("SnowGolem", $chunk, $nbt);
				$entity->spawnToAll();
			}
		}

		return true;
	}

}