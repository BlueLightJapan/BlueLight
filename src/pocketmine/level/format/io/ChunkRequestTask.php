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

namespace pocketmine\level\format\io;

use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\nbt\NBT;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\FullChunkDataPacket;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\tile\Spawnable;

class ChunkRequestTask extends AsyncTask{

	protected $levelId;

	protected $chunk;
	protected $chunkX;
	protected $chunkZ;

	protected $tiles;

	protected $compressionLevel;

	public function __construct(Level $level, Chunk $chunk){
		$this->levelId = $level->getId();
		$this->compressionLevel = $level->getServer()->networkCompressionLevel;

		$this->chunk = $chunk->fastSerialize();
		$this->chunkX = $chunk->getX();
		$this->chunkZ = $chunk->getZ();

		//TODO: serialize tiles with chunks
		$tiles = "";
		$nbt = new NBT(NBT::LITTLE_ENDIAN);
		foreach($chunk->getTiles() as $tile){
			if($tile instanceof Spawnable){
				$nbt->setData($tile->getSpawnCompound());
				$tiles .= $nbt->write(true);
			}
		}

		$this->tiles = $tiles;
	}

	public function onRun(){
		$chunk = Chunk::fastDeserialize($this->chunk);

		$pk = new FullChunkDataPacket();
		$pk->chunkX = $this->chunkX;
		$pk->chunkZ = $this->chunkZ;
		$pk->data = $chunk->networkSerialize() . $this->tiles;

		$batch = new BatchPacket();
		$batch->addPacket($pk);
		$batch->compress($this->compressionLevel);
		$batch->encode();

		$this->setResult($batch->buffer, false);
	}

	public function onCompletion(Server $server){
		$level = $server->getLevel($this->levelId);
		if($level instanceof Level and $this->hasResult()){
			$batch = new BatchPacket($this->getResult());
			$batch->compressed = true;
			$batch->isEncoded = true;
			$level->chunkRequestCallback($this->chunkX, $this->chunkZ, $batch);
		}
	}

}