<?php
namespace pocketmine\entity\AI;

class EntityAILookIdle extends EntityAIBase{

	private $idleEntity;
	private $lookX;
	private $lookZ;
	private $idleTime;

	public function __construct($entitylivingIn){
		$this->idleEntity = $entitylivingIn;
		$this->setMutexBits(3);
	}

	public function shouldExecute(){
		return rand(0, 100) / 100 < 0.02;
	}

	public function continueExecuting(){
		return $this->idleTime >= 0;
	}

	public function startExecuting(){
		$d0 = (M_PI * 2) * rand(0, 100) / 100;
		$this->lookX = cos($d0);
		$this->lookZ = sin($d0);
		$this->idleTime = 20 + rand(0, 20);
	}

	public function updateTask(){
		--$this->idleTime;
		$this->idleEntity->getLookHelper()->setLookPosition($this->idleEntity->x + $this->lookX, $this->idleEntity->y + $this->idleEntity->getEyeHeight(), $this->idleEntity->z + $this->lookZ, 10.0, 40);
	}
}