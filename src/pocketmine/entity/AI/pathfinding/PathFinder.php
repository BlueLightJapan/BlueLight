<?php
namespace pocketmine\entity\AI\pathfinding;

class PathFinder{

	private $path;
	private $pathOptions = [];
	private $nodeProcessor;

	public function __construct($nodeProcessorIn){
		$this->path = new Path();
		$this->nodeProcessor = $nodeProcessorIn;
	}

	public function createEntityPathTo1($worldObj, $entityFrom, $entityTo, $dist){
		return $this->createEntityPathTo3($worldObj, $entityFrom, $entityTo->x, $entityTo->getBoundingBox()->minY, $entityTo->z, $dist);
	}

	public function createEntityPathTo2($worldObj, $entityIn, $targetPos, $dist){
		return $this->createEntityPathTo3($worldObj, $entityIn, $targetPos->getX() + 0.5, $targetPos->getY() + 0.5, $targetPos->getZ() + 0.5, $dist);
	}

	private function createEntityPathTo3($worldObj, $entityIn, $x, $y, $z, $distance){
		$this->path->clearPath();
		$this->nodeProcessor->initProcessor($worldObj, $entityIn);
		$pathpoint = $this->nodeProcessor->getPathPointTo($entityIn);
		$pathpoint1 = $this->nodeProcessor->getPathPointToCoords($entityIn, $x, $y, $z);
		$pathentity = $this->addToPath($entityIn, $pathpoint, $pathpoint1, $distance);
		$this->nodeProcessor->postProcess();
		return $pathentity;
	}

	private function addToPath($entityIn, $pathpointStart, $pathpointEnd, $maxDistance){
		$pathpointStart->totalPathDistance = 0.0;
		$pathpointStart->distanceToNext = $pathpointStart->distanceToSquared($pathpointEnd);
		$pathpointStart->distanceToTarget = $pathpointStart->distanceToNext;
		$this->path->clearPath();
		$this->path->addPoint($pathpointStart);
		$pathpoint = $pathpointStart;

		while (!$this->path->isPathEmpty()){
			$pathpoint1 = $this->path->dequeue();

			if ($pathpoint1->equals($pathpointEnd)){
				return $this->createEntityPath($pathpointStart, $pathpointEnd);
			}

			if ($pathpoint1->distanceToSquared($pathpointEnd) < $pathpoint->distanceToSquared($pathpointEnd)){
				$pathpoint = $pathpoint1;
			}

			$pathpoint1->visited = true;
			$i = $this->nodeProcessor->findPathOptions($this->pathOptions, $entityIn, $pathpoint1, $pathpointEnd, $maxDistance);
			$this->pathOptions = $i[1];

			for ($j = 0; $j < $i[0]; ++$j){
				$pathpoint2 = $this->pathOptions[$j];
				$f = $pathpoint1->totalPathDistance + $pathpoint1->distanceToSquared($pathpoint2);

				if ($f < $maxDistance * 2.0 && (!$pathpoint2->isAssigned() || $f < $pathpoint2->totalPathDistance)){
					$pathpoint2->previous = $pathpoint1;
					$pathpoint2->totalPathDistance = $f;
					$pathpoint2->distanceToNext = $pathpoint2->distanceToSquared($pathpointEnd);

					if ($pathpoint2->isAssigned()){
						$this->path->changeDistance($pathpoint2, $pathpoint2->totalPathDistance + $pathpoint2->distanceToNext);
					}else{
						$pathpoint2->distanceToTarget = $pathpoint2->totalPathDistance + $pathpoint2->distanceToNext;
						$this->path->addPoint($pathpoint2);
					}
				}
			}
		}

		if ($pathpoint == $pathpointStart){
			return null;
		}else{
			return $this->createEntityPath($pathpointStart, $pathpoint);
		}
	}

	private function createEntityPath($start, $end){
		$i = 1;

		for ($pathpoint = $end; $pathpoint->previous != null; $pathpoint = $pathpoint->previous){
			++$i;
		}

		$apathpoint = [];
		$pathpoint1 = $end;
		--$i;

		for ($apathpoint[$i] = $end; $pathpoint1->previous != null; $apathpoint[$i] = $pathpoint1){
			$pathpoint1 = $pathpoint1->previous;
			--$i;
		}

		return new PathEntity($apathpoint);
	}
}