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

declare(strict_types=1);

namespace pocketmine\level\light;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;

class ChunkLightPopulator{
	/** @var ChunkManager */
	protected $level;

	/** @var int */
	protected $chunkX;
	/** @var int */
	protected $chunkZ;

	protected $blockLightUpdates = null;
	protected $skyLightUpdates = null;

	public function __construct(ChunkManager $level, int $chunkX, int $chunkZ){
		$this->level = $level;
		$this->chunkX = $chunkX;
		$this->chunkZ = $chunkZ;

		$this->blockLightUpdates = new BlockLightUpdate($level);
		$this->skyLightUpdates = new SkyLightUpdate($level);
	}

	public function populate(){
		$chunk = $this->level->getChunk($this->chunkX, $this->chunkZ);

		$chunk->setAllBlockSkyLight(0);
		$chunk->setAllBlockLight(0);

		$maxY = $chunk->getMaxY();

		$realX = $this->chunkX << 4;
		$realZ = $this->chunkZ << 4;
		for($x = 0; $x < 16; ++$x){
			for($z = 0; $z < 16; ++$z){
				$heightMap = $chunk->getHeightMap($x, $z);

				for($y = $maxY; $y >= 0; --$y){
					if($y >= $heightMap){
						if(
							$y === $heightMap or
							($x < 15 and $y < $chunk->getHeightMap($x + 1, $z)) or
							($x > 0 and $y < $chunk->getHeightMap($x - 1, $z)) or
							($z < 15 and $y < $chunk->getHeightMap($x, $z + 1)) or
							($z > 0 and $y < $chunk->getHeightMap($x, $z - 1))
						){
							$this->skyLightUpdates->setAndUpdateLight($realX + $x, $y, $realZ + $z, 15);
						}else{
							$chunk->setBlockSkyLight($x, $y, $z, 15);
						}
					}

					if(($blockLight = Block::$light[$chunk->getBlockId($x, $y, $z)]) > 0){
						$this->blockLightUpdates->setAndUpdateLight($realX + $x, $y, $realZ + $z, $blockLight);
						//$chunk->setBlockLight($x, $y, $z, $blockLight);
					}
				}
			}
		}

		$this->blockLightUpdates->execute();
		$this->skyLightUpdates->execute();
	}
}