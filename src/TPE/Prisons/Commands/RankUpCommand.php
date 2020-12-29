<?php

declare(strict_types=1);

namespace TPE\Prisons\Commands;

use _64FF00\PurePerms\PurePerms;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use TPE\Prisons\Prisons;
use pocketmine\utils\TextFormat;
use TPE\Prisons\Utils\Configuration;

class RankUpCommand extends Command {

    private $messages;

    public function __construct() {
        parent::__construct("rankup", "Rankup to the next rank.", TextFormat::RED . "Usage: /rankup", ["ru"]);
        $this->setPermission("prisons.rankup");
        $this->messages = new Config(Prisons::get()->getDataFolder() . "messages.yml");
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
        $nextRank = $rank;
        $nextRank++;

        if($nextRank === "aa") {
            $sender->sendMessage($this->messages->get("max-rank"));
            return false;
        }

        if($data = Prisons::get()->getConfig()->get("ranks." . $nextRank)) {
            $money = EconomyAPI::getInstance()->myMoney($sender);
            $ascensionLevel = Prisons::get()->getAscension($sender->getName());

            if($ascensionLevel === 0) $ascensionLevel = 1;

            $data["price"] *= $ascensionLevel * Prisons::get()->getConfig()->get("ascension-multiplier");

            if($money >= $data["price"]) {
                EconomyAPI::getInstance()->reduceMoney($sender, $data["price"]);
                Prisons::get()->setRank($sender->getName(), $nextRank);

                $currentRank = Prisons::get()->getRank($sender->getName());

                $sender->sendMessage(str_replace("{RANK}", $currentRank, $this->messages->get("ranked-up")));

                $pp = Prisons::get()->getServer()->getPluginManager()->getPlugin("PurePerms");

                if($pp instanceof PurePerms) {
                    foreach ($data["added-permissions"] as $permission) {
                        $pp->getUserDataMgr()->setPermission($sender, $permission);
                    }
                }

            } else {
                $sender->sendMessage(str_replace("{NEEDED}", $data["price"], $this->messages->get("not-enough-money-rankup")));
            }

        } else {
            $sender->sendMessage(TextFormat::RED . "Configuration error!");
            Prisons::get()->getLogger()->critical("Configuration error detected, fix immediately or reset your config file.");
        }

        return true;
    }

}