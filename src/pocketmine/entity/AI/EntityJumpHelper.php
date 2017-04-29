<?php
namespace pocketmine\entity\AI;

class EntityJumpHelper{

	private $entity;
	protected $isJumping;

	public function __construct($entityIn){
		$this->entity = $entityIn;
	}

	public function setJumping(){
		$this->isJumping = true;
	}

	public function doJump(){
		$this->entity->setJumping($this->isJumping);
		$this->isJumping = false;
	}
}