<?php
namespace pocketmine\entity\AI;

class EntityAIArrowAttack extends EntityAIBase{

	private $entityHost;
	private $rangedAttackEntityHost;
	private $attackTarget;
	private $rangedAttackTime;
	private $entityMoveSpeed;
	private $field_75318_f;
	private $field_96561_g;
	private $maxRangedAttackTime;
	private $field_96562_i;
	private $maxAttackDistance;

	public function __construct($attacker, float $movespeed, int $p_i1650_4_, int $maxAttackTime, float $maxAttackDistanceIn){
		$this->rangedAttackTime = -1;
		$this->rangedAttackEntityHost = $attacker;
		$this->entityHost = $attacker;
		$this->entityMoveSpeed = $movespeed;
		$this->field_96561_g = $p_i1650_4_;
		$this->maxRangedAttackTime = $maxAttackTime;
		$this->field_96562_i = $maxAttackDistanceIn;
		$this->maxAttackDistance = $maxAttackDistanceIn * $maxAttackDistanceIn;
		$this->setMutexBits(3);
	}

	public function shouldExecute() : bool{
		$entitylivingbase = $this->entityHost->getAttackTarget();

		if ($entitylivingbase == null){
			return false;
		}else{
			$this->attackTarget = $entitylivingbase;
			return true;
		}
	}

	public function continueExecuting() : bool{
		return $this->shouldExecute() || !$this->entityHost->getNavigator()->noPath();
	}

	public function resetTask(){
		$this->attackTarget = null;
		$this->field_75318_f = 0;
		$this->rangedAttackTime = -1;
	}

	public function updateTask(){
		if($this->attackTarget == null) return;
		$d0 = $this->entityHost->distanceSquared($this->attackTarget);
		$flag = true;//$this->entityHost->canSee($this->attackTarget);

		if ($flag){
			++$this->field_75318_f;
		}else{
			$this->field_75318_f = 0;
		}

		if ($d0 <= $this->maxAttackDistance && $this->field_75318_f >= 20){
			$this->entityHost->getNavigator()->clearPathEntity();
		}else{
			$this->entityHost->getNavigator()->tryMoveToEntityLiving($this->attackTarget, $this->entityMoveSpeed);
		}

		$this->entityHost->getLookHelper()->setLookPositionWithEntity($this->attackTarget, 30.0, 30.0);

		if (--$this->rangedAttackTime == 0){
			if ($d0 > $this->maxAttackDistance || !$flag){
				return;
			}

			$f = sqrt($d0) / $this->field_96562_i;
			$lvt_5_1_ = $this->clamp($f, 0.1, 1.0);
			$this->rangedAttackEntityHost->attackEntityWithRangedAttack($this->attackTarget, $lvt_5_1_);
			$this->rangedAttackTime = floor($f * ($this->maxRangedAttackTime - $this->field_96561_g) + $this->field_96561_g);
		}else if ($this->rangedAttackTime < 0){
			$f2 = sqrt($d0) / $this->field_96562_i;
			$this->rangedAttackTime = floor($f2 * ($this->maxRangedAttackTime - $this->field_96561_g) + $this->field_96561_g);
		}
	}

	public function clamp($num, $min, $max){
		return $num < $min ? $min : ($num > $max ? $max : $num);
	}
}