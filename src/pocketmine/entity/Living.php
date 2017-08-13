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

namespace pocketmine\entity;

use pocketmine\block\Block;
use pocketmine\entity\AI\EntityAITasks;
use pocketmine\entity\AI\EntityLookHelper;
use pocketmine\entity\AI\EntityMoveHelper;
use pocketmine\entity\AI\EntityJumpHelper;
use pocketmine\entity\AI\pathfinding\PathNavigateGround;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Timings;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\Player;
use pocketmine\utils\Binary;
use pocketmine\utils\BlockIterator;

abstract class Living extends Entity implements Damageable{

	protected $gravity = 0.08;
	protected $drag = 0.02;

	protected $attackTime = 0;

	protected $invisible = false;

	public $navigator;
	public $tasks;
	public $targetTasks;
	public $lookHelper;
	public $moveHelper;
	public $jumpHelper;
	public $isJumping = false;
	public $jumpMovementFactor = 0.02;
	private $jumpTicks = 0;
	public $moveForward = 0.0;
	public $moveStrafing = 0.0;
	public $landMovementFactor;
	private $attackTarget;
	private $entityLivingToAttack;
	private $revengeTimer = -1;
	private $recentlyHit = 0;
	private $attackingPlayer = null;

	protected $jumpVelocity = 0.42;

	/** @var Effect[] */
	protected $effects = [];

	abstract public function getName();

	protected function initEntity(){
		parent::initEntity();

		if(isset($this->namedtag->HealF)){
			$this->namedtag->Health = new FloatTag("Health", (float) $this->namedtag["HealF"]);
			unset($this->namedtag->HealF);
		}elseif(isset($this->namedtag->Health)){
			if(!($this->namedtag->Health instanceof FloatTag)){
				$this->namedtag->Health = new FloatTag("Health", (float) $this->namedtag->Health->getValue());
			}
		}else{
			$this->namedtag->Health = new FloatTag("Health", (float) $this->getMaxHealth());
		}

		$this->setHealth($this->namedtag["Health"]);

		if(isset($this->namedtag->ActiveEffects)){
			foreach($this->namedtag->ActiveEffects->getValue() as $e){
				$amplifier = Binary::unsignByte($e->Amplifier->getValue()); //0-255 only

				$effect = Effect::getEffect($e["Id"]);
				if($effect === null){
					continue;
				}

				$effect->setAmplifier($amplifier)->setDuration($e["Duration"])->setVisible($e["ShowParticles"] > 0);

				$this->addEffect($effect);
			}
		}
	}

