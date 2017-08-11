<?php
namespace pocketmine\entity\AI\pathfinding;

class Path{

	private $pathPoints = [];
	private $count;

	public function addPoint($point){
		if ($point->index >= 0){
			//ERROR
		}else{
			if ($this->count == count($this->pathPoints)){
				$apathpoint = [];
				for($i = 0; $i < $this->count; $i++){
					$apathpoint[$i] = $this->pathPoints[$i];
				}
				$this->pathPoints = $apathpoint;
			}

			$this->pathPoints[$this->count] = $point;
			$point->index = $this->count;
			$this->sortBack($this->count++);
			return $point;
		}
	}

	public function clearPath(){
		$this->count = 0;
	}

	public function dequeue(){
		$pathpoint = $this->pathPoints[0];
		$this->pathPoints[0] = $this->pathPoints[--$this->count];
		$this->pathPoints[$this->count] = null;

		if ($this->count > 0){
			$this->sortForward(0);
		}

		$pathpoint->index = -1;
		return $pathpoint;
	}

	public function changeDistance($point, $distance){
		$f = $point->distanceToTarget;
		$point->distanceToTarget = $distance;

		if ($distance < $f){
			$this->sortBack($point->index);
		}else{
			$this->sortForward($pont->index);
		}
	}

	private function sortBack($p_75847_1_){
		$pathpoint = $this->pathPoints[$p_75847_1_];

		for ($f = $pathpoint->distanceToTarget; $p_75847_1_ > 0; $p_75847_1_ = $i){
			$i = $p_75847_1_ - 1 >> 1;
			$pathpoint1 = $this->pathPoints[$i];

			if ($f >= $pathpoint1->distanceToTarget){
				break;
			}

			$this->pathPoints[$p_75847_1_] = $pathpoint1;
			$pathpoint1->index = $p_75847_1_;
		}

		$this->pathPoints[$p_75847_1_] = $pathpoint;
		$pathpoint->index = $p_75847_1_;
	}

	private function sortForward($p_75846_1_){
		$pathpoint = $this->pathPoints[$p_75846_1_];
		$f = $pathpoint->distanceToTarget;

		while (true){
			$i = 1 + ($p_75846_1_ << 1);
			$j = $i + 1;

			if ($i >= $this->count){
				break;
			}

			$pathpoint1 = $this->pathPoints[$i];
			$f1 = $pathpoint1->distanceToTarget;

			if ($j >= $this->count){
				$pathpoint2 = null;
				$f2 = 2147483647;
			}else{
				$pathpoint2 = $this->pathPoints[$j];
				$f2 = $pathpoint2->distanceToTarget;
			}

			if ($f1 < $f2){
				if ($f1 >= $f){
					break;
				}

				$this->pathPoints[$p_75846_1_] = $pathpoint1;
				$pathpoint1->index = $p_75846_1_;
				$p_75846_1_ = $i;
			}else{
				if ($f2 >= $f){
					break;
				}

				$this->pathPoints[$p_75846_1_] = $pathpoint2;
				$pathpoint2->index = $p_75846_1_;
				$p_75846_1_ = $j;
			}
		}

		$this->pathPoints[$p_75846_1_] = $pathpoint;
		$pathpoint->index = $p_75846_1_;
	}

	public function isPathEmpty(){
		return $this->count == 0;
	}
}