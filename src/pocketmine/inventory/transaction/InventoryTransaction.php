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

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\MainLogger;

/**
 * This InventoryTransaction only allows doing Transaction between one / two inventories
 */
class InventoryTransaction{
	/** @var float */
	private $creationTime;
	protected $hasExecuted = false;
	/** @var Player */
	protected $source;

	/** @var Inventory[] */
	protected $inventories = [];

	/** @var InventoryAction[] */
	protected $actions = [];

	/**
	 * @param Player            $source
	 * @param InventoryAction[] $actions
	 */
	public function __construct(Player $source, array $actions = []){
		$this->creationTime = microtime(true);
		$this->source = $source;
		foreach($actions as $action){
			$this->addAction($action);
		}
	}

	/**
	 * @return Player
	 */
	public function getSource() : Player{
		return $this->source;
	}

	public function getCreationTime() : float{
		return $this->creationTime;
	}

	/**
	 * @return Inventory[]
	 */
	public function getInventories() : array{
		return $this->inventories;
	}

	/**
	 * @return InventoryAction[]
	 */
	public function getActions() : array{
		return $this->actions;
	}

	/**
	 * @param InventoryAction $action
	 */
	public function addAction(InventoryAction $action) : void{
		if(!isset($this->actions[$hash = spl_object_hash($action)])){
			$this->actions[spl_object_hash($action)] = $action;
			$action->onAddToTransaction($this);
		}else{
			throw new \InvalidArgumentException("Tried to add the same action to a transaction twice");
		}
	}

	/**
	 * @internal This method should not be used by plugins, it's used to add tracked inventories for InventoryActions
	 * involving inventories.
	 *
	 * @param Inventory $inventory
	 */
	public function addInventory(Inventory $inventory) : void{
		if(!isset($this->inventories[$hash = spl_object_hash($inventory)])){
			$this->inventories[$hash] = $inventory;
		}
	}

	/**
	 * @param Item[] $needItems
	 * @param Item[] $haveItems
	 *
	 * @return bool
	 */
	protected function matchItems(array &$needItems, array &$haveItems) : bool{
		foreach($this->actions as $key => $action){
			if(!$action->getTargetItem()->isNull()){
				$needItems[] = $action->getTargetItem();
			}

			if(!$action->isValid($this->source)){
				return false;
			}

			if(!$action->getSourceItem()->isNull()){
				$haveItems[] = $action->getSourceItem();
			}
		}

		foreach($needItems as $i => $needItem){
			foreach($haveItems as $j => $haveItem){
				if($needItem->equals($haveItem)){
					$amount = min($needItem->getCount(), $haveItem->getCount());
					$needItem->setCount($needItem->getCount() - $amount);
					$haveItem->setCount($haveItem->getCount() - $amount);
					if($haveItem->getCount() === 0){
						unset($haveItems[$j]);
					}
					if($needItem->getCount() === 0){
						unset($needItems[$i]);
						break;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Iterates over SlotChangeActions in this transaction and compacts any which refer to the same slot in the same
	 * inventory so they can be correctly handled.
	 *
	 * Under normal circumstances, the same slot would never be changed more than once in a single transaction. However,
	 * due to the way things like the crafting grid are "implemented" in MCPE 1.2 (a.k.a. hacked-in), we may get
	 * multiple slot changes referring to the same slot in a single transaction. These multiples are not even guaranteed
	 * to be in the correct order (slot splitting in the crafting grid for example, causes the actions to be sent in the
	 * wrong order), so this method also tries to chain them into order.
	 *
	 * @return bool
	 */
	protected function squashDuplicateSlotChanges() : bool{
		/** @var SlotChangeAction[][] $slotChanges */
		$slotChanges = [];
		foreach($this->actions as $key => $action){
			if($action instanceof SlotChangeAction){
				$slotChanges[spl_object_hash($action->getInventory()) . "@" . $action->getSlot()][] = $action;
			}
		}

		foreach($slotChanges as $hash => $list){
			if(count($list) === 1){ //No need to compact slot changes if there is only one on this slot
				unset($slotChanges[$hash]);
				continue;
			}

			$originalList = $list;

			/** @var SlotChangeAction|null $originalAction */
			$originalAction = null;
			/** @var Item|null $lastTargetItem */
			$lastTargetItem = null;

			foreach($list as $i => $action){
				if($action->isValid($this->source)){
					$originalAction = $action;
					$lastTargetItem = $action->getTargetItem();
					unset($list[$i]);
					break;
				}
			}

			if($originalAction === null){
				return false; //Couldn't find any actions that had a source-item matching the current inventory slot
			}

			do{
				$sortedThisLoop = 0;
				foreach($list as $i => $action){
					$actionSource = $action->getSourceItem();
					if($actionSource->equalsExact($lastTargetItem)){
						$lastTargetItem = $action->getTargetItem();
						unset($list[$i]);
						$sortedThisLoop++;
					}
				}
			}while($sortedThisLoop > 0);

			if(count($list) > 0){ //couldn't chain all the actions together
				MainLogger::getLogger()->debug("Failed to compact " . count($originalList) . " actions for " . $this->source->getName());
				return false;
			}

			foreach($originalList as $action){
				unset($this->actions[spl_object_hash($action)]);
			}

			$this->addAction(new SlotChangeAction($originalAction->getInventory(), $originalAction->getSlot(), $originalAction->getSourceItem(), $lastTargetItem));
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function canExecute() : bool{
		$this->squashDuplicateSlotChanges();

		$haveItems = [];
		$needItems = [];
		return $this->matchItems($needItems, $haveItems) and count($this->actions) > 0 and count($haveItems) === 0 and count($needItems) === 0;
	}

	protected function handleFailed() : void{
		foreach($this->actions as $action){
			$action->onExecuteFail($this->source);
		}
	}

	protected function callExecuteEvent() : bool{
		Server::getInstance()->getPluginManager()->callEvent($ev = new InventoryTransactionEvent($this));
		return !$ev->isCancelled();
	}

	/**
	 * @return bool
	 */
	public function execute() : bool{
		if($this->hasExecuted() or !$this->canExecute()){
			return false;
		}

		if(!$this->callExecuteEvent()){
			$this->handleFailed();
			return true;
		}

		foreach($this->actions as $action){
			if(!$action->onPreExecute($this->source)){
				$this->handleFailed();
				return true;
			}
		}

		foreach($this->actions as $action){
			if($action->execute($this->source)){
				$action->onExecuteSuccess($this->source);
			}else{
				$action->onExecuteFail($this->source);
			}
		}

		$this->hasExecuted = true;

		return true;
	}

	/**
	 * @return bool
	 */
	public function hasExecuted() : bool{
		return $this->hasExecuted;
	}
}
