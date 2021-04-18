<?php

declare(strict_types=1);

namespace TPE\Prisons\Listener;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat;
use TPE\Prisons\Data\BaseDB;
use TPE\Prisons\Prisons;
use pocketmine\event\player\PlayerPreLoginEvent;
use TPE\Prisons\Utils;

final class EventListener implements Listener {

    /**
     * @param PlayerPreLoginEvent $event
     * @return void
     */
    public function onPlayerLogin(PlayerPreLoginEvent $event) : void {
        Prisons::get()->getDatabaseConnector()->executeInsert(
            BaseDB::PRISONS_REGISTER_PLAYER,
            ['username' => $event->getPlayer()->getLowerCaseName()]
        );
        Prisons::get()->getPrisonRank($event->getPlayer(), function(array $rows) use($event) {
            foreach($rows as $row) {
                if(isset($row['prisonrank'])) {
                    $cr = $row['prisonrank'];
                } else {
                    $cr = "a";
                }
                Prisons::get()->setPrisonRank($event->getPlayer(), $cr);
            }
            Prisons::get()->getPrisonPrestige($event->getPlayer(), function(array $rows) use($event) {
                foreach($rows as $row) {
                    if(isset($row['prestige'])) {
                        $cp = $row['prestige'];
                    } else {
                        $cp = 0;
                    }
                }
                Prisons::get()->setPrisonPrestige($event->getPlayer, $cp);
            });
        });
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBreak(BlockBreakEvent $event) : void {
        if(Prisons::get()->isInMine($event->getBlock()) || $event->getPlayer()->getLevel() === Utils::getPlotWorld() || $event->getPlayer()->hasPermission("prisons.nomine.bypass"))  {
            return;
        } else {
            $event->setCancelled();
            if(!is_null(Utils::getMessage("no-breaking-here"))) {
                $event->getPlayer()->sendMessage(Utils::getMessage("no-breaking-here"));
            } else {
                $event->getPlayer()->sendMessage(TextFormat::RED . "Configuration error detected!");
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @return void
     */
    public function onPlace(BlockPlaceEvent $event) : void {
        if(Prisons::get()->isInMine($event->getBlock()) || $event->getPlayer()->getLevel() === Utils::getPlotWorld() || $event->getPlayer()->hasPermission("prisons.nomine.bypass")) {
            return;
        } else {
            $event->setCancelled();
            if(!is_null(Utils::getMessage("no-placing-here"))) {
                $event->getPlayer()->sendMessage(Utils::getMessage("no-placing-here"));
            } else {
                $event->getPlayer()->sendMessage(TextFormat::RED . "Configuration error detected!");
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     * @priority HIGHEST
     * @return void
     */
    public function onChat(PlayerChatEvent $event): void {
        $event->setCancelled();
        if(Prisons::get()->getPermissionManager() === "pureperms") {
            Prisons::get()->getPrisonRank($event->getPlayer(), function (array $rows) use ($event) {
                $currentRank = "";
                foreach ($rows as $row) {
                    $currentRank = $row['prisonrank'];
                }

                Prisons::get()->getPrisonPrestige($event->getPlayer(), function (array $rows) use ($event, $currentRank) {
                    $currentPrestige = 0;
                    foreach ($rows as $row) {
                        $currentPrestige = $row['prestige'];
                    }

                    $format = str_replace(["{PRISON_RANK}", "{PRISON_PRESTIGE}"], [Utils::getRankName($currentRank), $currentPrestige], $event->getFormat());
                    // Am looking for some assistance on this issue as it is not ideal. 
                    Prisons::get()->getServer()->broadcastMessage($format);
                });
            });
        }
    }
}
