<?php

declare(strict_types=1);

namespace TPE\Prisons\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use TPE\Prisons\Listener\PrisonListener\PrisonPrestigeEvent;
use TPE\Prisons\Prisons;
use TPE\Prisons\Utils;

final class PrestigeCommand extends Command implements PluginIdentifiableCommand {

    public function __construct() {
        parent::__construct("prestige", "Prison prestige command.", null, ['p']);
        $this->setPermission("prisons.prestige");
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
            if(!is_null(Utils::getMessage("prestige-usage"))) {
                $sender->sendMessage(Utils::getMessage("prestige-usage"));
            } else {
                $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
            }
            return;
        }

        Prisons::get()->getPrisonRank($sender, function (array $rows) use($sender) {
            foreach ($rows as $row) {
                $currentRank = $row['prisonrank'];
            }

            if($currentRank !== "z") {
                if(!is_null(Utils::getMessage("not-rank-z"))) {
                    $sender->sendMessage(Utils::getMessage("not-rank-z"));
                } else {
                    $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
                }
                return;
            }

            Prisons::get()->getPrisonPrestige($sender, function (array $rows) use($sender) {
                foreach ($rows as $row) {
                    $currentPrestige = $rows['prestige'];
                }

                $nextPrestige = $currentPrestige;
                $nextPrestige++;

                if($nextPrestige > array_key_last(Utils::getPrestiges())) {
                    if(!is_null(Utils::getMessage("max-prestige-level"))) {
                        $sender->sendMessage(Utils::getMessage("max-prestige-level"));
                    } else {
                        $sender->sendMessage(TextFormat::RED . "You are at the max prestige level!");
                    }
                    return;
                }

                if(Utils::processTransaction($sender, Utils::getPrestigePrice($nextPrestige))) {
                    (new PrisonPrestigeEvent($sender, $nextPrestige, $currentPrestige, Utils::getPrestigeCommands($currentPrestige), Utils::getPrestigePermissions($currentPrestige, "added"), Utils::getPrestigePermissions($currentPrestige, "removed")));

                    if(empty(Prisons::get()->getConfig()->get("world-name"))) {
                        $sender->teleport(Prisons::get()->getServer()->getDefaultLevel()->getSpawnLocation());
                    } else {
                        $sender->teleport(Prisons::get()->getServer()->getLevelByName((string)Prisons::get()->getConfig()->get("world-name")));
                    }

                    if(!is_null(Utils::getMessage("successfully-prestiged"))) {
                        $message = Utils::getMessage("successfully-prestiged");
                        $sender->sendMessage(str_replace("{PRESTIGE}", $nextPrestige, $message));
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
                    }
                } else {
                    if(!is_null(Utils::getMessage("not-enough-money-prestige"))) {
                        $message = Utils::getMessage("not-enough-money-prestige");
                        $sender->sendMessage(str_replace("{NEEDED}", Utils::getPrestigePrice($currentPrestige), $message));
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
