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

namespace pocketmine\inventory\transaction;

use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\inventory\BigCraftingGrid;
use pocketmine\inventory\CraftingRecipe;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;

class CraftingTransaction extends InventoryTransaction{

	protected $gridSize;
	/** @var Item[][] */
	protected $inputs;
	/** @var Item[][] */
	protected $secondaryOutputs;
	/** @var Item|null */
	protected $primaryOutput;

	/** @var CraftingRecipe|null */
	protected $recipe = null;

	public function __construct(Player $source, $actions = []){
		$this->gridSize = ($source->getCraftingGrid() instanceof BigCraftingGrid) ? 3 : 2;

		$air = ItemFactory::get(Item::AIR, 0, 0);
		$this->inputs = array_fill(0, $this->gridSize, array_fill(0, $this->gridSize, $air));
		$this->secondaryOutputs = array_fill(0, $this->gridSize, array_fill(0, $this->gridSize, $air));

		parent::__construct($source, $actions);
	}

	public function setInput(int $index, Item $item) : void{
		$y = (int) ($index / $this->gridSize);
		$x = $index % $this->gridSize;

		if($this->inputs[$y][$x]->isNull()){
			$this->inputs[$y][$x] = clone $item;
		}else{
			throw new \RuntimeException("Input $index has already been set");
		}
	}

	public function getInputMap() : array{
		return $this->inputs;
	}

	public function setExtraOutput(int $index, Item $item) : void{
		$y = (int) ($index / $this->gridSize);
		$x = $index % $this->gridSize;

		if($this->secondaryOutputs[$y][$x]->isNull()){
			$this->secondaryOutputs[$y][$x] = clone $item;
		}else{
			throw new \RuntimeException("Output $index has already been set");
		}
	}

	public function getPrimaryOutput() : ?Item{
		return $this->primaryOutput;
	}

	public function setPrimaryOutput(Item $item) : void{
		if($this->primaryOutput === null){
			$this->primaryOutput = clone $item;
		}else{
			throw new \RuntimeException("Primary result item has already been set");
		}
	}

	public function getRecipe() : ?CraftingRecipe{
		return $this->recipe;
	}

	private function reindexInputs() : array{
		$xOffset = $this->gridSize;
		$yOffset = $this->gridSize;

		$height = 0;
		$width = 0;

		foreach($this->inputs as $y => $row){
			foreach($row as $x => $item){
				if(!$item->isNull()){
					$xOffset = min($x, $xOffset);
					$yOffset = min($y, $yOffset);

					$height = max($y + 1 - $yOffset, $height);
					$width = max($x + 1 - $xOffset, $width);
				}
			}
		}

		if($height === 0 or $width === 0){
			return [];
		}

		$air = ItemFactory::get(Item::AIR, 0, 0);
		$reindexed = array_fill(0, $height, array_fill(0, $width, $air));
		foreach($reindexed as $y => $row){
			foreach($row as $x => $item){
				$reindexed[$y][$x] = $this->inputs[$y + $yOffset][$x + $xOffset];
			}
		}

		return $reindexed;
	}

	public function canExecute() : bool{
		$inputs = $this->reindexInputs();

		$this->recipe = $this->source->getServer()->getCraftingManager()->matchRecipe($inputs, $this->primaryOutput, $this->secondaryOutputs);

		return $this->recipe !== null and parent::canExecute();
	}

	protected function callExecuteEvent() : bool{
		$this->source->getServer()->getPluginManager()->callEvent($ev = new CraftItemEvent($this));
		return !$ev->isCancelled();
	}

	public function execute() : bool{
		if(parent::execute()){
			switch($this->primaryOutput->getId()){
				case Item::CRAFTING_TABLE:
					$this->source->awardAchievement("buildWorkBench");
					break;
				case Item::WOODEN_PICKAXE:
					$this->source->awardAchievement("buildPickaxe");
					break;
				case Item::FURNACE:
					$this->source->awardAchievement("buildFurnace");
					break;
				case Item::WOODEN_HOE:
					$this->source->awardAchievement("buildHoe");
					break;
				case Item::BREAD:
					$this->source->awardAchievement("makeBread");
					break;
				case Item::CAKE:
					$this->source->awardAchievement("bakeCake");
					break;
				case Item::STONE_PICKAXE:
				case Item::GOLDEN_PICKAXE:
				case Item::IRON_PICKAXE:
				case Item::DIAMOND_PICKAXE:
					$this->source->awardAchievement("buildBetterPickaxe");
					break;
				case Item::WOODEN_SWORD:
					$this->source->awardAchievement("buildSword");
					break;
				case Item::DIAMOND:
					$this->source->awardAchievement("diamond");
					break;
			}

			return true;
		}

		return false;
	}
}
