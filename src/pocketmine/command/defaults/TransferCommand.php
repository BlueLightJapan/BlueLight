<?php

namespace pocketmine\command\defaults;

use pocketmine\network\protocol\TransferPacket;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Server;

class TransferCommand extends VanillaCommand{
    public function __construct($name)
    {
        parent::__construct(
        $name,
        "%pocketmine.command.transfer.description",
        "%pocketmine.command.transfer.usage",
        ["transfer"]
    );
        $this->setPermission("pocketmine.command.transfer");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args){
        if(!($this->testPermission($sender))){
            return false;
        }

        $pk = new TransferPacket();
        $pk->address = $args[0];
        $pk->port = $args[1];
        $sender->dataPacket($pk);

        Command::broadcastCommandMessage($sender, "Transferred to " . $args[0] . ":" . $args[1]);
    }
}