	protected function addAttributes(){
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::HEALTH));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::FOLLOW_RANGE));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::KNOCKBACK_RESISTANCE));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::MOVEMENT_SPEED));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::ATTACK_DAMAGE));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::ABSORPTION));
	}

	public function setHealth($amount){
		$wasAlive = $this->isAlive();
		parent::setHealth($amount);
		$this->attributeMap->getAttribute(Attribute::HEALTH)->setValue($this->getHealth(), true);
		if($this->isAlive() and !$wasAlive){
			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->getId();
			$pk->event = EntityEventPacket::RESPAWN;
			$this->server->broadcastPacket($this->hasSpawned, $pk);
		}
	}

	public function getMaxHealth(){
		return $this->attributeMap->getAttribute(Attribute::HEALTH)->getMaxValue();
	}

	public function setMaxHealth($amount){
		$this->attributeMap->getAttribute(Attribute::HEALTH)->setMaxValue($amount);
	}

	public function getAbsorption() : float{
		return $this->attributeMap->getAttribute(Attribute::ABSORPTION)->getValue();
	}

	public function setAbsorption(float $absorption){
		$this->attributeMap->getAttribute(Attribute::ABSORPTION)->setValue($absorption);
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->Health = new FloatTag("Health", $this->getHealth());

		if(count($this->effects) > 0){
			$effects = [];
			foreach($this->effects as $effect){
				$effects[] = new CompoundTag("", [
					new ByteTag("Id", $effect->getId()),
					new ByteTag("Amplifier", Binary::signByte($effect->getAmplifier())),
					new IntTag("Duration", $effect->getDuration()),
					new ByteTag("Ambient", 0),
					new ByteTag("ShowParticles", $effect->isVisible() ? 1 : 0)
				]);
			}

			$this->namedtag->ActiveEffects = new ListTag("ActiveEffects", $effects);
		}else{
			unset($this->namedtag->ActiveEffects);
		}
	}


	public function hasLineOfSight(Entity $entity){
		//TODO: head height
		return true;
		//return $this->getLevel()->rayTraceBlocks(Vector3::createVector($this->x, $this->y + $this->height, $this->z), Vector3::createVector($entity->x, $entity->y + $entity->height, $entity->z)) === null;
	}

	public function heal($amount, EntityRegainHealthEvent $source){
		parent::heal($amount, $source);
		if($source->isCancelled()){
			return;
		}

		$this->attackTime = 0;
	}

	/**
	 * Returns an array of Effects currently active on the mob.
	 * @return Effect[]
	 */
	public function getEffects() : array{
		return $this->effects;
	}

	/**
	 * Removes all effects from the mob.
	 */
	public function removeAllEffects(){
		foreach($this->effects as $effect){
			$this->removeEffect($effect->getId());
		}
	}

	/**
	 * Removes the effect with the specified ID from the mob.
	 *
	 * @param int $effectId
	 */
	public function removeEffect(int $effectId){
		if(isset($this->effects[$effectId])){
			$effect = $this->effects[$effectId];
			unset($this->effects[$effectId]);
			$effect->remove($this);

			$this->recalculateEffectColor();
		}
	}

	/**
	 * Returns the effect instance active on this entity with the specified ID, or null if the mob does not have the
	 * effect.
	 *
	 * @param int $effectId
	 *
	 * @return Effect|null
	 */
	public function getEffect(int $effectId){
		return $this->effects[$effectId] ?? null;
	}

	/**
	 * Returns whether the specified effect is active on the mob.
	 *
	 * @param int $effectId
	 *
	 * @return bool
	 */
	public function hasEffect(int $effectId) : bool{
		return isset($this->effects[$effectId]);
	}

	/**
	 * Adds an effect to the mob.
	 * If a weaker effect of the same type is already applied, it will be replaced.
	 * If a weaker or equal-strength effect is already applied but has a shorter duration, it will be replaced.
	 *
	 * @param Effect $effect
	 */
	public function addEffect(Effect $effect){
		if(isset($this->effects[$effect->getId()])){
			$oldEffect = $this->effects[$effect->getId()];
			if(
				abs($effect->getAmplifier()) < $oldEffect->getAmplifier()
				or (abs($effect->getAmplifier()) === abs($oldEffect->getAmplifier()) and $effect->getDuration() < $oldEffect->getDuration())
			){
				return;
			}
			$effect->add($this, true, $oldEffect);
		}else{
			$effect->add($this, false);
		}

		$this->effects[$effect->getId()] = $effect;

		$this->recalculateEffectColor();
	}

	/**
	 * Recalculates the mob's potion bubbles colour based on the active effects.
	 */
	protected function recalculateEffectColor(){
		//TODO: add transparency values
		$color = [0, 0, 0]; //RGB
		$count = 0;
		$ambient = true;
		foreach($this->effects as $effect){
			if($effect->isVisible() and $effect->hasBubbles()){
				$c = $effect->getColor();
				$color[0] += $c[0] * $effect->getEffectLevel();
				$color[1] += $c[1] * $effect->getEffectLevel();
				$color[2] += $c[2] * $effect->getEffectLevel();
				$count += $effect->getEffectLevel();
				if(!$effect->isAmbient()){
					$ambient = false;
				}
			}
		}

		if($count > 0){
			$r = ($color[0] / $count) & 0xff;
			$g = ($color[1] / $count) & 0xff;
			$b = ($color[2] / $count) & 0xff;

			$this->setDataProperty(Entity::DATA_POTION_COLOR, Entity::DATA_TYPE_INT, 0xff000000 | ($r << 16) | ($g << 8) | $b);
			$this->setDataProperty(Entity::DATA_POTION_AMBIENT, Entity::DATA_TYPE_BYTE, $ambient ? 1 : 0);
		}else{
			$this->setDataProperty(Entity::DATA_POTION_COLOR, Entity::DATA_TYPE_INT, 0);
			$this->setDataProperty(Entity::DATA_POTION_AMBIENT, Entity::DATA_TYPE_BYTE, 0);
		}
	}

	/**
	 * Sends the mob's potion effects to the specified player.
	 * @param Player $player
	 */
	public function sendPotionEffects(Player $player){
		foreach($this->effects as $effect){
			$pk = new MobEffectPacket();
			$pk->entityRuntimeId = $this->id;
			$pk->effectId = $effect->getId();
			$pk->amplifier = $effect->getAmplifier();
			$pk->particles = $effect->isVisible();
			$pk->duration = $effect->getDuration();
			$pk->eventId = MobEffectPacket::EVENT_ADD;

			$player->dataPacket($pk);
		}
	}


	/**
	 * Returns the initial upwards velocity of a jumping entity in blocks/tick, including additional velocity due to effects.
	 * @return float
	 */
	public function getJumpVelocity() : float{
		return $this->jumpVelocity + ($this->hasEffect(Effect::JUMP) ? ($this->getEffect(Effect::JUMP)->getEffectLevel() / 10) : 0);
	}

	/**
	 * Called when the entity jumps from the ground. This method adds upwards velocity to the entity.
	 */
	public function jump(){
		if($this->onGround){
			$this->motionY = $this->getJumpVelocity(); //Y motion should already be 0 if we're jumping from the ground.
		}
	}

	public function fall(float $fallDistance){
		$damage = floor($fallDistance - 3 - ($this->hasEffect(Effect::JUMP) ? $this->getEffect(Effect::JUMP)->getEffectLevel() : 0));
		if($damage > 0){
			$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_FALL, $damage);
			$this->attack($ev->getFinalDamage(), $ev);
		}
	}

	public function attack($damage, EntityDamageEvent $source){
		if($this->attackTime > 0 or $this->noDamageTicks > 0){
			$lastCause = $this->getLastDamageCause();
			if($lastCause !== null and $lastCause->getDamage() >= $damage){
				$source->setCancelled();
			}
		}

		if($this->hasEffect(Effect::FIRE_RESISTANCE) and (
				$source->getCause() === EntityDamageEvent::CAUSE_FIRE
				or $source->getCause() === EntityDamageEvent::CAUSE_FIRE_TICK
				or $source->getCause() === EntityDamageEvent::CAUSE_LAVA
			)
		){
			$source->setCancelled();
		}

		if($this->hasEffect(Effect::DAMAGE_RESISTANCE)){
			$source->setDamage(-($source->getDamage(EntityDamageEvent::MODIFIER_BASE) * 0.20 * $this->getEffect(Effect::DAMAGE_RESISTANCE)->getEffectLevel()), EntityDamageEvent::MODIFIER_RESISTANCE);
		}

		parent::attack($damage, $source);

		if($source->isCancelled()){
			return;
		}

		if($source instanceof EntityDamageByEntityEvent){
			$e = $source->getDamager();
			if($source instanceof EntityDamageByChildEntityEvent){
				$e = $source->getChild();
			}

			if($e !== null){
				if($e->isOnFire() > 0){
					$this->setOnFire(2 * $this->server->getDifficulty());
				}

				$deltaX = $this->x - $e->x;
				$deltaZ = $this->z - $e->z;
				$this->knockBack($e, $damage, $deltaX, $deltaZ, $source->getKnockBack());
				if ($e instanceof Living){
					$this->setRevengeTarget($e);
				}

				if ($e instanceof Player){
					$this->recentlyHit = 100;
					$this->attackingPlayer = $e;
				}else if ($e instanceof Wolf){

					//if ($e->isTamed()){
					//	$this->recentlyHit = 100;
					//	$this->attackingPlayer = null;
					//}
				}

			}
		}

		$pk = new EntityEventPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->event = $this->getHealth() <= 0 ? EntityEventPacket::DEATH_ANIMATION : EntityEventPacket::HURT_ANIMATION; //Ouch!
		$this->server->broadcastPacket($this->hasSpawned, $pk);

		$this->attackTime = 10; //0.5 seconds cooldown
	}

	public function knockBack(Entity $attacker, $damage, $x, $z, $base = 0.4){
		$f = sqrt($x * $x + $z * $z);
		if($f <= 0){
			return;
		}

		$f = 1 / $f;

		$motion = new Vector3($this->motionX, $this->motionY, $this->motionZ);

		$motion->x /= 2;
		$motion->y /= 2;
		$motion->z /= 2;
		$motion->x += $x * $f * $base;
		$motion->y += $base;
		$motion->z += $z * $f * $base;

		if($motion->y > $base){
			$motion->y = $base;
		}

		$this->setMotion($motion);
	}

	public function kill(){
		if(!$this->isAlive()){
			return;
		}
		parent::kill();
		$this->callDeathEvent();
	}

	protected function callDeathEvent(){
		$this->server->getPluginManager()->callEvent($ev = new EntityDeathEvent($this, $this->getDrops()));
		foreach($ev->getDrops() as $item){
			$this->getLevel()->dropItem($this, $item);
		}
	}

	public function entityBaseTick($tickDiff = 1){
		Timings::$timerLivingEntityBaseTick->startTiming();
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_BREATHING, !$this->isInsideOfWater());
		if ($this->jumpTicks > 0){
			--$this->jumpTicks;
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->server->entityAIEnabled && !($this instanceof Human)){
			$this->updateEntityActionState();
			$this->moveStrafing *= 0.98;
			$this->moveForward *= 0.98;
			//$this->moveStrafing  = 0.01;
			//$this->moveForward = 0.05;
			$this->moveEntityWithHeading($this->moveStrafing, $this->moveForward);
			if ($this->recentlyHit > 0){
				--$this->recentlyHit;
			}else{
				$this->attackingPlayer = null;
			}
		}

		$this->doEffectsTick($tickDiff);

		if($this->isAlive()){
			if($this->isInsideOfSolid()){
				$hasUpdate = true;
				$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_SUFFOCATION, 1);
				$this->attack($ev->getFinalDamage(), $ev);
			}

			if(!$this->hasEffect(Effect::WATER_BREATHING) and $this->isInsideOfWater()){
				if($this instanceof WaterAnimal){
					$this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, 400);
				}else{
					$hasUpdate = true;
					$airTicks = $this->getDataProperty(self::DATA_AIR) - $tickDiff;
					if($airTicks <= -20){
						$airTicks = 0;

						$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_DROWNING, 2);
						$this->attack($ev->getFinalDamage(), $ev);
					}
					$this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, $airTicks);
				}
			}else{
				if($this instanceof WaterAnimal){
					$hasUpdate = true;
					$airTicks = $this->getDataProperty(self::DATA_AIR) - $tickDiff;
					if($airTicks <= -20){
						$airTicks = 0;

						$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_SUFFOCATION, 2);
						$this->attack($ev->getFinalDamage(), $ev);
					}
					$this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, $airTicks);
				}else{
					$this->setDataProperty(self::DATA_AIR, self::DATA_TYPE_SHORT, 400);
				}
			}

            if($this->server->entityAIEnabled){
                if ($this->isJumping){
                    if ($this->isInsideOfWater()){
                        $this->updateAITick();
                    }else if ($this->isInsideOfLava()){
                        $this->handleJumpLava();
                    }else if ($this->onGround && $this->jumpTicks == 0){
                        $this->doJump();
                        $this->jumpTicks = 10;
                    }
 			    }else{
                    $this->jumpTicks = 0;
                }
 		    }
		}
        if($this->server->entityAIEnabled && !($this instanceof Human)){
            if ($this->entityLivingToAttack != null){
                if (!$this->entityLivingToAttack->isAlive()){
                    $this->setRevengeTarget(null);
                }else if ($this->server->getTick() - $this->revengeTimer > 100){
                    $this->setRevengeTarget(null);
                }
 		    }
		}

		if($this->attackTime > 0){
			$this->attackTime -= $tickDiff;
		}

		Timings::$timerLivingEntityBaseTick->stopTiming();

		return $hasUpdate;
	}
	public function moveEntityWithHeading($strafe, $forward){
		if (!$this->isInsideOfWater() || $this instanceof Player && $this->isFlying()){
			if (!$this->isInsideOfLava() || $this instanceof Player && $this->isFlying()){
				$f4 = 0.91;
				if ($this->onGround){
					$f4 = 0.6;
				}
 				$f = 0.16277136 / ($f4 * $f4 * $f4);

				if ($this->onGround){
					 $f5 = $this->getAIMoveSpeed() * $f;
				}else{
 					$f5 = $this->jumpMovementFactor;
				}

				$this->moveFlying($strafe, $forward, $f5);
				$f4 = 0.91;
				if ($this->onGround){
					$f4 = 0.6;
				}

				if ($this->isOnLadder()){
					$f6 = 0.15;
					$this->motionX = $this->clamp($this->motionX, -$f6, $f6);
					$this->motionZ = $this->clamp($this->motionZ, -$f6, $f6);
					$this->fallDistance = 0.0;

					if ($this->motionY < -0.15){
						$this->motionY = -0.15;
					}

					$flag = $this->isSneaking() && $this instanceof Player;

					if ($flag && $this->motionY < 0.0){
						$this->motionY = 0.0;
					}

				}

 				$this->move($this->motionX, $this->motionY, $this->motionZ);

				if ($this->isCollidedHorizontally && $this->isOnLadder()){
					$this->motionY = 0.2;
 				}

				$this->motionY -= 0.08;

 				$this->motionY *= 0.9800000190734863;
 				$this->motionX *= $f4;
 				$this->motionZ *= $f4;
			}else{
				$d1 = $this->y;
				$this->moveFlying($strafe, $forward, 0.02);
				$this->move($this->motionX, $this->motionY, $this->motionZ);
				$this->motionX *= 0.5;
				$this->motionY *= 0.5;
				$this->motionZ *= 0.5;
				$this->motionY -= 0.02;
			}
 		}else{
			$d0 = $this->y;
			$f1 = 0.8;
			$f2 = 0.02;
			$f3 = 0;//水中移動のえんちゃんとレベル
			if ($f3 > 3.0){
				$f3 = 3.0;
			}
    			if (!$this->onGround){
				$f3 *= 0.5;
			}
 			if ($f3 > 0.0){
				$f1 += (0.54600006 - $f1) * $f3 / 3.0;
				$f2 += ($this->getAIMoveSpeed() * 1.0 - $f2) * $f3 / 3.0;
			}

			$this->moveFlying($strafe, $forward, $f2);
 			$this->move($this->motionX, $this->motionY, $this->motionZ);
 			$this->motionX *= $f1;
 			$this->motionY *= 0.800000011920929;
 			$this->motionZ *= $f1;
 			$this->motionY -= 0.02;
 		}
 	}

	protected function doEffectsTick(int $tickDiff = 1){
		if(count($this->effects) > 0){
			foreach($this->effects as $effect){
				if($effect->canTick()){
					$effect->applyEffect($this);
				}
				$effect->setDuration($effect->getDuration() - $tickDiff);
				if($effect->getDuration() <= 0){
					$this->removeEffect($effect->getId());
				}
			}
		}
	}

	protected function dealFireDamage(){
		if(!$this->hasEffect(Effect::FIRE_RESISTANCE)){
			parent::dealFireDamage();
		}
	}

	/**
	 * @return ItemItem[]
	 */
	public function getDrops() : array{
		return [];
	}

	/**
	 * @param int   $maxDistance
	 * @param int   $maxLength
	 * @param array $transparent
	 *
	 * @return Block[]
	 */
	public function getLineOfSight($maxDistance, $maxLength = 0, array $transparent = []){
		if($maxDistance > 120){
			$maxDistance = 120;
		}

		if(count($transparent) === 0){
			$transparent = null;
		}

		$blocks = [];
		$nextIndex = 0;

		$itr = new BlockIterator($this->level, $this->getPosition(), $this->getDirectionVector(), $this->getEyeHeight(), $maxDistance);

		while($itr->valid()){
			$itr->next();
			$block = $itr->current();
			$blocks[$nextIndex++] = $block;

			if($maxLength !== 0 and count($blocks) > $maxLength){
				array_shift($blocks);
				--$nextIndex;
			}

			$id = $block->getId();

			if($transparent === null){
				if($id !== 0){
					break;
				}
			}else{
				if(!isset($transparent[$id])){
					break;
				}
			}
		}

		return $blocks;
	}

	/**
	 * @param int   $maxDistance
	 * @param array $transparent
	 *
	 * @return Block|null
	 */
	public function getTargetBlock($maxDistance, array $transparent = []){
		try{
			$block = $this->getLineOfSight($maxDistance, 1, $transparent)[0];
			if($block instanceof Block){
				return $block;
			}
		}catch(\ArrayOutOfBoundsException $e){
		}

		return null;
	}
    public function updateEntityActionState(){
        $this->targetTasks->onUpdateTasks();
        $this->tasks->onUpdateTasks();
        $this->navigator->onUpdateNavigation();
        $this->updateAITasks();
        $this->moveHelper->onUpdateMoveHelper();
        $this->lookHelper->onUpdateLook();
        $this->jumpHelper->doJump();
        $this->updateMovement();
        return true;
    }

    public function updateAITasks(){
    }

    public function updateAITick(){
        $this->motionY += 0.03999999910593033;
    }
    protected function getJumpUpwardsMotion(){
        return 0.42;
    }

    protected function handleJumpLava(){
        $this->motionY += 0.03999999910593033;
    }

    protected function doJump(){
        $this->motionY = $this->getJumpUpwardsMotion();

        //if (this.isPotionActive(Potion.jump)){
        //	this.motionY += (double)((float)(this.getActivePotionEffect(Potion.jump).getAmplifier() + 1) * 0.1F);
        //}

        if ($this->isSprinting()){
            $f = $this->yaw * 0.017453292;
            $this->motionX -= sin($f) * 0.2;
            $this->motionZ += cos($f) * 0.2;
        }
    }

    public function setJumping($jumping){
        $this->isJumping = $jumping;
    }

    public function getMaxFallHeight() : float{
        if ($this->getAttackTarget() == null){
            return 3;
        }else{
            $i = $this->getHealth() - $this->getMaxHealth() * 0.33;
            $i = $i - (3 - 1) * 4;

            if ($i < 0){
                $i = 0;
            }

            return $i + 3;
        }
    }

    public function getVerticalFaceSpeed(){
        return 40;
    }

    public function setMoveForward($forward){
        $this->moveForward = $forward;
    }

    public function getAIMoveSpeed(){
        return $this->landMovementFactor;
    }

    public function setAIMoveSpeed($speedIn){
        $this->landMovementFactor = $speedIn;
        $this->setMoveForward($speedIn);
    }

    public function getNewNavigator($worldIn){
        return new PathNavigateGround($this, $worldIn);
    }

    public function getNavigator(){
        return $this->navigator;
    }

	public function getAITarget(){
		return $this->entityLivingToAttack;
	}

	public function getRevengeTimer(){
		return $this->revengeTimer;
	}

	public function setRevengeTarget($livingBase){
		$this->entityLivingToAttack = $livingBase;
		$this->revengeTimer = $this->server->getTick();
	}

	public function getAttackTarget(){
		return $this->attackTarget;
	}

	public function setAttackTarget($entitylivingbaseIn){
		$this->attackTarget = $entitylivingbaseIn;
	}

	public function getMoveHelper(){
	        return $this->moveHelper;
	}

	public function getLookHelper(){
		return $this->lookHelper;
	}

	public function getJumpHelper(){
		return $this->jumpHelper;
	}
	public function clamp($num, $min, $max){
		return $num < $min ? $min : ($num > $max ? $max : $num);
	}
}
