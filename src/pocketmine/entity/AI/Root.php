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

class Root {


    public $root;

    private $xyz;
    private $rootCount;
    private $isEnd;

    public function __construct($xyz) {
        $this->root = [];
        $this->rootCount = 0;
        $this->xyz = $xyz;
        $this->isEnd = false;
    }

    public function addMotion(array $xyz) {
        $n_root = clone $this;
        $n_root->root[] = $xyz;
        $n_root->setXyz([$this->xyz[0]+$xyz[0], $this->xyz[1]+$xyz[1], $this->xyz[2]+$xyz[2]]);
        return $n_root;
    }

    public function getRoot() {
        if(count($this->root) > $this->rootCount) {

            $result = $this->root[$this->rootCount];
            $this->rootCount++;
            return $result;

        } else {

            $this->isEnd = true;
            return null;

        }
    }

    public function isEnd() {
        return $this->isEnd;
    }

    public function getXyz() {
        return $this->xyz;
    }

    public function setXyz($xyz) {
        $this->xyz = $xyz;
    }
}