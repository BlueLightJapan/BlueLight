<?php

/*
 *   ____  _            _      _       _     _
 *  |  _ \| |          | |    (_)     | |   | |
 *  | |_) | |_   _  ___| |     _  __ _| |__ | |_
 *  |  _ <| | | | |/ _ \ |    | |/ _` | '_ \| __|
 *  | |_) | | |_| |  __/ |____| | (_| | | | | |_
 *  |____/|_|\__,_|\___|______|_|\__, |_| |_|\__|
 *                                __/ |
 *                               |___/
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/



namespace pocketmine\entity\AI;

use pocketmine\level\Position;
use pocketmine\level\Level;

use pocketmine\block\Transparent;
use pocketmine\block\Stair;

class RootExplorer {

    private $start;
    private $end;
    private $level;
    private $root;

    public function __construct(array $start, array $end, Level $level) {
        $this->start = $start;
        $this->end   = $end;
        $this->level = $level;
        
    }

    public function exec() {

        $start = $this->start;
        $end   = $this->end;

        $max_x = max($start[0], $end[0]) + 5;
        $max_y = max($start[1], $end[1]) + 5;
        $max_z = max($start[2], $end[2]) + 5;

        
        $min_x = min($start[0], $end[0]) - 5;
        $min_y = min($start[1], $end[1]) - 5;
        $min_z = min($start[2], $end[2]) - 5;

        $pos = [];

        for($x = $min_x; $x < $max_x; $x++) {

            for($y = $min_y; $y < $max_y; $y++) {

                for($z = $min_z; $z < $max_z; $z++) {
                    $block1 = $this->level->getBlock(new Position($x, $y, $z));
                    $block2 = $this->level->getBlock(new Position($x, $y - 1, $z));
                    
                    $pos[$x][$y][$z] = (($block1 instanceof Transparent) and !($block1 instanceof Stair)) and !(($block2 instanceof Transparent) and !($block2 instanceof Stair));
                }

            }
            
        }
        // var_dump($pos);
        // sleep(10);

        $roots[] = new Root($this->start);

        $motions = [
            [ 1,  0,  0],
            [ 1,  1,  0],
            [ 1, -1,  0],
            [-1,  0,  0],
            [-1,  1,  0],
            [-1, -1,  0],
            [ 0,  0,  1],
            [ 0,  1,  1],
            [ 0, -1,  1],
            [ 0,  0, -1],
            [ 0,  1, -1],
            [ 0, -1, -1],
        ];

        while(1) {
            
            if(count($roots) === 0) {
                $this->root = null;
                break;
            }

            $n_roots = [];

            foreach($roots as $root) {

                $xyz = $root->getXyz();

                // print("A");

                foreach($motions as $motion) {
                    
                    if($pos [$xyz[0]+$motion[0]] [$xyz[1]+$motion[1]] [$xyz[2]+$motion[2]] ?? false) {
                        $n_roots[] = $new_root = $root->addMotion($motion);
                        $n_xyz = $new_root->getXyz();
                        $pos [$xyz[0]+$motion[0]] [$xyz[1]+$motion[1]] [$xyz[2]+$motion[2]] = false;
                        if(
                            $n_xyz[0] == $this->end[0] and
                            $n_xyz[1] == $this->end[1] and
                            $n_xyz[2] == $this->end[2]
                        ) {
                            $this->root = $new_root;
                            break 3;
                        }

                    }
                }

            }

            $roots = $n_roots;

        }

    }

    public function getRoot() {
        return $this->root->getRoot();
    }

    public function isEnd() {
        return  $this->root->isEnd();
    }

    public function isEmpty() {
        return ($this->root ?? null) === null;
    }
}