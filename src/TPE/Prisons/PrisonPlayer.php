<?php

declare(strict_types = 1);

namespace TPE\Prisons;

use pocketmine\utils\UUID;
use pocketmine\Player;
use pocketmine\Server;

class PrisonPlayer {
    
  /** @var UUID **/
  private $uuid;
  /** @var string **/
  private $username;
  /** @var string **/
  private $prisonrank;
  /** @var int **/
  private $prestige;
  
  public function __construct(UUID $uuid, string $username, string $prisonrank, int $prestige) {
      $this->uuid = $uuid;
      $this->username = $username;
      $this->prisonrank = $prisonrank;
      $this->prestige = $prestige;
  }
  
  public function getUuid() : Uuid {
      return $this->uuid;
  }
  
  public function getUsername() : string {
      return $this->username;
  }
  
  public function setUsername(string $username) : void {
      $this->username = $username;
      $this->updateDb();
  }
  
  public function getPrisonRank() : string {
      return $this->prisonrank;
  }
  
  public function setPrisonRank(string $prisonrank) : void {
      $this->prisonrank = $prisonrank;
      $this->updateDb();
  }
  
  public function getPrestige() : int {
      return $this->prestige;
  }
  
  public function setPrestige(int $prestige) : void {
      $this->prestige = $prestige;
      $this->updateDb();
  }
  
  public function updateDb() : void {
      Prisons::get()->getDataBase()->executeChange("prisons.update", [
          "uuid" => $this->uuid->toString(),
          "username" => $this->username,
          "prisonrank" => $this->prisonrank,
          "prestige" => $this->prestige
      ]);
  }
}
