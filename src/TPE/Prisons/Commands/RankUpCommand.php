<?php

declare(strict_types=1);

namespace TPE\Prisons\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use TPE\Prisons\Listener\PrisonListener\PrisonRankUpEvent;
use TPE\Prisons\Prisons;
use TPE\Prisons\Utils;

final class RankUpCommand extends Command implements PluginIdentifiableCommand {

    public function __construct() {
        parent::__construct("rankup", "Prison rank up command.", null, ["ru"]);
        $this->setPermission("prisons.rankup");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->testPermission($sender)) {
            if(!is_null(Utils::getMessage("no-perms"))) {
                $sender->sendMessage(Utils::getMessage("no-perms"));
            } else {
                $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
            }
            return;
        }

        if(!$sender instanceof Player) {
            if(!is_null(Utils::getMessage("must-be-player"))) {
                $sender->sendMessage(Utils::getMessage("must-be-player"));
            } else {
                $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
            }
            return;
        }

        if(isset($args[0])) {
            if(!is_null(Utils::getMessage("rankup-usage"))) {
                $sender->sendMessage(Utils::getMessage("rankup-usage"));
            } else {
                $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
            }
            return;
        }

        Prisons::get()->getPrisonRank($sender, function (array $rows) use($sender) {
            $currentRank = "";
            foreach ($rows as $row) {
                $currentRank = $row['prisonrank'];
            }

            $nextRank = $currentRank;
            $nextRank++;

            if($nextRank == "aa") {
                $sender->sendMessage(Utils::getMessage("max-rank"));
                return;
            }

            Prisons::get()->getPrisonPrestige($sender, function (array $rows) use($sender, $currentRank, $nextRank) {
                $currentPrestige = 0;
                foreach ($rows as $row) {
                    $currentPrestige = $row['prestige'];
                }

                $price = Utils::getRankUpPrice($currentRank, $currentPrestige);

                if(Utils::processTransaction($sender, $price)) {
                    (new PrisonRankUpEvent($sender, (string)$nextRank, $currentRank, Utils::getRankCommands($currentRank), Utils::getRankPermissions($currentRank, "added"), Utils::getRankPermissions($currentRank, "removed")))->call();

                    if(!is_null(Utils::getMessage("ranked-up"))) {
                        $message = Utils::getMessage("ranked-up");
                        $sender->sendMessage(str_replace("{RANK}", Utils::getRankName($nextRank), $message));
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
                    }
                } else {
                    if(!is_null(Utils::getMessage("not-enough-money-rankup"))) {
                        $message = Utils::getMessage("not-enough-money-rankup");
                        $sender->sendMessage(str_replace("{NEEDED}", Utils::getRankUpPrice($currentRank, $currentPrestige), $message));
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
                    }
                }
            });
        });
    }

    public function getPlugin(): Plugin {
        return Prisons::get();
    }

}
