<?php

declare(strict_types=1);

namespace TPE\Prisons;

use onebone\economyapi\EconomyAPI;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

final class Utils {

    /**
     * @return array
     *
     * Returns an array of messages specified in the config.
     */
    private static function getMessages() : array {
        return Prisons::get()->getConfig()->get("messages");
    }

    /**
     * @param string $message
     * @return string|null
     *
     * Returns a specified message.
     */
    public static function getMessage(string $message) : ?string {
        if(self::getMessages()[$message]) {
            return TextFormat::colorize(self::getMessages()[$message]);
        } else {
            return null;
        }
    }

    /**
     * @return array
     *
     * Returns an array containing all ranks specified in the config.
     */
    public static function getRanks() : array {
        return Prisons::get()->getConfig()->get("ranks");
    }

    /**
     * @return array
     *
     * Returns an array containing all prestiges specified in the config.
     */
    public static function getPrestiges() : array {
        return Prisons::get()->getConfig()->get("prestiges");
    }

    /**
     * @param string $rank
     * @param string $type
     * @return array|null
     *
     * Returns an array of permissions added/removed depending upon
     * what is specified, will return null if the rank does not exist.
     */
    public static function getRankPermissions(string $rank, string $type = "added") : ?array {
        switch (strtolower($type)) {
            case "added":
                if(self::getRanks()[$rank]) {
                    return self::getRanks()[$rank]['added-permissions'];
                } else {
                    return null;
                }
            break;

            case "removed":
                if(self::getRanks()[$rank]) {
                    return self::getRanks()[$rank]['removed-permissions'];
                } else {
                    return null;
                }
            break;

            default:
                return null;
            break;
        }
    }

    /**
     * @param int $prestige
     * @param string $type
     * @return array|null
     *
     * Returns an array of permissions added/removed depending upon
     * what is specified, will return null if the prestige level does
     * not exist.
     */
    public static function getPrestigePermissions(int $prestige, string $type = "added") : ?array {
        switch (strtolower($type)) {
            case "added":
                if(self::getPrestiges()[$prestige]) {
                    return self::getPrestiges()[$prestige]['added-permissions'];
                } else {
                    return null;
                }
            break;

            case "removed":
                if(self::getPrestiges()[$prestige]) {
                    return self::getPrestiges()[$prestige]['removed-permissions'];
                } else {
                    return null;
                }
            break;

            default:
                return null;
            break;
        }
    }

    /**
     * @param string $rank
     * @param int $prestige
     * @return float|null
     *
     *
     * Returns a rank up price for the specified rank,
     * based on a players prestige, prestige multiplier
     * and the base price provided for ranking up.
     */
    public static function getRankUpPrice(string $rank, int $prestige) : ?float {
        if(self::getRanks()[$rank] && self::getPrestiges()[$prestige]) {
            if($prestige === 0) {
                return self::getRanks()[$rank]['price'];
            } else {
                return self::getRanks()[$rank]['price'] *= $prestige * self::getPrestigeMultiplier();
            }
        } else {
            return null;
        }
    }

    /**
     * @param int $prestige
     * @return int|null
     *
     * Returns a prestige price for the specified prestige level,
     * if the prestige level does not exist, null is returned.
     */
    public static function getPrestigePrice(int $prestige) : ?int {
        if(self::getPrestiges()[$prestige]) {
            return self::getPrestiges()[$prestige]['price'];
        } else {
            return null;
        }
    }

    /**
     * @param string $rank
     * @return array|null
     *
     * Returns an array of commands for the specified rank,
     * if the rank does not exist, null is returned.
     */
    public static function getRankCommands(string $rank) : ?array {
        if(self::getRanks()[$rank]) {
            return self::getRanks()[$rank]['commands'];
        } else {
            return null;
        }
    }

    /**
     * @param int $prestige
     * @return array|null
     *
     * Returns an array of commands for the specified prestige
     * level, if the prestige level does not exist, null is
     * returned.
     */
    public static function getPrestigeCommands(int $prestige) : ?array {
        if(self::getPrestiges()[$prestige]) {
            return self::getPrestiges()[$prestige]['commands'];
        } else {
            return null;
        }
    }

    /**
     * @param string $rank
     * @return string|null
     *
     * Returns a the name specified in the config for a specific
     * rank, if the rank doesn't exist, null is returned.
     */
    public static function getRankName(string $rank) : ?string {
        if(self::getRanks()[$rank]) {
            return self::getRanks()[$rank]['rankName'];
        } else {
            return null;
        }
    }

    /**
     * @return float
     *
     * Returns a multiplier specified in config.
     */
    public static function getPrestigeMultiplier() : float {
        return (float)Prisons::get()->getConfig()->get("prestige-multiplier");
    }

    /**
     * @param Player $player
     * @param float $price
     * @return bool
     *
     * Returns true if the player has sufficient money.
     */
    public static function processTransaction(Player $player, float $price) : bool {
        if(EconomyAPI::getInstance()->myMoney($player) >= $price) {
            EconomyAPI::getInstance()->reduceMoney($player, $price);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Level|null
     *
     * Returns the specified plot level if it exists else
     * it will return null.
     */
    public static function getPlotWorld() : ?Level {
        if(!is_null(Prisons::get()->getServer()->getLevelByName((string)Prisons::get()->getConfig()->get("plot-world")))) {
            return Prisons::get()->getServer()->getLevelByName((string)Prisons::get()->getConfig()->get("plot-world"));
        } else {
            return null;
        }
    }
}