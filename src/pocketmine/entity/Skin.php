<?php

/*
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLight Team
 * 
 *
*/

namespace pocketmine\entity;

class Skin{

	const SINGLE_SKIN_SIZE = 64 * 32 * 4;
	const DOUBLE_SKIN_SIZE = 64 * 64 * 4;

	const MODEL_STEVE = "Standard_Steve";
	const MODEL_ALEX = "Standard_Alex";

	/** @var (Base64 decoded) string */
	protected $data;
	/** @var  string */
	protected $model;

	public function __construct($data, $model){
		$this->data = $data;
		$this->model = $model;
	}

	public function getData(){
		return $this->data;
	}

	public function getModel(){
		return $this->model;
	}

	public function setData($data){
		if(strlen($data) != self::SINGLE_SKIN_SIZE && strlen($data) != self::DOUBLE_SKIN_SIZE){
			Server::getInstance()->getLogger()->critical("Invalid skin");
			return false;
		}
		$this->data = $data;
	}

	public function setModel($model){
		if($model == "") $model = self::MODEL_STEVE;
		$this->model = $model;
	}

}