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
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/

namespace pocketmine\entity;

use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;

abstract class Animal extends Creature implements Ageable{

	protected $growingAge;
	protected $forcedAge;
	protected $c;
	private $ageWidth = -1.0;
	private $ageHeight;

	private $inLove;
	private $playerInLove;

	protected function initEntity(){
		parent::initEntity();

		if(!isset($this->namedtag->Age) or !($this->namedtag->Age instanceof IntTag)){
			$this->namedtag->Age = new IntTag("Age", 0);
		}
		if(!isset($this->namedtag->ForcedAge) or !($this->namedtag->ForcedAge instanceof IntTag)){
			$this->namedtag->ForcedAge = new IntTag("ForcedAge", 0);
		}
		if(!isset($this->namedtag->inLove) or !($this->namedtag->inLove instanceof IntTag)){
			$this->namedtag->inLove = new IntTag("inLove", 0);
		}
		$this->setGrowingAge($this->namedtag["Age"]);
		$this->forcedAge = $this->namedtag["ForcedAge"];
		$this->inLove = $this->namedtag["inLove"];
	}

	public function onRightClick(Player $player){
		$item = $player->getInventory()->getItemInHand();
		if($this->isBaby()){
			$this->onGowingUpdate(((-$this->getGrowingAge() / 20) * 0.1), true);
			return;
		}else{
			$this->setInLove($player);
		}
		parent::onRightClick($player);
	}

	public function updateAITasks(){
		if ($this->getGrowingAge() != 0){
			$this->inLove = 0;
		}

		parent::updateAITasks();
	}

	public function entityBaseTick($tickDiff = 1){
		parent::entityBaseTick($tickDiff);
		$i = $this->getGrowingAge();

		if ($i < 0){
			++$i;
			$this->setGrowingAge($i);

			if ($i == 0){
				$this->onGrowingAdult();
			}
		}else if ($i > 0){
			--$i;
			$this->setGrowingAge($i);
		}

		if ($this->getGrowingAge() != 0){
			$this->inLove = 0;
		}

		if ($this->inLove > 0){
			--$this->inLove;
		}
	}

	protected function onGrowingAdult(){
	}

	public function setScaleForAge(bool $isBaby){
		//$this->setScale($isBaby ? 0.5 : 1.0);
		$this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_BABY, $isBaby);
	}

	/*protected function setSize(float $width, float $height){
		$flag = $this->ageWidth > 0.0F;
		$this->ageWidth = $width;
		$this->ageHeight = $height;

		if (!$flag){
			$this->setScale(1.0F);
		}
	}

	protected function setScale(float $scale){
		parent::setSize($this->ageWidth * $scale, $this->ageHeight * $scale);
	}*/

	public function isBaby(){
		return $this->getGrowingAge() < 0;
	}

	public abstract function createChild($ageable);

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->Age = new IntTag("Age", $this->getGrowingAge());
		$this->namedtag->FrocedAge = new IntTag("ForcedAge", $this->forcedAge);
		$this->namedtag->inLove = new IntTag("inLove", $this->inLove);
	}

	public function getGrowingAge() : int{
		return $this->growingAge;
	}

	public function onGrowingUpdate(int $growAge, bool $isByPlayer){
		$i = $this->getGrowingAge();
		$j = $i;
		$i = $i + $growAge * 20;

		if ($i > 0){
			$i = 0;

			if ($j < 0){
				$this->onGrowingAdult();
			}
		}

		$k = $i - $j;
		$this->setGrowingAge($i);

		if ($isByPlayer){
			$this->forcedAge += $k;

			if ($this->c == 0){
				$this->c = 40;
			}
		}

		if ($this->getGrowingAge() == 0){
			$this->setGrowingAge($this->forcedAge);
		}
	}

	public function setInLove($player){
		$this->inLove = 600;
		$this->playerInLove = $player;
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INLOVE, true);
	}

	public function getPlayerInLove(){
		return $this->playerInLove;
	}

	public function isInLove(): bool{
		return $this->inLove > 0;
	}

	public function resetInLove(){
		$this->inLove = 0;
	}

	public function canMateWith($otherAnimal){
		return $otherAnimal == $this ? false : (get_class($otherAnimal) != get_class($this) ? false : $this->isInLove() && $otherAnimal->isInLove());
	}

	public function addGrowth(int $growth){
		$this->onGrowingUpdate($growth, false);
	}

	public function setGrowingAge(int $age){
		$this->growingAge = $age;
		$this->setScaleForAge($this->isBaby());
	}
}