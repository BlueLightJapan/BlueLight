<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\ButtonClickSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

class WoodenButton extends Flowable{
	protected $id = self::WOODEN_BUTTON;

	public $isActivated = false;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function onUpdate($type){
		if($type == Level::BLOCK_UPDATE_SCHEDULED){
			if($this->isActivated()) {
				$this->meta ^= 0x08;
				$this->getLevel()->setBlock($this, $this, true, false);
				$this->getLevel()->addSound(new ButtonClickSound($this));

			}
			return Level::BLOCK_UPDATE_SCHEDULED;
		}
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$side = $this->getDamage();
			if($this->isActivated()) $side ^= 0x08;
			$faces = [
				0 => 1,
				1 => 0,
				2 => 3,
				3 => 2,
				4 => 5,
				5 => 4,
			];

			if($this->getSide($faces[$side]) instanceof Transparent){
				$this->getLevel()->useBreakOn($this);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		return false;
	}

	public function getName(){
		return "Wooden Button";
	}

	public function getHardness() {
		return 0.5;
	}

	public function onBreak(Item $item){
		if($this->isActivated()){
			$this->meta ^= 0x08;
			$this->getLevel()->setBlock($this, $this, true, false);
		}
		$this->getLevel()->setBlock($this, new Air(), true, false);
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($target->isTransparent() === false){
			$this->meta = $face;
			$this->getLevel()->setBlock($block, $this, true, false);
			return true;
		}
		return false;
	}

	public function canBeActivated() : bool {
		return true;
	}

	public function isActivated(Block $from = null){
		return (($this->meta & 0x08) === 0x08);
	}

	public function onActivate(Item $item, Player $player = null){
		if(!$this->isActivated()){
			$this->meta ^= 0x08;
			$this->getLevel()->setBlock($this, $this, true, false);
			$this->getLevel()->addSound(new ButtonClickSound($this));
			$this->getLevel()->scheduleUpdate($this, 30);
		}
		return true;
	}
}
