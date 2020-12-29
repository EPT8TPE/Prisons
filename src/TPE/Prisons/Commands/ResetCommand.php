<?php

declare(strict_types=1);

namespace TPE\Prisons\Commands;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use TPE\Prisons\Prisons;

class ResetCommand extends Command {

    private $messages;

    public function __construct() {
        parent::__construct("reset", "Resets prison database, do not use unless you wish to reset player progress.", TextFormat::RED . "/reset");
        $this->setPermission("prisons.reset");
        $this->messages = new Config(Prisons::get()->getDataFolder() . "messages.yml", Config::YAML);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->testPermission($sender)) {
            $sender->sendMessage($this->messages->get("no-perms"));
            return false;
        }

        foreach (Prisons::get()->getServer()->getOnlinePlayers() as $player) {
            $player->kick(strval("Prison database resetting..."));
        }

        unlink("prisons.db");
        Prisons::get()->getServer()->shutdown();

        return true;
    }

}