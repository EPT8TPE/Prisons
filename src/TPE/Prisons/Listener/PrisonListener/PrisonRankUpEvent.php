<?php

declare(strict_types=1);

namespace TPE\Prisons\Listener\PrisonListener;

use pocketmine\event\Cancellable;
use pocketmine\player\Player;
use TPE\Prisons\Prisons;
use _64FF00\PurePerms\PurePerms;
use pocketmine\event\CancellableTrait;

final class PrisonRankUpEvent extends PrisonPlayerEvent implements Cancellable {
    use CancellableTrait;

    /** @var string */
    protected $newRank;

    /** @var string */
    protected $currentRank;

    /** @var string[] */
    protected $commands = [];

    /** @var string[] */
    protected $addedPerms = [];

    /** @var string[] */
    protected $removedPerms = [];

    public function __construct(Player $player, string $newRank, string $currentRank, array $commands, array $addedPerms, array $removedPerms) {
        $this->player = $player;
        $this->newRank = $newRank;
        $this->currentRank = $currentRank;
        $this->commands = $commands;
        $this->addedPerms = $addedPerms;
        $this->removedPerms = $removedPerms;
    }

    public function getNewRank() : string {
        return $this->newRank;
    }

    public function getCurrentRank() : string {
        return $this->currentRank;
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
