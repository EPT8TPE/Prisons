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
use TPE\Prisons\Prisons;
use TPE\Prisons\Utils\Configuration;

class AscendCommand extends Command {

    private $messages;

    public function __construct() {
        parent::__construct("ascend", "Ascend to the next ascension level, resetting your prisons rank and money to 0.", TextFormat::RED . "Usage: /ascend");
        $this->setPermission("prisons.ascend");
        $this->messages = new Config(Prisons::get()->getDataFolder() . "messages.yml", Config::YAML);
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

        $ascensionLevel = Prisons::get()->getAscension($sender->getName());

        $nextAscensionLevel = $ascensionLevel;
        $nextAscensionLevel++;

        if($nextAscensionLevel > (int) array_key_last(Prisons::get()->getConfig()->get("ascensions"))) {
            $sender->sendMessage($this->messages->get("max-ascension-level"));
        }

        if($data = Prisons::get()->getConfig()->getNested("ascensions." . $nextAscensionLevel)) {
            $money = EconomyAPI::getInstance()->myMoney($sender);

            if($money >= $data["price"]) {
                EconomyAPI::getInstance()->setMoney($sender, 0);
                Prisons::get()->setAscension($sender->getName(), $nextAscensionLevel);
                Prisons::get()->setRank($sender->getName(), "a");

                $currentAscensionLevel = Prisons::get()->getAscension($sender->getName());

                $sender->sendMessage(str_replace("{ASCENSION}", $currentAscensionLevel, $this->messages->get("successfully-ascended")));

                if(empty(Prisons::get()->getConfig()->get("world-name"))) {
                    $sender->teleport(Prisons::get()->getServer()->getDefaultLevel()->getSpawnLocation());
                } else {
                    $sender->teleport(Prisons::get()->getServer()->getLevelByName((string)Prisons::get()->getConfig()->get("world-name")));
                }

                $pp = Prisons::get()->getConfig()->get("PurePerms");

                if($pp instanceof PurePerms) {
                    foreach ($data["added-permissions"] as $permission) {
                        $pp->getUserDataMgr()->setPermission($sender, $permission);
                    }

                    foreach ($data["removed-permissions"] as $permission) {
                        $pp->getUserDataMgr()->unsetPermission($sender, $permission);
                    }
                }

            } else {
                $sender->sendMessage(str_replace("{NEEDED}", $data["price"], $this->messages->get("not-enough-money-ascension")));
            }

        } else {
            $sender->sendMessage(TextFormat::RED . "Configuration error!");
            Prisons::get()->getLogger()->critical("Configuration error detected, fix immediately or reset your config file!");
        }

        return true;
    }


}
