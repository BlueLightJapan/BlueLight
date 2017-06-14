<?php

/*
 *
 *   _____       _             _   _____  ______      ____       _        
 *  / ____|     (_)           | | |  __ \|  ____|    |  _ \     | |       
 * | (___  _ __  _  __ _  ___ | |_| |__) | |__ ______| |_) | ___| |_ __ _ 
 *  \___ \| '_ \| |/ _` |/ _ \| __|  ___/|  __|______|  _ < / _ \ __/ _` |
 *  ____) | |_) | | (_| | (_) | |_| |    | |____     | |_) |  __/ || (_| |
 * |_____/| .__/|_|\__, |\___/ \__|_|    |______|    |____/ \___|\__\__,_|
 *        | |       __/ |                                                 
 *        |_|      |___/      
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author SpigotPE-Beta Team
 * @link http://github.com/SpigotPE-Beta
 *
 *
*/

namespace pocketmine\block;

use pocketmine\item\Tool;

class CyanGlazedTerracotta extends Solid{

	protected $id = self::CYAN_GLAZED_TERRACOTTA;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Cyan Glazed Terracotta";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getHardness(){
		return 1.8;
	}
}
