<?php
namespace pocketmine\entity\AI\pathfinding;

abstract class NodeProcessor{

	protected $worldObj;
	protected $pointMap = [];
	protected $entitySizeX;
	protected $entitySizeY;
	protected $entitySizeZ;

	public function initProcessor($world, $entityIn){
		$this->worldObj = $world;
		$this->pointMap = [];
		$this->entitySizeX = floor($entityIn->width + 1.0);
		$this->entitySizeY = floor($entityIn->height + 1.0);
		$this->entitySizeZ = floor($entityIn->width + 1.0);
	}

	public function postProcess(){
	}

	protected function openPoint($x, $y, $z){
		$i = PathPoint::makeHash($x, $y, $z);

		if (empty($this->pointMap[$i])){
			$pathpoint = new PathPoint($x, $y, $z);
			$this->pointMap[$i] = $pathpoint;
		}else{
			$pathpoint = $this->pointMap[$i];
		}

		return $pathpoint;
	}

	public abstract function getPathPointTo($entityIn);

	public abstract function getPathPointToCoords($entityIn, $x, $y, $target);

	public abstract function findPathOptions($pathOptions, $entityIn, $currentPoint, $targetPoint, $maxDistance);
}