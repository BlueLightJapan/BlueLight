<?php
namespace pocketmine\entity\AI\pathfinding;

use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

abstract class PathNavigate{

	protected $theEntity;
	protected $worldObj;
	protected $currentPath;
	protected $speed;
	private $pathSearchRange;
	private $totalTicks;
	private $ticksAtLastPos;
	private $lastPosCheck;
	private $heightRequirement = 1.0;
	private $pathFinder;

	public function __construct($entitylivingIn, $worldIn){
		$this->theEntity = $entitylivingIn;
		$this->worldObj = $worldIn;
		$this->pathSearchRange = 16;//
		$this->pathFinder = $this->getPathFinder();
		$this->lastPosCheck = new Vector3();
	}

	protected abstract function getPathFinder();

	public function setSpeed($speedIn){
		$this->speed = $speedIn;
	}

	public function getPathSearchRange(){
		return $this->pathSearchRange;
	}

	public function getPathToXYZ($x, $y, $z){
		return $this->getPathToPos(new Vector3(floor($x), $y, floor($z)));
	}

	public function getPathToPos($pos){
		if (!$this->canNavigate()){
			return null;
		}else{
			$f = $this->getPathSearchRange();
			$pathentity = $this->pathFinder->createEntityPathTo2($this->worldObj, $this->theEntity, $pos, $f);
			return $pathentity;
		}
	}

	public function tryMoveToXYZ($x, $y, $z, $speedIn){
		$pathentity = $this->getPathToXYZ(floor($x), floor($y), floor($z));
		return $this->setPath($pathentity, $speedIn);
	}

	public function setHeightRequirement($jumpHeight){
		$this->heightRequirement = $jumpHeight;
	}

	public function getPathToEntityLiving($entityIn){
		if (!$this->canNavigate()){
			return null;
		}else{
			$f = $this->getPathSearchRange();
			$pathentity = $this->pathFinder->createEntityPathTo1($this->worldObj, $this->theEntity, $entityIn, $f);
			return $pathentity;
		}
	}

	public function tryMoveToEntityLiving($entityIn, $speedIn){
		$pathentity = $this->getPathToEntityLiving($entityIn);
		return $pathentity != null ? $this->setPath($pathentity, $speedIn) : false;
	}

	public function setPath($pathentityIn, $speedIn){
		if ($pathentityIn == null){
			$this->currentPath = null;
			return false;
		}else{
			if (!$pathentityIn->isSamePath($this->currentPath)){
				$this->currentPath = $pathentityIn;
			}

			$this->removeSunnyPath();

			if ($this->currentPath->getCurrentPathLength() == 0){
				return false;
			}else{
				$this->speed = $speedIn;
				$vec3 = $this->getEntityPosition();
				$this->ticksAtLastPos = $this->totalTicks;
				$this->lastPosCheck = $vec3;
				return true;
			}
		}
	}

	public function getPath(){
		return $this->currentPath;
	}

	public function onUpdateNavigation(){
		++$this->totalTicks;

		if (!$this->noPath()){
			if ($this->canNavigate()){
				$this->pathFollow();
			}else if ($this->currentPath != null && $this->currentPath->getCurrentPathIndex() < $this->currentPath->getCurrentPathLength()){
				$vec3 = $this->getEntityPosition();
				$vec31 = $this->currentPath->getVectorFromIndex($this->theEntity, $this->currentPath->getCurrentPathIndex());

 				if ($vec3->y > $vec31->y && !$this->theEntity->onGround && floor($vec3->x) == floor($vec31->x) && floor($vec3->z) == floor($vec31->z)){
					$this->currentPath->setCurrentPathIndex($this->currentPath->getCurrentPathIndex() + 1);
				}
			}

			if (!$this->noPath()){
				$vec32 = $this->currentPath->getPosition($this->theEntity);
				if ($vec32 != null){
					$axisalignedbb1 = (new AxisAlignedBB($vec32->x, $vec32->y, $vec32->z, $vec32->x, $vec32->y, $vec32->z))->expand(0.5, 0.5, 0.5);
					$list = $this->worldObj->getCollidingEntities($axisalignedbb1->addCoord(0.0, -1.0, 0.0), $this->theEntity);
					$d0 = -1.0;
					$axisalignedbb1 = $axisalignedbb1->offset(0.0, 1.0, 0.0);

					foreach($list as $entity){
						$d0 = $entity->getBoundingBox()->calculateYOffset($axisalignedbb1, $d0);
					}

					$this->theEntity->getMoveHelper()->setMoveTo($vec32->x, $vec32->y + $d0, $vec32->z, $this->speed);
				}
			}
		}
	}

	protected function pathFollow(){
		$vec3 = $this->getEntityPosition();
		$i = $this->currentPath->getCurrentPathLength();

		for ($j = $this->currentPath->getCurrentPathIndex(); $j < $this->currentPath->getCurrentPathLength(); ++$j){
			if ($this->currentPath->getPathPointFromIndex($j)->yCoord != $vec3->y){
				$i = $j;
				break;
			}
		}

		$f = $this->theEntity->width * $this->theEntity->width * $this->heightRequirement;

		for ($k = $this->currentPath->getCurrentPathIndex(); $k < $i; ++$k){
			$vec31 = $this->currentPath->getVectorFromIndex($this->theEntity, $k);

			if ($vec3->distanceSquared($vec31) < $f){
				$this->currentPath->setCurrentPathIndex($k + 1);
			}
		}

		$j1 = ceil($this->theEntity->width + 0.25);
		$k1 = $this->theEntity->height + 1;
		$l = $j1;

		for ($i1 = $i - 1; $i1 >= $this->currentPath->getCurrentPathIndex(); --$i1){
			if ($this->isDirectPathBetweenPoints($vec3, $this->currentPath->getVectorFromIndex($this->theEntity, $i1), $j1, $k1, $l)){
				$this->currentPath->setCurrentPathIndex($i1);
				$this->checkForStuck($vec3);
				return;
			}
		}

		$this->checkForStuck($vec3);
	}

	protected function checkForStuck($positionVec3){
		if ($this->totalTicks - $this->ticksAtLastPos > 100){
			if ($positionVec3->distanceSquared($this->lastPosCheck) < 2.25){
				$this->clearPathEntity();
			}

			$this->ticksAtLastPos = $this->totalTicks;
			$this->lastPosCheck = $positionVec3;
		}
	}

	public function noPath(){
		return $this->currentPath == null || $this->currentPath->isFinished();
	}

	public function clearPathEntity(){
		$this->currentPath = null;
	}

	protected abstract function getEntityPosition();

	protected abstract function canNavigate();

	protected function isInLiquid(){
		return $this->theEntity->isInsideOfWater() || $this->theEntity->isInsideOfLava();
	}

	protected function removeSunnyPath(){
	}

	protected abstract function isDirectPathBetweenPoints($posVec31, $posVec32, $sizeX, $sizeY, $sizeZ);
}