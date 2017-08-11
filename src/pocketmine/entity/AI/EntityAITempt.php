<?php
namespace pocketmine\entity\AI;

class EntityAITempt extends EntityAIBase{

	private $temptedEntity;
	private $speed;
	private $targetX;
	private $targetY;
	private $targetZ;
	private $pitch;
	private $yaw;
	private $temptingPlayer;
	private $delayTemptCounter;
	private $isRunning;
	private $temptItem;
	private $scaredByPlayerMovement;
	private $avoidWater;

	public function __construct($temptedEntityIn, float $speedIn, int $temptItemIn, bool $scaredByPlayerMovementIn){
		$this->temptedEntity = $temptedEntityIn;
		$this->speed = $speedIn;
		$this->temptItem = $temptItemIn;
		$this->scaredByPlayerMovement = $scaredByPlayerMovementIn;
		$this->setMutexBits(3);

		if (!($temptedEntityIn->getNavigator() instanceof PathNavigateGround)){
			//ERROR "Unsupported mob type for TemptGoal"
		}
	}

	public function shouldExecute() : bool{
		if ($this->delayTemptCounter > 0){
			--$this->delayTemptCounter;
			return false;
		}else{
			$distance = 10.0;
			$target = null;
			foreach($this->temptedEntity->level->getPlayers() as $player) {

				$p2e_distance = $player->distance($this->temptedEntity);
				if($distance > $p2e_distance) {
					$target = $player;
					$distance = $p2e_distance;
				}
			}
			$this->temptingPlayer = $target;

			if ($this->temptingPlayer == null){
				return false;
			}else{
				$item = $this->temptingPlayer->getInventory()->getItemInHand();
				return $item->getId() == $this->temptItem;
			}
		}
	}

	public function continueExecuting() : bool{
		if ($this->scaredByPlayerMovement){
			if ($this->temptedEntity->distanceSquared($this->temptingPlayer) < 36.0){
				if ($this->temptingPlayer->distanceSquared($this->targetX, $this->targetY, $this->targetZ) > 0.010000000000000002){
					return false;
				}

				if (abs($this->temptingPlayer->pitch - $this->pitch) > 5.0 || abs($this->temptingPlayer->yaw - $this->yaw) > 5.0){
					return false;
				}
			}else{
				$this->targetX = $this->temptingPlayer->x;
				$this->targetY = $this->temptingPlayer->y;
				$this->targetZ = $this->temptingPlayer->z;
			}

			$this->pitch = $this->temptingPlayer->pitch;
			$this->yaw = $this->temptingPlayer->yaw;
		}

		return $this->shouldExecute();
	}

	public function startExecuting(){
		$this->targetX = $this->temptingPlayer->x;
		$this->targetY = $this->temptingPlayer->y;
		$this->targetZ = $this->temptingPlayer->z;
		$this->isRunning = true;
		$this->avoidWater = $this->temptedEntity->getNavigator()->getAvoidsWater();
		$this->temptedEntity->getNavigator()->setAvoidsWater(false);
	}

	public function resetTask(){
		$this->temptingPlayer = null;
		$this->temptedEntity->getNavigator()->clearPathEntity();
		$this->delayTemptCounter = 100;
		$this->isRunning = false;
		$this->temptedEntity->getNavigator()->setAvoidsWater($this->avoidWater);
	}

	public function updateTask(){
		if($this->temptingPlayer == null)
			return;
		$this->temptedEntity->getLookHelper()->setLookPositionWithEntity($this->temptingPlayer, 30.0, $this->temptedEntity->getVerticalFaceSpeed());

		if ($this->temptedEntity->distanceSquared($this->temptingPlayer) < 6.25){
			$this->temptedEntity->getNavigator()->clearPathEntity();
		}else{
			$this->temptedEntity->getNavigator()->tryMoveToEntityLiving($this->temptingPlayer, $this->speed);
		}
	}

	public function isRunning() : bool{
		return $this->isRunning;
	}
}