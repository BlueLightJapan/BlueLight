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

namespace pocketmine\block;

use pocketmine\block\utils\PillarRotationHelper;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Quartz extends Solid{

	const NORMAL = 0;
	const CHISELED = 1;
	const PILLAR = 2;

	protected $id = self::QUARTZ_BLOCK;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 0.8;
	}

	public function getName() : string{
		static $names = [
			self::NORMAL => "Quartz Block",
			self::CHISELED => "Chiseled Quartz Block",
			self::PILLAR => "Quartz Pillar"
		];
		return $names[$this->meta & 0x03] ?? "Unknown";
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $facePos, Player $player = null) : bool{
		if($this->meta !== self::NORMAL){
			$this->meta = PillarRotationHelper::getMetaFromFace($this->meta, $face);
		}
		return $this->getLevel()->setBlock($blockReplace, $this, true, true);
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function getVariantBitmask() : int{
		return 0x03;
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return parent::getDrops($item);
		}

		return [];
	}
}