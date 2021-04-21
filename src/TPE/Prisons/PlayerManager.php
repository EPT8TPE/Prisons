<?php

declare(strict_types = 1);

namespace TPE\Prisons;

class PlayerManager {
  
  /** @var self **/
  private static $instance;
  
  private $players = [];
  
  public function __construct() {
      self::$instance = $this;
  }
  
  public function createPlayer(Player $player) {
        
  }  
}
