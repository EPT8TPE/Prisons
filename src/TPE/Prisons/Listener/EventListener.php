<?php

declare(strict_types=1);

namespace TPE\Prisons\Listener;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat;
use TPE\Prisons\Prisons;
use pocketmine\event\player\PlayerJoinEvent;
use TPE\Prisons\Utils;

final class EventListener implements Listener {

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        if(($member = Prisons::get()->getPlayerManager()->getPlayer($player)) === null) $member = Prisons::get()->getPlayerManager()->createPlayer($player);
        if($member->getUsername() !== $player->getName()) $member->setUsername($player->getName());
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBreak(BlockBreakEvent $event) : void {
        if($event->getPlayer()->getWorld()->getFolderName() === Utils::getPlotWorld()->getFolderName() || $event->getPlayer()->hasPermission("prisons.nomine.bypass"))  {
            return;
        } else {
            $event->cancel();
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
        if($event->getPlayer()->getWorld()->getFolderName() === Utils::getPlotWorld()->getFolderName() || $event->getPlayer()->hasPermission("prisons.nomine.bypass")) {
            return;
        } else {
            $event->cancel();
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
        $member = Prisons::get()->getPlayerManager()->getPlayer($event->getPlayer());
        $format = str_replace(["{PRISON_RANK}", "{PRISON_PRESTIGE}"], [Utils::getRankName($member->getPrisonRank()), $member->getPrestige()], $event->getFormat());
        $event->setFormat($format);
    }
}
