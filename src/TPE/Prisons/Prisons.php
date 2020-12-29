<?php

declare(strict_types=1);

namespace TPE\Prisons;

use pocketmine\plugin\PluginBase;
use TPE\Prisons\Commands\AscendCommand;
use TPE\Prisons\Commands\RankUpCommand;
use TPE\Prisons\Commands\ResetCommand;
use TPE\Prisons\Listener\EventListener;
use TPE\Prisons\Provider\SQLite3;
use TPE\Prisons\Utils\Configuration;

class Prisons extends PluginBase {

    private static $instance;

    private static $provider;

    private static $configuration;

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        $this->checkUpdate();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register("Prisons", new RankUpCommand());
        $this->getServer()->getCommandMap()->register("Prisons", new AscendCommand());

        self::$provider = new SQLite3();
        self::$provider->initDb();
        self::$configuration = new Configuration();
        
        $this->saveResource("playerlist.yml");
    }
    
    public function onDisable() : void {
        if(isset(self::$provider)) {
            self::$provider->closeDb();
        }
        
        $this->saveResource("playerlist.yml");
    }

    public function checkUpdate() : void {
        if($this->getConfig()->get("version") !== 1) {
            $this->getLogger()->notice("Your configuration file is outdated, updating...");
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "old_config.yml");
            $this->saveDefaultConfig();
            $this->getConfig()->reload();
        }
    }

    public static function getProvider() : SQLite3 {
        return self::$provider;
    }

    public static function get() : self {
        return self::$instance;
    }

    public function getRank(string $player) : string {
        return self::getProvider()->getPlayerData($player, "rank");
    }

    public function getAscension(string $player) : int {
        return self::getProvider()->getPlayerData($player, "ascension");
    }

    public function setRank(string $player, string $rank) : void {
        self::getProvider()->setPlayerData($player, "rank", $rank);
    }

    public function setAscension(string $player, int $ascension) : void {
        self::getProvider()->setPlayerData($player, "ascension", $ascension);
    }



}
