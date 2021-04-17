<?php

declare(strict_types=1);

namespace TPE\Prisons\Listener\PrisonListener;

use CortexPE\Hierarchy\Hierarchy;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use TPE\Prisons\Prisons;

final class PrisonPrestigeEvent extends PrisonPlayerEvent {

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

        Prisons::get()->setPrisonPrestige($this->getPlayer(), $this->getNewPrestige());

        foreach ($this->getCommands() as $command) {
            Prisons::get()->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{PLAYER}", $this->getPlayer()->getName(), $command));
        }

        $manager = Prisons::get()->getPermissionManager();

        if($manager === "hierarchy") {
            $manager = Prisons::get()->getServer()->getPluginManager()->getPlugin("Hierarchy");
            if($manager instanceof Hierarchy) {
                foreach ($this->getAddedPermissions() as $permission) {
                    $manager->getMemberFactory()->getMember($this->getPlayer())->addMemberPermission($permission);
                }

                foreach ($this->getRemovedPermissions() as $permission) {
                    $manager->getMemberFactory()->getMember($this->getPlayer())->removeMemberPermission($permission);
                }
            }
        }

        if($manager === "pureperms") {
            $manager = Prisons::get()->getServer()->getPluginManager()->getPlugin("PurePerms");
            if($manager instanceof PurePerms) {
                foreach ($this->getAddedPermissions() as $permission) {
                    $manager->getUserDataMgr()->setPermission($this->getPlayer(), $permission);
                }

                foreach ($this->getRemovedPermissions() as $permission) {
                    $manager->getUserDataMgr()->unsetPermission($this->getPlayer(), $permission);
                }
            }
        }
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