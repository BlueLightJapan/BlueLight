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
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/

namespace pocketmine\entity;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\level\MovingObjectPosition;
use pocketmine\math\Vector3;
use pocketmine\block\Rail;
use pocketmine\block\Block;
use pocketmine\Server;
use pocketmine\Player;

class Minecart extends Vehicle{

	const NETWORK_ID = 84;

	const TYPE_NORMAL = 1;
	const TYPE_CHEST = 2;
	const TYPE_HOPPER = 3;
	const TYPE_TNT = 4;

	public $height = 0.7;
	public $width = 0.98;
	public $isInReverse = false;
	public $matrix = [[[0, 0, -1], [0, 0, 1]], [[ -1, 0, 0], [1, 0, 0]], [[ -1, -1, 0], [1, 0, 0]], [[ -1, 0, 0], [1, -1, 0]], [[0, 0, -1], [0, -1, 1]], [[0, -1, -1], [0, 0, 1]], [[0, 0, 1], [1, 0, 0]], [[0, 0, 1], [ -1, 0, 0]], [[0, 0, -1], [ -1, 0, 0]], [[0, 0, -1], [1, 0, 0]]];
	public $minecartX;
	public $minecartY;
	public $minecartZ;
	public $minecartYaw;
	public $minecraftPitch;
	public $prevPosX;
	public $prevPosY;
	public $prevPosZ;
	public $prevRotationYaw;
	public $prevRotationPitch;

	public function getName(){
		return "Minecart";
	}

	public function getType(){
		return self::TYPE_NORMAL;
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = 0;
		$pk->speedY = 0;
		$pk->speedZ = 0;
		$pk->yaw = 0;
		$pk->pitch = 0;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);
		$this->prevPosX = $this->x;
		$this->prevPosY = $this->y;
		$this->prevPosZ = $this->z;
		$this->motionY -= 0.03999999910593033;
		$k = floor($this->x);
		$l = floor($this->y);
		$i1 = floor($this->z);

		if($this->level->getBlock(new Vector3($k, $l - 1, $i1)) instanceof Rail){
			$l--;
		}

		$blockpos = new Vector3($k, $l, $i1);
		$block = $this->level->getBlock($blockpos);
		if ($block instanceof Rail){
			$this->onRail($blockpos, $block);
		}else{
			$this->moveDerailedMinecart();
		}

		$this->pitch = 0.0;
		$d0 = $this->prevPosX - $this->x;
		$d2 = $this->prevPosZ - $this->z;

		if ($d0 * $d0 + $d2 * $d2 > 0.001){
			$this->yaw = atan2($d2, $d0) * 180.0 / M_PI;

			if ($this->isInReverse){
				$this->yaw += 180.0;
			}
		}

		$d3 = $this->wrapAngleTo180($this->yaw - $this->prevRotationYaw);

		if ($d3 < -170.0 || $d3 >= 170.0){
			$this->yaw += 180.0;
			$this->isInReverse = !$this->isInReverse;
		}

		$this->setRotation($this->yaw, $this->pitch);

		$bb = clone $this->getBoundingBox();
		$list = $this->getLevel()->getCollidingEntities($bb->expand(0.20000000298023224, 0.0, 0.20000000298023224), $this);

		foreach($list as $entity){
			if($entity != $this->ridingEntity && $entity instanceof Minecart){
				$this->applyEntityCollision($entity);
			}
		}

		if ($this->ridingEntity != null && !$this->ridingEntity->isAlive()){
			$this->ridingEntity = null;
		}

		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();

