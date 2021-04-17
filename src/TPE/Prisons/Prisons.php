<?php

declare(strict_types=1);

namespace TPE\Prisons;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use TPE\Prisons\Data\BaseDB;
use TPE\Prisons\Listener\EventListener;
use falkirks\minereset\Mine;
use pocketmine\block\Block;

final class Prisons extends PluginBase {

    private static $instance;

    /** @var DataConnector */
    private $database;
    
    /** @var \falkirks\minereset\MineReset */
    private $mineReset;

    /** @var string */
    public $permissionManager;

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        $this->initDatabase();
        $this->saveDefaultConfig();
        $this->checkUpdate();
        $this->mineReset = $this->getServer()->getPluginManager()->getPlugin("MineReset");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        if($this->getConfig()->get("permission-manager") === "pureperms") {
            $this->permissionManager = "pureperms";
        } elseif($this->getConfig()->get("permission-manager") === "hierarchy") {
            $this->permissionManager = "hierarchy";
        } elseif(!is_null($this->getServer()->getPluginManager()->getPlugin("PurePerms")) && is_null($this->getServer()->getPluginManager()->getPlugin("Hierarchy"))) {
            $this->permissionManager = "pureperms";
            $this->getConfig()->set("permission-manager", "pureperms");
            $this->getLogger()->info("No permission manager specified in config, automatically assigned PurePerms.");
        } elseif(is_null($this->getServer()->getPluginManager()->getPlugin("PurePerms")) && !is_null($this->getServer()->getPluginManager()->getPlugin("Hierarchy"))) {
            $this->permissionManager = "hierarchy";
            $this->getConfig()->set("permission-manager", "hierarchy");
            $this->getLogger()->info("No permission manager specified in config, automatically assigned Hierarchy");
        } else {
            $this->getLogger()->critical("Either you have neither hierarchy or pure perms installed or you have both installed!");
            $this->getLogger()->critical("Please resolve this issue before using this plugin!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }
    
    public function onDisable() : void {
        if(isset($this->database)) $this->database->close();
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
        $this->database->executeGeneric(BaseDB::INIT_TABLES);
        $this->database->waitAll();
    }

    /**
     * @return DataConnector
     *
     * Returns a data connector that is used with libasyncsql.
     */
    public function getDatabaseConnector() : DataConnector {
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
     * @param Player $player
     * @param callable $callable
     * @return void
     *
     * Get the prison rank of a specified player.
     */
    public function getPrisonRank(Player $player, callable $callable) : void {
        $this->database->executeSelect(BaseDB::GET_PRISON_RANK, [
            "username" => $player->getLowerCaseName(),
        ], $callable);
    }

    /**
     * @param Player $player
     * @param string $rank
     * @return void
     *
     * Set the prison rank of a specified player.
     */
    public function setPrisonRank(Player $player, string $rank) : void {
        $this->getDatabaseConnector()->executeInsert(
            BaseDB::UPDATE_RANK,
            ['username' => $player->getLowerCaseName(), 'prisonrank' => $rank]
        );
    }

    /**
     * @param Player $player
     * @param callable $callable
     * @return void
     *
     * Get the prison prestige level of a specified player.
     */
    public function getPrisonPrestige(Player $player, callable $callable) : void {
        $this->database->executeSelect(BaseDB::GET_PRISON_PRESTIGE, [
            "username" => $player->getLowerCaseName(),
        ], $callable);
    }

    /**
     * @param Player $player
     * @param int $prestige
     * @return void
     *
     * Set the prison prestige level of a specified player.
     */
    public function setPrisonPrestige(Player $player, int $prestige) : void {
        $this->getDatabaseConnector()->executeInsert(
            BaseDB::UPDATE_PRESTIGE,
            ["username" => $player->getLowerCaseName()]
        );
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
}
