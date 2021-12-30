<?php

declare(strict_types=1);

namespace TPE\Prisons\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use TPE\Prisons\Listener\PrisonListener\PrisonPrestigeEvent;
use TPE\Prisons\Prisons;
use TPE\Prisons\Utils;
use _64FF00\PurePerms\PurePerms;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\plugin\PluginOwned;

final class PrestigeCommand extends Command implements PluginOwned {

    /**
    * PrestigeCommand constructor.
    */
    public function __construct() {
        parent::__construct("prestige", "Prison prestige command.", null, ['p']);
        $this->setPermission("prisons.prestige");
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
            if(!is_null(Utils::getMessage("prestige-usage"))) {
                $sender->sendMessage(Utils::getMessage("prestige-usage"));
            } else {
                $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
            }
            return;
        }

        $member = Prisons::get()->getPlayerManager()->getPlayer($sender);
        $currentRank = $member->getPrisonRank();

        if($currentRank !== "z") {
            if(!is_null(Utils::getMessage("not-rank-z"))) {
                $sender->sendMessage(Utils::getMessage("not-rank-z"));
            } else {
                $sender->sendMessage(TextFormat::RED . "Configuration error detected!");
            }
            return;
        }
        
        $currentPrestige = $member->getPrestige();

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
              $event = (new PrisonPrestigeEvent($sender, $nextPrestige, $currentPrestige, Utils::getPrestigeCommands($currentPrestige), Utils::getPrestigePermissions($currentPrestige, "added"), Utils::getPrestigePermissions($currentPrestige, "removed")));
              $event->call();
              if($event->isCancelled()) return;
              
              $member = Prisons::get()->getPlayerManager()->getPlayer($event->getPlayer());
              $member->setPrestige($nextPrestige);
              $member->setPrisonRank("a");

              foreach ($event->getCommands() as $command) {
                  str_replace()Prisons::get()->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), str_replace("{PLAYER"}, $event->getPlayer()->getName(), $command));
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
              
              if(empty(Prisons::get()->getConfig()->get("world-name"))) {
                   $sender->teleport(Prisons::get()->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
              } else {
                   if(Prisons::get()->getServer()->getWorldManager()->getWorldByName((string)Prisons::get()->getConfig()->get("world-name")) === null) {
                       $sender->sendMessage(TextFormat::RED . "World specified in config is invalid, please contact an admin!");
                   } else {
                       $sender->teleport(Prisons::get()->getServer()->getWorldManager()->getWorldByName((string)Prisons::get()->getConfig()->get("world-name"))->getSpawnLocation());
                   }
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
    }

    /**
     * @return Plugin
     */
    public function getOwningPlugin(): Plugin {
        return Prisons::get();
    }
}
