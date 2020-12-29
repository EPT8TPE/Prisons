<?php

declare(strict_types = 1);

namespace TPE\Prisons\Utils;

use pocketmine\utils\Config;

use TPE\Prisons\Prisons;

class Configuration {

    private static function getConfig() : Config {
        return Prisons::get()->getConfig();
    }

    public static function getRankName(string $player) : string {
        return self::getConfig()->getNested("ranks." . Prisons::get()->getRank($player) . ".rankName");
    }

}
