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

    public function onChat(PlayerChatEvent $event) {
        $event->setFormat(str_replace(["{RANK}", "{PRESTIGE}"], [Configuration::getRankName($event->getPlayer()->getName()), Prisons::get()->getAscension($event->getPlayer()->getName())], $event->getForamt()));
    }



}
