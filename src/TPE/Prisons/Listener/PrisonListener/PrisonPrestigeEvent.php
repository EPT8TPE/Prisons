<?php

declare(strict_types=1);

namespace TPE\Prisons\Listener\PrisonListener;

use pocketmine\event\Cancellable;
use _64FF00\PurePerms\PurePerms;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\player\Player;
use TPE\Prisons\Prisons;
use pocketmine\event\CancellableTrait;

final class PrisonPrestigeEvent extends PrisonPlayerEvent implements Cancellable {
    use CancellableTrait;
    
    /** @var int */
    protected $newPrestige;

    /** @var int */
    protected $currentPrestige;

    /** @var string[] */
    protected $commands = [];

    /** @var string[] */
    protected $addedPerms = [];

    /** @var string[] */
    protected $removedPerms = [];

    public function __construct(Player $player, int $newPrestige, int $currentPrestige, array $commands, array $addedPerms, array $removedPerms) {
        $this->player = $player;
        $this->newPrestige = $newPrestige;
        $this->currentPrestige = $currentPrestige;
        $this->commands = $commands;
        $this->addedPerms = $addedPerms;
        $this->removedPerms = $removedPerms;
    }

    public function getNewPrestige() : int {
        return $this->newPrestige;
    }

    public function getCurrentPrestige() : int {
        return $this->currentPrestige;
    }

    public function getCommands() : array {
        return $this->commands;
    }

    public function setCommands(array $commands) : void {
        $this->commands = $commands;
    }

    public function getAddedPermissions() : array {
        return $this->addedPerms;
    }

    public function setAddedPermissions(array $addedPerms) : void {
        $this->addedPerms = $addedPerms;
    }

    public function getRemovedPermissions() : array {
        return $this->removedPerms;
    }

    public function setRemovedPermissions(array $removedPerms) : void {
        $this->removedPerms = $removedPerms;
    }
}
