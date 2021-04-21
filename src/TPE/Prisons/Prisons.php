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
        $this->initDatabase();
        $this->saveDefaultConfig();
        $this->checkUpdate();
        $this->mineReset = $this->getServer()->getPluginManager()->getPlugin("MineReset");
        $this->playerManager = new PlayerManager();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->registerCommands();
        $this->permissionManager = "pureperms";
    }
    
    public function onDisable() : void {
        if($this->database instanceof DataConnector) {
            $this->database->waitAll();
            $this->database->close();
        }
    }

    public function checkUpdate() : void {
        if($this->getConfig()->get("version") !== 2.0) {
            $this->getLogger()->notice("Your configuration file is outdated, updating...");
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "old_config.yml");
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
    * @param Player $player
    * @return ?string
    *
    * Returns the players current rank if they exist in the 
    * database else returns null.
    */
    public function getPrisonRank(Player $player) : ?string {
        return $this->getPlayerManager()->getPlayer($player)->getPrisonRank() ?? null;
    }
    
    /** 
    * @param Player $player
    * @param string $rank
    * @return bool
    *
    * Set the prison rank of a specific player, returns true
    * if successfull and false if not.
    */
    public function setPrisonRank(Player $player, string $rank) : bool {
        $member = $this->getPlayerManager()->getPlayer($player) ?? null;
        if(!is_null($member)) {
            $member->setPrisonRank($rank);
            return true;
        } else {
            return false;
        }
        return false;
    }
    
    /** 
    * @param Player $player
    * @return ?int
    *
    * Returns the players current prestige if they exist in the 
    * database else returns null.
    */
    public function getPrestige(Player $player) : ?int {
        return $this->getPlayerManager()->getPlayer($player)->getPrestige() ?? null;
    }
    
    /** 
    * @param Player $player
    * @param int $prestige
    * @return bool
    *
    * Set the prestige of a specific player, returns true 
    * if successfull and false if not.
    */
    public function setPrestige(Player $player, int $prestige) : bool {
        $member = $this->getPlayerManager()->getPlayer($player) ?? null;
        if(!is_null($member)) {
            $member->setPrestige($prestige);
            return true;
        } else {
            return false;
        }
        return false;
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
