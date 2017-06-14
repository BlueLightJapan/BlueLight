<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\Server;


use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\event\plugin\PluginEnableEvent;
use pocketmine\utils\MainLogger;


class FolderPluginLoader implements PluginLoader {

    /** @var Server */
    private $server;

    /**
     * @param Server $server
     */
    public function __construct(Server $server) {
        $this->server = $server;
    }

    /**
     * Loads the plugin contained in $file
     *
     * @param string $file
     *
     * @return Plugin
     */
    public function loadPlugin($file) {
        if (is_dir($file) and file_exists($file . "/plugin.yml") and file_exists($file . "/src/")) {
            if (($description = $this->getPluginDescription($file)) instanceof PluginDescription) {
                MainLogger::getLogger()->info("Loading " . $description->getFullName());
                $dataFolder = dirname($file) . DIRECTORY_SEPARATOR . $description->getName();
                if (file_exists($dataFolder) and !is_dir($dataFolder)) {
                    trigger_error("Projected dataFolder '" . $dataFolder . "' for " . $description->getName() . " exists and is not a directory", E_USER_WARNING);

                    return null;
                }


                $className = $description->getMain();
                $this->server->getLoader()->addPath($file . "/src");

                if (class_exists($className, true)) {
                    $plugin = new $className();
                    $this->initPlugin($plugin, $description, $dataFolder, $file);

                    return $plugin;
                } else {
                    trigger_error("Couldn't load source plugin " . $description->getName() . ": class " . $description->getMain() . "not found", E_USER_WARNING);

                    return null;
                }
            }
        }

        return null;
    }

    /**
     * Gets the PluginDescription from the file
     *
     * @param string $file
     *
     * @return PluginDescription
     */
    public function getPluginDescription($file) {
        if (is_dir($file) and file_exists($file . "/plugin.yml")) {
            $yaml = @file_get_contents($file . "/plugin.yml");
            if ($yaml != "") {
                return new PluginDescription($yaml);
            }
        }

        return null;
    }

    /**
     * Returns the filename patterns that this loader accepts
     *
     * @return array|string
     */
    public function getPluginFilters() {
        return "/[^\\.]/";
    }

    /**
     * @param PluginBase $plugin
     * @param PluginDescription $description
     * @param string $dataFolder
     * @param string $file
     */
    private function initPlugin(PluginBase $plugin, PluginDescription $description, $dataFolder, $file) {
        $plugin->init($this, $this->server, $description, $dataFolder, $file);
        $plugin->onLoad();
    }

    /**
     * @param Plugin $plugin
     */
    public function enablePlugin(Plugin $plugin) {
        if ($plugin instanceof PluginBase and !$plugin->isEnabled()) {
            MainLogger::getLogger()->info("Enabling " . $plugin->getDescription()->getFullName());

            $plugin->setEnabled(true);

            Server::getInstance()->getPluginManager()->callEvent(new PluginEnableEvent($plugin));
        }
    }

    /**
     * @param Plugin $plugin
     */
    public function disablePlugin(Plugin $plugin) {
        if ($plugin instanceof PluginBase and $plugin->isEnabled()) {
            MainLogger::getLogger()->info("Disabling " . $plugin->getDescription()->getFullName());

            Server::getInstance()->getPluginManager()->callEvent(new PluginDisableEvent($plugin));

            $plugin->setEnabled(false);
        }
    }
}