		$this->timings->stopTiming();
		return true;
	}

	public function onRail($blockpos, $block){
		$this->fallDistance = 0.0;
		$vec3 = $this->func_70489_a($this->x, $this->y, $this->z);
		$this->y = $blockpos->getY();
		$flag = false;
		$flag1 = false;

		if ($block->getId() == Block::POWERED_RAIL){
			$flag = $block->getDamage() >= 8;
			$flag1 = !$flag;
		}

		$d0 = 0.0078125;

		$damage = $block->getDamage();
		if ($block->getId() == Block::POWERED_RAIL && $block->isPowered()) $damage -= 8;
		switch ($damage){
			case Rail::SLOPED_ASCENDING_EAST:
				$this->motionX -= 0.0078125;
				$this->y++;
				break;
			case Rail::SLOPED_ASCENDING_WEST:
				$this->motionX += 0.0078125;
				$this->y++;
				break;
			case Rail::SLOPED_ASCENDING_NORTH:
				$this->motionZ += 0.0078125;
				$this->y++;
				break;
			case Rail::SLOPED_ASCENDING_SOUTH:
				$this->motionZ -= 0.0078125;
				$this->y++;
		}

		$aint = $this->matrix[$damage];
		$d1 = $aint[1][0] - $aint[0][0];
		$d2 = $aint[1][2] - $aint[0][2];
		$d3 = sqrt($d1 * $d1 + $d2 * $d2);
		$d4 = $this->motionX * $d1 + $this->motionZ * $d2;

		if ($d4 < 0.0){
			$d1 = -$d1;
			$d2 = -$d2;
		}

		$d5 = sqrt($this->motionX * $this->motionX + $this->motionZ * $this->motionZ);

		if ($d5 > 2.0){
			$d5 = 2.0;
		}

		$this->motionX = $d5 * $d1 / $d3;
		$this->motionZ = $d5 * $d2 / $d3;

		if ($this->ridingEntity instanceof Entity){
			$d6 = $this->ridingEntity->moveForward;
			$this->ridingEntity->moveForward = 0;

			if ($d6 > 0.0){
				$d7 = -sin($this->ridingEntity->yaw * M_PI / 180.0);
				$d8 = cos($this->ridingEntity->yaw * M_PI / 180.0);
				$d9 = $this->motionX * $this->motionX + $this->motionZ * $this->motionZ;

				if ($d9 < 0.01){
					$this->motionX += $d7 * 0.1;
					$this->motionZ += $d8 * 0.1;
					$flag1 = false;
				}
			}
		}

		if ($flag1){
			$d17 = sqrt($this->motionX * $this->motionX + $this->motionZ * $this->motionZ);

			if ($d17 < 0.03){
				$this->motionX *= 0.0;
				$this->motionY *= 0.0;
				$this->motionZ *= 0.0;
			}else{
				$this->motionX *= 0.5;
				$this->motionY *= 0.0;
				$this->motionZ *= 0.5;
			}
		}

		$d18 = 0.0;
		$d19 = $blockpos->getX() + 0.5 + $aint[0][0] * 0.5;
		$d20 = $blockpos->getZ() + 0.5 + $aint[0][2] * 0.5;
		$d21 = $blockpos->getX() + 0.5 + $aint[1][0] * 0.5;
		$d10 = $blockpos->getZ() + 0.5 + $aint[1][2] * 0.5;
		$d1 = $d21 - $d19;
		$d2 = $d10 - $d20;

		if ($d1 == 0.0){
			$this->x = $blockpos->getX() + 0.5;
			$d18 = $this->z - $blockpos->getZ();
		}else if ($d2 == 0.0){
			$this->z = $blockpos->getZ() + 0.5;
			$d18 = $this->x - $blockpos->getX();
		}else{
			$d11 = $this->x - $d19;
			$d12 = $this->z - $d20;
			$d18 = ($d11 * $d1 + $d12 * $d2) * 2.0;
		}

		$this->x = $d19 + $d1 * $d18;
		$this->z = $d20 + $d2 * $d18;
		$this->setPosition(new Vector3($this->x, $this->y, $this->z));
		$d22 = $this->motionX;
		$d23 = $this->motionZ;

		if ($this->ridingEntity != null){
			$d22 *= 0.75;
			$d23 *= 0.75;
		}

		$d13 = $this->getMaximumSpeed();
		$d22 = $this->clamp($d22, -$d13, $d13);
		$d23 = $this->clamp($d23, -$d13, $d13);
		$this->move($d22, 0.0, $d23);

		if ($aint[0][1] != 0 && floor($this->x) - $blockpos->getX() == $aint[0][0] && floor($this->z) - $blockpos->getZ() == $aint[0][2]){
			$this->setPosition(new Vector3($this->x, $this->y + $aint[0][1], $this->z));
		}else if ($aint[1][1] != 0 && floor($this->x) - $blockpos->getX() == $aint[1][0] && floor($this->z) - $blockpos->getZ() == $aint[1][2]){
			$this->setPosition(new Vector3($this->x, $this->y + $aint[1][1], $this->z));
		}

		$this->applyDrag();
		$vec31 = $this->func_70489_a($this->x, $this->y, $this->z);

		if ($vec31 != null && $vec3 != null){
			$d14 = ($vec3->y - $vec31->y) * 0.05;
			$d5 = sqrt($this->motionX * $this->motionX + $this->motionZ * $this->motionZ);

			if ($d5 > 0.0){
				$this->motionX = $this->motionX / $d5 * ($d5 + $d14);
				$this->motionZ = $this->motionZ / $d5 * ($d5 + $d14);
			}

			$this->setPosition(new Vector3($this->x, $vec31->y, $this->z));
		}

		$j = floor($this->x);
		$i = floor($this->z);

		if ($j != $blockpos->getX() || $i != $blockpos->getZ()){
			$d5 = sqrt($this->motionX * $this->motionX + $this->motionZ * $this->motionZ);
			$this->motionX = $d5 * ($j - $blockpos->getX());
			$this->motionZ = $d5 * ($i - $blockpos->getZ());
		}

		if ($flag){
			$d15 = sqrt($this->motionX * $this->motionX + $this->motionZ * $this->motionZ);

			$damage = $block->getDamage();
			if ($block->getId() == Block::POWERED_RAIL && $block->isPowered()) $damage -= 8;

			if ($d15 > 0.01){
				$d16 = 0.06;
				$this->motionX += $this->motionX / $d15 * $d16;
				$this->motionZ += $this->motionZ / $d15 * $d16;
			}else if ($damage == Rail::STRAIGHT_EAST_WEST){
				if ($this->level->getBlock($blockpos->getSide(Vector3::SIDE_WEST))->isSolid()){
					$this->motionX = 0.02;
				}else if ($this->level->getBlock($blockpos->getSide(Vector3::SIDE_EAST))->isSolid()){
					$this->motionX = -0.02;
				}
			}else if ($damage == Rail::STRAIGHT_NORTH_SOUTH){
				if ($this->level->getBlock($blockpos->getSide(Vector3::SIDE_NORTH))->isSolid()){
					$this->motionZ = 0.02;
				}else if ($this->level->getBlock($blockpos->getSide(Vector3::SIDE_SOUTH))->isSolid()){
					$this->motionZ = -0.02;
				}
			}
		}
	}

	public function func_70489_a($x, $y, $z){
		$i = floor($x);
		$j = floor($y);
		$k = floor($z);

		if($this->level->getBlock(new Vector3($i, $j - 1, $k)) instanceof Rail){
			$j--;
		}

		$block = $this->level->getBlock(new Vector3($i, $j, $k));

		if ($block instanceof Rail){

			$damage = $block->getDamage();
			if ($block->getId() == Block::POWERED_RAIL && $block->isPowered()) $damage -= 8;
			$aint = $this->matrix[$damage];
			$d0 = 0.0;
			$d1 = $i + 0.5 + $aint[0][0] * 0.5;
			$d2 = $j + 0.0625 + $aint[0][1] * 0.5;
			$d3 = $k + 0.5 + $aint[0][2] * 0.5;
			$d4 = $i + 0.5 + $aint[1][0] * 0.5;
			$d5 = $j + 0.0625 + $aint[1][1] * 0.5;
			$d6 = $k + 0.5 + $aint[1][2] * 0.5;
			$d7 = $d4 - $d1;
			$d8 = ($d5 - $d2) * 2.0;
			$d9 = $d6 - $d3;

			if ($d7 == 0.0){
				$x = $i + 0.5;
				$d0 = $z - $k;
			}else if ($d9 == 0.0){
				$z = $k + 0.5;
				$d0 = $x - $i;
			}else{
				$d10 = $x - $d1;
				$d11 = $z - $d3;
				$d0 = ($d10 * $d7 + $d11 * $d9) * 2.0;
			}

			$x = $d1 + $d7 * $d0;
			$y = $d2 + $d8 * $d0;
			$z = $d3 + $d9 * $d0;

			if ($d8 < 0.0){
				$y++;
			}

			if ($d8 > 0.0){
				$y += 0.5;
			}

			return new Vector3($x, $y, $z);
		}else{
			return null;
		}
	}

	public function applyDrag(){
		if ($this->ridingEntity != null){
			$this->motionX *= 0.996999979019165;
			$this->motionY *= 0.0;
			$this->motionZ *= 0.996999979019165;
		}else{
			$this->motionX *= 0.9599999785423279;
			$this->motionY *= 0.0;
			$this->motionZ *= 0.9599999785423279;
		}
	}

	public function getMaximumSpeed(){
		return 0.4;
	}

	public function moveDerailedMinecart(){
		$d0 = $this->getMaximumSpeed();
		$this->motionX = $this->clamp($this->motionX, -$d0, $d0);
		$this->motionZ = $this->clamp($this->motionZ, -$d0, $d0);

		if ($this->onGround){
			$this->motionX *= 0.5;
			$this->motionY *= 0.5;
			$this->motionZ *= 0.5;
		}

		$this->move($this->motionX, $this->motionY, $this->motionZ);

		if (!$this->onGround){
			$this->motionX *= 0.949999988079071;
			$this->motionY *= 0.949999988079071;
			$this->motionZ *= 0.949999988079071;
		}
	}

	public function clamp($num, $min, $max){
		return $num < $min ? $min : ($num > $max ? $max : $num);
	}

	public function applyEntityCollision($entityIn){
		if ($entityIn != $this->ridingEntity){
			if ($entityIn instanceof Living && !($entityIn instanceof Player) && !($entityIn instanceof IronGolem) && $this->getType() == self::TYPE_NORMAL && $this->motionX * $this->motionX + $this->motionZ * $this->motionZ > 0.01 && $this->ridingEntity == null && $entityIn->ridingEntity == null){
				$entityIn->setLink($this);
 			}

			$d0 = $entityIn->x - $this->x;
			$d1 = $entityIn->z - $this->z;
			$d2 = $d0 * $d0 + $d1 * $d1;

			if ($d2 >= 9.999999747378752E-5){
				$d2 = sqrt($d2);
				$d0 = $d0 / $d2;
				$d1 = $d1 / $d2;
				$d3 = 1.0 / $d2;

				if ($d3 > 1.0){
					$d3 = 1.0;
				}

				$d0 = $d0 * $d3;
				$d1 = $d1 * $d3;
				$d0 = $d0 * 0.10000000149011612;
				$d1 = $d1 * 0.10000000149011612;
				$d0 = $d0 * (1.0 - 0);
				$d1 = $d1 * (1.0 - 0);
				$d0 = $d0 * 0.5;
				$d1 = $d1 * 0.5;

				if ($entityIn instanceof Minecart){
					$d4 = $entityIn->x - $this->x;
					$d5 = $entityIn->z - $this->z;
					$vec3 = (new Vector3($d4, 0.0, $d5))->normalize();
					$vec31 = (new Vector3(cos($this->yaw * M_PI / 180.0), 0.0, sin($this->yaw * M_PI / 180.0)))->normalize();
					$d6 = abs($vec3->dot($vec31));

					if ($d6 < 0.800000011920929){
						return;
					}

 					$d7 = $entityIn->motionX + $this->motionX;
					$d8 = $entityIn->motionZ + $this->motionZ;
					$d7 = $d7 / 2.0;
					$d8 = $d8 / 2.0;
					$this->motionX *= 0.20000000298023224;
					$this->motionZ *= 0.20000000298023224;
					$this->motionX += $d7 - $d0;
					$this->motionZ += $d8 - $d1;
					$entityIn->motionX *= 0.20000000298023224;
					$entityIn->motionZ *= 0.20000000298023224;
					$entityIn->motionX += $d7 + $d0;
					$entityIn->motionZ += $d8 + $d1;
				}else{
					$this->motionX += -$d0;
					$this->motionZ += -$d1;
					$entityIn->motionX += $d0 / 4.0;
					$entityIn->motionZ += $d1 / 4.0;
				}
			}
		}
	}

	public function attack($damage, EntityDamageEvent $source){
		parent::attack($damage, $source);
		if($source->isCancelled()) return false;

		$flag = $source instanceof EntityDamageByEntityEvent && $source->getDamager() instanceof Player && $source->getDamager()->isCreative();
		if($flag){
			$this->kill();
			$this->close();
		}else{
			//$pk = new EntityEventPacket();
			//$pk->eid = $this->getId();
			//$pk->event = EntityEventPacket::HURT_ANIMATION;
			//Server::getInstance()->broadcastPacket($this->getViewers(), $pk);
		}
	}

	public function getRidePosition(){
		return [0, 1, 0];
	}
}
