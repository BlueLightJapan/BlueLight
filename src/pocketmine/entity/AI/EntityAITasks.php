<?php
namespace pocketmine\entity\AI;

class EntityAITasks{

	public $taskEntries = [];
	private $executingTaskEntries = [];
	private $tickCount;
	private $tickRate = 3;

	public function addTask($priority, $task){
		$entry = new EntityAITaskEntry($priority, $task);
		$this->taskEntries[spl_object_hash($entry)] = new EntityAITaskEntry($priority, $task);
	}

	public function removeTask($task){
		foreach($this->taskEntries as $hashObject => $entry){
			if($task == $entry){
				if(isset($this->executingTaskEntries[spl_object_hash($entry)])){
					$entry->action->resetTask();
					unset($this->executingTaskEntries[spl_object_hash($entry)]);
				}
			}
		}
	}

	public function onUpdateTasks(){

		$count = 0;
		$entry = null;

		if ($this->tickCount++ % $this->tickRate == 0){
			$data = [];
			foreach($this->taskEntries as $index => $e){
				$data[$count] = $index;
				$count++;
			}
			$count = -1;
			while (true){
				while (true){
					if (empty($data[$count + 1])){
						break 2;
					}

					$count++;
					$flag = isset($this->executingTaskEntries[$data[$count]]);

					if (!$flag){
						break;
					}
					$entry = $this->executingTaskEntries[$data[$count]];

					if (!$this->canUse($entry) || !$this->canContinue($entry)){
						$entry->action->resetTask();
						unset($this->executingTaskEntries[spl_object_hash($entry)]);
						break;
					}
				}

				if ($this->canUse($this->taskEntries[$data[$count]]) && $this->taskEntries[$data[$count]]->action->shouldExecute()){
					$this->taskEntries[$data[$count]]->action->startExecuting();
					$this->executingTaskEntries[spl_object_hash($this->taskEntries[$data[$count]])] = $this->taskEntries[$data[$count]];
				}
			}
		}else{
			$data = [];
			foreach($this->executingTaskEntries as $index => $e){
				$data[$count] = $index;
				$count++;
			}
			$count = 0;

			while (isset($data[$count + 1])){
				$count++;
				$entry = $this->executingTaskEntries[$data[$count]];
				if (!$this->canContinue($entry)){
					$entry->action->resetTask();
					$this->executingTaskEntries[spl_object_hash($entry)] = $entry;
				}
			}
		}
		foreach($this->executingTaskEntries as $entry){
			$entry->action->updateTask();
		}
	}

	private function canContinue($taskEntry){
		$flag = $taskEntry->action->continueExecuting();
		return $flag;
	}

	private function canUse($taskEntry){
		if($taskEntry == null){
			return false;
		}
		foreach($this->taskEntries as $entry){
			if ($entry != $taskEntry){
				if ($taskEntry->priority >= $entry->priority){
					if (!$this->areTasksCompatible($taskEntry, $entry) && isset($this->executingTaskEntries[spl_object_hash($entry)])){
						return false;
					}
				}else if (!$entry->action->isInterruptible() && isset($this->executingTaskEntries[sql_object_hash($entry)])){
					return false;
				}
			}
		}

		return true;
	}

	private function areTasksCompatible($taskEntry1, $taskEntry2){
		return ($taskEntry1->action->getMutexBits() & $taskEntry2->action->getMutexBits()) == 0;
	}
}