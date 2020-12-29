<?php


namespace TPE\Prisons\Utils;


use pocketmine\utils\Config;



use TPE\Prisons\Prisons;

class Configuration {

    private static function getConfig() : Config {
        return Prisons::get()->getConfig();
    }

    public static function getRankName(string $player) : string {
        return self::getConfig()->getNested("ranks." . self::getRank($player) . ".rankName");
    }

}