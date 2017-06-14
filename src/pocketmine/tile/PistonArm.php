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

namespace pocketmine\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class PistonArm extends Spawnable{

	public function __construct(Level $level, CompoundTag $nbt){
        if(!isset($nbt->Sticky)){
			$nbt->Sticky = new ByteTag("Sticky", (bool) false);
        }
		parent::__construct($level, $nbt);
	}

    public function getSpawnCompound(){
        /*if($this->extended){
            $c = new CompoundTag("", [
                new CompoundTag("AttachedBlocks", []),
                new CompoundTag("BreakBlocks", []),
                new FloatTag("LastProgress", 1.0),
                new ByteTag("NewState", 2),
                new FloatTag("Progress", 1.0),
                new ByteTag("State", 2),
                new ByteTag("Sticky", (bool) false),
                new StringTag("id", Tile::PISTON),
                new ByteTag("isMovable", (bool) true),
                new IntTag("x", (int) $this->x),
                new IntTag("y", (int) $this->y),
                new IntTag("z", (int) $this->z),
            ]);
        }else{*/
        $c = new CompoundTag("", [
            new CompoundTag("AttachedBlocks", []),
            new CompoundTag("BreakBlocks", []),
            new FloatTag("LastProgress", 0.0),
            new ByteTag("NewState", 0),
            new FloatTag("Progress", 0.0),
            new ByteTag("State", 0),
            $this->namedtag->Sticky,
            new StringTag("id", Tile::PISTON),
            new ByteTag("isMovable", (bool) true),
            new IntTag("x", (int) $this->x),
            new IntTag("y", (int) $this->y),
            new IntTag("z", (int) $this->z),
        ]);
        #}

        return $c;
    }
}
