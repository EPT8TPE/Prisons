<?php

declare(strict_types = 1);

namespace TPE\Prisons;

use TPE\Prisons\PrisonPlayer;
use pocketmine\Player;
use pocketmine\utils\UUID;

class PlayerManager {
  
  /** @var self **/
  private static $instance;
  
  /** @var PrisonPlayer[] **/
  private $players = [];
  
  public function __construct() {
      self::$instance = $this;
    
      Prisons::get()->getDataBase()->executeSelect("prisons.load", [], function (array $rows) : void {
          foreach($rows as $row) {
              $this->players[$row["uuid"]] = new PrisonPlayer(UUID::fromString($row["uuid"]), $row["username"], $row["prisonrank"], $row["prestige"]);
          }
      });
  }
  
  public static function get() : self {
      return self::$instance;
  }
  
  public function createPlayer(Player $player) : PrisonPlayer {
      Prisons::get()->getDataBase()->executeInsert("prisons.create", [
          "uuid" => $player->getUniqueId()->toString(),
          "username" => $player->getName(),
          "prisonrank" => "a",
          "prestige" => 0
      ]);
      $this->players[$player->getUniqueId()->toString()] = new PrisonPlayer($player->getUniqueId(), $player->getName(), "a", 0);
      return $this->players[$player->getUniqueId()->toString()];
  }  
  
  public function getPlayerByUuid(UUID $uuid) : ?PrisonPlayer {
      return $this->players[$uuid->toString()] ?? null;
  }
  
  public function getPlayer(Player $player) : ?PrisonPlayer {
      return $this->getPlayerByUuid($player->getUniqueId());
  }
  
  public function getPlayersByName(string $name) : ?PrisonPlayer {
      foreach ($this->players as $player) {
            if (strtolower($player->getUsername()) === strtolower($name)) return $player;
      }
      return null;
  }
}
