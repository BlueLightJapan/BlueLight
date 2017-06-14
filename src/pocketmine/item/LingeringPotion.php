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

namespace pocketmine\item;

class LingeringPotion extends Item {
	
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::LINGERING_POTION, $meta, $count, $this->getNameByMeta($meta));
	}
	
	public function getNameByMeta($meta){
		switch($meta){
			case Potion::WATER_BOTTLE:
				return "Lingering Water Bottle"; 
			case Potion::MUNDANE:
			case Potion::MUNDANE_EXTENDED:
				return "Lingering Mundane Potion";
			case Potion::THICK:
				return "Lingering Thick Potion";
			case Potion::AWKWARD:
				return "Lingering Awkward Potion";
			case Potion::INVISIBILITY:
			case Potion::INVISIBILITY_T:
				return "Lingering Potion of Invisibility";
			case Potion::LEAPING:
			case Potion::LEAPING_T:
				return "Lingering Potion of Leaping";
			case Potion::LEAPING_TWO:
				return "Lingering Potion of Leaping II";
			case Potion::FIRE_RESISTANCE:
			case Potion::FIRE_RESISTANCE_T:
				return "Lingering Potion of Fire Residence";
			/*case Potion::SPEED:
			case Potion::SPEED_T:
				return "Lingering Potion of Swiftness";
			case Potion::SPEED_TWO:
				return "Lingering Potion of Swiftness II";*/
			case Potion::SLOWNESS:
			case Potion::SLOWNESS_T:
				return "Lingering Potion of Slowness";
			case Potion::WATER_BREATHING:
			case Potion::WATER_BREATHING_T:
				return "Lingering Potion of Water Breathing";
			case Potion::HARMING:
				return "Lingering Potion of Harming";
			case Potion::HARMING_TWO:
				return "Lingering Potion of Harming II";
			case Potion::POISON:
			case Potion::POISON_T:
				return "Lingering Potion of Poison";
			case Potion::POISON_TWO:
				return "Lingering Potion of Poison II";
			case Potion::HEALING:
				return "Lingering Potion of Healing";
			case Potion::HEALING_TWO:
				return "Lingering Potion of Healing II";
  			case Potion::NIGHT_VISION:
  			case Potion::NIGHT_VISION_T:
				return "Lingerin Potion of Night Vision";
			default:
				return "Lingering Potion";
		}
	}
	
}
