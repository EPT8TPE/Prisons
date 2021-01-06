<?php

declare(strict_types=1);

namespace TPE\Prisons\Listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use TPE\Prisons\Prisons;
use pocketmine\event\player\PlayerChatEvent;
use TPE\Prisons\Utils\Configuration;

class EventListener implements Listener {

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer()->getName();

        if(!Prisons::getProvider()->checkPlayerRegistered($player)) {
            Prisons::getProvider()->registerPlayer($player);
        }

        $cfg = new Config(Prisons::get()->getDataFolder() . "playerlist.yml");

        if(!in_array($player, $cfg->get("players"))) {
            $array = $cfg->get("players");
            $array[] = $player;

            $cfg->set("players", $array);
            $cfg->save();
        }
    }
    
    
    /**
    * @param PlayerChatEvent $event
    * @priority HIGHEST
    * @ignoreCancelled true
    */
    public function onChat(PlayerChatEvent $event) {
        $format = str_replace(["{PRISON_RANK}", "{PRISON_ASCENSION}"], [Configuration::getRankName($event->getPlayer()->getName()), Prisons::get()->getAscension($event->getPlayer()->getName())], $event->getFormat());
        $event->setFormat($format);
    }
    
    public function onBreak(BlockBreakEvent $event) {
        if(Prisons::get()->isInMine($event->getBlock()) || $event->getPlayer()->hasPermission("no-mine-bypass") || $event->getPlayer()->getLevel()->getName() === Prisons::get()->getConfig()->get("plot-world")) {
            return;
        } else {
            $event->setCancelled(true);
            $event->getPlayer()->sendMessage(Prisons::get()->getConfig()->get("no-breaking-here"));
        }
    }
    
    public function onPlace(BlockPlaceEvent $event) {
        if(Prisons::get()->isInMine($event->getBlock()) || $event->getPlayer()->hasPermission("no-mine-bypass") || $event->getPlayer()->getLevel()->getName() === Prisons::get()->getConfig()->get("plot-world")) {
            return;
        } else {
            $event->setCancelled(true);
            $event->getPlayer()->sendMessage(Prisons::get()->getConfig()->get("no-placing-here"));
        }
    }
    
}
