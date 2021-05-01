<?php

declare(strict_types=1);

namespace TPE\Prisons;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use TPE\Prisons\Listener\EventListener;
use falkirks\minereset\Mine;
use pocketmine\block\Block;
use TPE\Prisons\Commands\RankUpCommand;
use TPE\Prisons\Commands\PrestigeCommand;
use TPE\Prisons\PlayerManager;

final class Prisons extends PluginBase {

    private static $instance;

    /** @var DataConnector */
    private $database;
    
    /** @var \falkirks\minereset\MineReset */
    private $mineReset;

    /** @var string */
    public $permissionManager;
    
    /** @var PlayerManager **/
    
    private $playerManager;

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        $this->checkUpdate();
        $this->initDatabase();
        $this->saveDefaultConfig();
        $this->mineReset = $this->getServer()->getPluginManager()->getPlugin("MineReset");
        $this->playerManager = new PlayerManager();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->registerCommands();
        $this->permissionManager = "pureperms";
    }
    
    public function onDisable() : void {
        if($this->database !== null) {
            $this->database->waitAll();
            $this->database->close();
        }
    }

    public function checkUpdate() : void {
        if($this->getConfig()->get("version") !== 2.1) {
            $this->getLogger()->notice("Your configuration file is outdated, updating...");
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "old_config.yml");
            rename($this->getDataFolder() . "sqlite.sql", $thus->getDataFolder() . "old_sqlitel.sql");
            rename($this->getDataFolder() . "mysql.sql", $this->getDataFolder() . "old_mysql.sql");
            $this->saveDefaultConfig();
            $this->getConfig()->reload();
        }
    }

    /**
     * @return self
     */
    public static function get() : self {
        return self::$instance;
    }

    /**
     * @return void
     */
    private function initDatabase() : void {
        $this->database = $database = libasynql::create($this, $this->getConfig()->get("database"), ["mysql" => "mysql.sql", "sqlite" => "sqlite.sql"]);
        $this->database->executeGeneric("prisons.init");
        $this->database->waitAll();
    }

    /**
     * @return DataConnector
     *
     * Returns a data connector that is used with libasyncsql.
     */
    public function getDataBase() : DataConnector {
        return $this->database;
    }

    /**
     * @return string
     *
     * Returns the name of the desired permission manager.
     */
    public function getPermissionManager() : string {
        return $this->permissionManager;
    }
    
    /** 
    * @return PlayerManager
    *
    * Returns a class used to get player data.
    */
    public function getPlayerManager() : PlayerManager {
        return $this->playerManager;
    }

    /**
     * @param Block $block
     * @return bool
     *
     * Returns true if the block the player is mining
     * is found to be in a registered mine.
     */
    public function isInMine(Block $block) : bool {
        foreach ($this->mineReset->getMineManager() as $mine) {
            /** @var Mine $mine */
            if($mine->isPointInside($block)) {
                return true;
            }
        }
        return false;
    }
    
    public function registerCommands() : void {
        $this->getServer()->getCommandMap()->register("Prisons", new RankUpCommand());
        $this->getServer()->getCommandMap()->register("Prisons", new PrestigeCommand());
    }
}
