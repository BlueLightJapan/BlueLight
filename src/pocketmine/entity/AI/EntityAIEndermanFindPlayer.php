<?php
namespace pocketmine\entity\AI;

class EntityAIEndermanFindPlayer extends EntityAINearestAttackableTarget{

	private $player;
	private $screamingTick;
	private $teleportToEntityTick;
	private $enderman;

	public function __construct($enderman){
		parent::__construct($enderman, "pocketmine\Player", true);
		$this->enderman = $enderman;
	}

	public function shouldExecute() : bool{
		$d0 = $this->getTargetDistance();
		$distance = $d0;
		$target = null;
		foreach($this->enderman->level->getPlayers() as $player) {
			$p2e_distance = $player->distance($this->enderman);
			if($distance > $p2e_distance and !$player->isCreative()) {
				$target = $player;
				$distance = $p2e_distance;
			}
		}
		if($target == null){
			return false;
		}
		$this->player = $target;
		return true;
	}

	public function startExecuting(){
		$this->screamingTick = 5;
		$this->teleportToEntityTick = 0;
	}

	public function resetTask(){
		$this->player = null;
		$this->enderman->setScreaming(false);
		//speed - 0.15000000596046448
		parent::resetTask();
	}

	public function continueExecuting() : bool{
		if ($this->player != null){
			if (!$this->enderman->shouldAttackPlayer($this->player)){
				return false;
			}else{
				$this->enderman->isAggressive = true;
				$this->enderman->faceEntity($this->player, 10.0, 10.0);
				return true;
			}
                }else{
			return parent::continueExecuting();
		}
	}

	public function updateTask(){
		if ($this->player != null){
			if (--$this->screamingTick <= 0){
				$this->targetEntity = $this->player;
				$this->player = null;
				parent::startExecuting();
				//"mob.endermen.stare"
				$this->enderman->setScreaming(true);
				//speed + 0.15000000596046448
			}
		}else{
			if ($this->targetEntity != null){
				if ($this->targetEntity instanceof Player && $this->enderman->shouldAttackPlayer($this->targetEntity)){
					if ($this->targetEntity->distanceSquared($this->enderman) < 16.0){
						$this->enderman->teleportRandomly();
					}

					$this->teleportToEntityTick = 0;
				}else if ($this->targetEntity->distanceSquared($this->enderman) > 256.0 && $this->teleportToEntityTick++ >= 30 && $this->enderman->teleportToEntity($this->targetEntity)){
					$this->teleportToEntityTick = 0;
				}
			}

			parent::updateTask();
		}
	}
}