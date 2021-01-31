<?php

declare(strict_types=1);

namespace TPE\Prisons\Commands;

use _64FF00\PurePerms\PurePerms;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\plugin\Plugin;
use TPE\Prisons\Prisons;
use TPE\Prisons\Utils\Configuration;
use pocketmine\command\PluginIdentifiableCommand;

class PrestigeCommand extends Command implements PluginIdentifiableCommand {

    private $messages;

    public function __construct() {
        parent::__construct("prestige", "Prestiging resets your prisons rank and money to 0.", TextFormat::RED . "Usage: /prestige");
        $this->setPermission("prisons.prestige");
        $this->messages = Prisons::get()->getConfig();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("You must be a player in order to use that command!");
            return false;
        }

        if(!$this->testPermission($sender)) {
            $sender->sendMessage($this->messages->get("no-perms"));
            return false;
        }

        $rank = Prisons::get()->getRank($sender->getName());

        if($rank !== "z") {
            $currentRank = Configuration::getRankName($sender->getName());

            $sender->sendMessage(str_replace("{RANK}", $currentRank, $this->messages->get("not-rank-z")));
            return false;
        }

        $prestigeLevel = Prisons::get()->getPrestige($sender->getName());

        $nextPrestigeLevel = $prestigeLevel;
        $nextPrestigeLevel++;

        if($nextPrestigeLevel > (int) array_key_last(Prisons::get()->getConfig()->get("prestiges"))) {
            $sender->sendMessage($this->messages->get("max-prestige-level"));
        }

        if($data = Prisons::get()->getConfig()->getNested("prestiges." . $nextPrestigeLevel)) {
            $money = EconomyAPI::getInstance()->myMoney($sender);

            if($money >= $data["price"]) {
                EconomyAPI::getInstance()->setMoney($sender, 0);
                Prisons::get()->setPrestige($sender->getName(), $nextPrestigeLevel);
                Prisons::get()->setRank($sender->getName(), "a");

                $currentPrestigeLevel = Prisons::get()->getPrestige($sender->getName());

                $sender->sendMessage(str_replace("{PRESTIGE}", $currentPrestigeLevel, $this->messages->get("successfully-prestiged")));

                if(empty(Prisons::get()->getConfig()->get("world-name"))) {
                    $sender->teleport(Prisons::get()->getServer()->getDefaultLevel()->getSpawnLocation());
                } else {
                    $sender->teleport(Prisons::get()->getServer()->getLevelByName((string)Prisons::get()->getConfig()->get("world-name")));
                }

                $pp = Prisons::get()->getServer()->getPluginManager()->getPlugin("PurePerms");

                if($pp instanceof PurePerms) {
                    foreach ($data["added-permissions"] as $permission) {
                        $pp->getUserDataMgr()->setPermission($sender, $permission);
                    }

                    foreach ($data["removed-permissions"] as $permission) {
                        $pp->getUserDataMgr()->unsetPermission($sender, $permission);
                    }
                }

            } else {
                $sender->sendMessage(str_replace("{NEEDED}", $data["price"], $this->messages->get("not-enough-money-prestige")));
            }

        } else {
            $sender->sendMessage(TextFormat::RED . "Configuration error!");
            Prisons::get()->getLogger()->critical("Configuration error detected, fix immediately or reset your config file!");
        }
        return true;
    }
    
    public function getPlugin() : Plugin {
        return Prisons::get();
    }
  
}
