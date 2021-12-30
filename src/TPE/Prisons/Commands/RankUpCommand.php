<?php

declare(strict_types=1);

namespace TPE\Prisons\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use TPE\Prisons\Listener\PrisonListener\PrisonRankUpEvent;
use TPE\Prisons\Prisons;
use TPE\Prisons\Utils;
use _64FF00\PurePerms\PurePerms;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\plugin\PluginOwned;

final class RankUpCommand extends Command implements PluginOwned {

    /**
    * RankUpCommand constructor.
    */
    public function __construct() {
        parent::__construct("rankup", "Prison rank up command.", null, ["ru"]);
        $this->setPermission("prisons.rankup");
    }

     /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
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

        $member = Prisons::get()->getPlayerManager()->getPlayer($sender);
        $currentRank = $member->getPrisonRank();

        $nextRank = $currentRank;
        $nextRank++;

        if($nextRank == "aa") {
            $sender->sendMessage(Utils::getMessage("max-rank"));
            return;
        }

        $currentPrestige = $member->getPrestige();

        $price = Utils::getRankUpPrice($currentRank, $currentPrestige);

        if(Utils::processTransaction($sender, $price)) {
            $event = (new PrisonRankUpEvent($sender, (string)$nextRank, $currentRank, Utils::getRankCommands($currentRank), Utils::getRankPermissions($currentRank, "added"), Utils::getRankPermissions($currentRank, "removed")));
            $event->call();
            if($event->isCancelled()) return;

            $member = Prisons::get()->getPlayerManager()->getPlayer($event->getPlayer());
            $member->setPrisonRank($nextRank);

            foreach ($event->getCommands() as $command) {
                Prisons::get()->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{PLAYER}", $event->getPlayer()->getName(), $command));
            }
                
            $manager = Prisons::get()->getServer()->getPluginManager()->getPlugin("PurePerms");
            
            if($manager instanceof PurePerms) {
                foreach ($event->getAddedPermissions() as $permission) {
                    $manager->getUserDataMgr()->setPermission($event->getPlayer(), $permission);
                }

                foreach ($event->getRemovedPermissions() as $permission) {
                    $manager->getUserDataMgr()->unsetPermission($event->getPlayer(), $permission);
                }
            }
            
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
    }

    /**
    * @return Plugin
    */
    public function getOwningPlugin(): Plugin {
        return Prisons::get();
    }

}
