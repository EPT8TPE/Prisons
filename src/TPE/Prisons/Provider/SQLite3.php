<?php

declare(strict_types=1);

namespace TPE\Prisons\Provider;

use pocketmine\utils\Config;
use TPE\Prisons\Prisons;

class SQLite3 {

    private $db = null;

    private $playerData = [];

    public function initDb() : void {
        $this->db = new \SQLite3( Prisons::get()->getDataFolder() . "prisons.db");

        if(!$this->db) {
            Prisons::get()->getLogger()->critical("Could not access the prison database, disabling...");
            Prisons::get()->getServer()->getPluginManager()->disablePlugin(Prisons::get());
        } else {
            if(!$this->db->exec("CREATE TABLE IF NOT EXISTS players(username TEXT, rank TEXT, ascension INT);")) {
                Prisons::get()->getLogger()->critical("Could not create player table, disabling...");
                Prisons::get()->getServer()->getPluginManager()->disablePlugin(Prisons::get());
            }

            $cfg = new Config(Prisons::get()->getDataFolder() . "playerlist.yml", Config::YAML);

            foreach ($cfg->get("players") as $player) {
                $query = $this->db->query("SELECT * FROM players WHERE username ='{$player}';");
                $fa = $query->fetchArray(SQLITE3_NUM);
                $this->playerData[strtolower($fa[0])] = ["username" => $fa[0],
                    "rank" => $fa[1],
                    "ascension" => $fa[2]
                    ];
            }
        }
    }

    public function checkPlayerRegistered(string $player) : bool {
        if(isset($this->playerData[strtolower($player)])) {
            return true;
        } else {
            return false;
        }
    }

    public function registerPlayer(string $player) : void {
        if(!$this->db->query("INSERT INTO players (username, rank, ascension) VALUES('{$player}', 'a', 0);")) {
            Prisons::get()->getLogger()->error("Error inserting player data!");
        } else {
            $this->playerData[strtolower($player)] = ["username" => $player, "rank" => "a", "ascension" => 0];
        }
    }

    public function getPlayerData(string $player, string $type) {
        return $this->playerData[strtolower($player)][$type];
    }

    public function setPlayerData(string $player, string $type, $data) : void {
        $this->playerData[strtolower($player)][$type] = $data;
    }

    public function closeDb() : void {
        Prisons::get()->getLogger()->info("Saving player data...");
        foreach ($this->playerData as $playerDatum) {
            $i = 0;
            $playerDatum[0] = $playerDatum["username"];
            $playerDatum[1] = $playerDatum["rank"];
            $playerDatum[2] = $playerDatum["ascension"];

            if(!$this->db->exec("UPDATE players SET rank='{$playerDatum[1]}', ascension = '{$playerDatum[2]}' WHERE username='{$playerDatum[0]}';")) {
                Prisons::get()->getLogger()->critical("Failed to update player data!");
            }
        }
    }

}