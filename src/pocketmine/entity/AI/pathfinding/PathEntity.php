<?php
namespace pocketmine\entity\AI\pathfinding;

use pocketmine\math\Vector3;

class PathEntity{

	private $points;
	private $currentPathIndex = 0;
	private $pathLength;

	public function __construct($pathpoints){
		$this->points = $pathpoints;
		$this->pathLength = count($pathpoints);
	}

	public function incrementPathIndex(){
		++$this->currentPathIndex;
	}

	public function isFinished(){
		return $this->currentPathIndex >= $this->pathLength;
	}

	public function getFinalPathPoint(){
		return $this->pathLength > 0 ? $this->points[$this->pathLength - 1] : null;
	}

	public function getPathPointFromIndex($index){
		return $this->points[$index];
	}

	public function getCurrentPathLength(){
		return $this->pathLength;
	}

	public function setCurrentPathLength($length){
		$this->pathLength = $length;
	}

	public function getCurrentPathIndex(){
		return $this->currentPathIndex;
	}

	public function setCurrentPathIndex($currentPathIndexIn){
		$this->currentPathIndex = $currentPathIndexIn;
	}

	public function getVectorFromIndex($entityIn, $index){
		$d0 = $this->points[$index]->xCoord + 0.5/* + ($entityIn->width + 1.0) * 0.5*/;
		$d1 = $this->points[$index]->yCoord;
		$d2 = $this->points[$index]->zCoord + 0.5/* + ($entityIn->width + 1.0) * 0.5:*/;
		return new Vector3($d0, $d1, $d2);
	}

	public function getPosition($entityIn){
		return $this->getVectorFromIndex($entityIn, $this->currentPathIndex);
	}

	public function isSamePath($pathentityIn){
		if ($pathentityIn == null){
			return false;
		}else if (count($pathentityIn->points) != count($this->points)){
			return false;
		}else{
			for ($i = 0; $i < count($this->points); ++$i){
				if ($this->points[$i]->xCoord != $pathentityIn->points[$i]->xCoord || $this->points[$i]->yCoord != $pathentityIn->points[$i]->yCoord || $this->points[$i]->zCoord != $pathentityIn->points[$i]->zCoord){
					return false;
				}
			}

			return true;
		}
	}

	public function isDestinationSame($vec){
		$pathpoint = $this->getFinalPathPoint();
		return $pathpoint == null ? false : $pathpoint->xCoord == $vec->xCoord && $pathpoint->zCoord == $vec->zCoord;
	}
}