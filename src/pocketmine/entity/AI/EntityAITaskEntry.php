<?php
namespace pocketmine\entity\AI;

class EntityAITaskEntry{

	public $action;
	public $priority;

	public function __construct($priorityIn, $task){
		$this->priority = $priorityIn;
		$this->action = $task;
	}
}