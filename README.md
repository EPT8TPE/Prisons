<a href="https://poggit.pmmp.io/p/Prisons"><img src="https://poggit.pmmp.io/shield.dl.total/Prisons"></a> <a href="https://poggit.pmmp.io/p/Prisons"><img src="https://poggit.pmmp.io/shield.state/Prisons"></a>

# Prisons
A pocketmine plugin implementing the legacy prison rank up system in addition to a form of prestiging.

# How to install
1. Download the phar [here](https://poggit.pmmp.io/p/Prisons).
2. Add it to your servers 'plugins' directory.
3. Restart your server.

# Features
- Custom events for prestiging and ranking up.
- Add permissions, remove permissions and run commands when ranking up and prestiging via the config.
- Chat formatting via purechat.
- A scorehud addon for the new version of scorehud.
- Written in libasyncsql in order to improve performance.
- Compatible with both sqlite3 and mysql.

# Commands 
```- /prestige - Reset your rank and multiply your rank up price by a specified amount.
- /rankup - Rank up to the next rank.
```

# Permissions
```prisons.*:
    default: op
    description: Allows access to all prison commands.
    children:
      prisons.rankup:
        default: true
        description: Allows user to use /rankup.
      prisons.prestige:
        default: true
        description: Allows players to use /prestige.
      prisons.nomine.bypass:
        default: op
        description: Allows players to mine/place blocks in areas that are not mine areas.  
```

# Addons/Dependencies 
- [Pureperms](https://poggit.pmmp.io/p/PurePerms) and [Minereset](https://poggit.pmmp.io/p/MineReset/3.2.0) are required for this plugin to operate.
- [Scorehud](https://poggit.pmmp.io/p/ScoreHud) and [PrisonScore](https://poggit.pmmp.io/ci/TPEimperialPE/PrisonScorehud/PrisonScorehud/dev:3) and required if you wish to 
implement scorehud tags.
- [Purechat](https://poggit.pmmp.io/p/PureChat/1.4.11) is required if you wish to implement a players rank and prestige into your chat format, use {PRISON_RANK} for the players prison rank and {PRISON_PRESTIGE} for their prestige.

# API
- Version 2.0 of prisons has been written in a [libasyncsql](https://github.com/poggit/libasynql) in order to reduce lag and align with poggit standards
hopefully reducing stress on the main thread.
- Prisons now runs using custom player classes of some sort and also an event based system.

Here's an example of me getting and setting the prestige and rank of a player.
```php 
<?php

declare(strict_types = 1);

namespace TPE\Test;

use TPE\Prisons\Prisons;
use pocketmine\Player;
use TPE\Prisons\PrisonPlayer;

class TestPlugin extends PluginBase {

    public function onEnable() {
        // enabled
    }
    
    public function getPrisonPlayer(Player $player) : ?PrisonPlayer {
        return Prisons::get()->getPlayerManager()->getPlayer($player) ?? null;
    }
    
    public function getPrisonRank(PrisonPlayer $player) : ?string {
        return $player->getPrisonRank() ?? null;
        // returns the prison rank of a player e.g 'a'.
    }
    
    public function setPrisonRank(PrisonPlayer $player, string $rank) : void {
        $player->setPrisonRank($rank);
        // sets the prison rank to whatever '$rank' is 
    }

    public function getPrestige(PrisonPlayer $player) : ?int {
        return $player->getPrestige() ?? null;
        // returns the prestige of a player, by default this is 0.
    }
    
    public function setPrestige(PrisonPlayer $player, int $prestige) : void {
        $player->setPrestige($prestige);
        // sets a players prestige to whatever '$prestige' is.
    }
}
```
Keep in mind that none of those functions are implemented into this plugin, you create them yourself.

- In addition to that we have created some none cancellable events you can listen for when players rank up and prestige.
```php
<?php

declare(strict_types = 1);

namespace TPE\Test;

use pocketmine\event\Listener;
use TPE\Prisons\Listener\PrisonListener\PrisonRankUpEvent;
use TPE\Prisons\Listener\PrisonListener\PrisonPrestigeEvent;

class EventListener implements Listener {

    public function onRankUp(PrisonRankUpEvent $event) : void {
        $player = $event->getPlayer(); // returns a 'Player' instance, not a 'PrisonPlayer' instance.
        $currentRank = $event->getCurrentRank(); // returns a string representing the current rank of a player.
        $newRank = $event->getNewRank(); // returns a string representing what the players rank will be set to when the event executes.
        $addedperms = $event->getAddedPermissions(); // returns a string array representing all added permissions for the specific rankup.
        $removedperms = $event->getRemovedPermissions(); // returns a string array representing all removed permissions for the specific rankup.
        $commands = $event->getCommands(); // returns a string array of all commands ran during rankup.
        
        $event->setAddedPermissions(["prisons.*"]); // sets the added permissions for that specific players rankup to 'prisons.*', you can specify multiple.
        $event->setRemovedPermissions(["prisons.prestige"]); // sets the removed permissions for that specific players rankup to 'prisons.prestige'.
        $event->setCommands(["say {PLAYER} has ranked up to {$newRank!"}]); // within all commands use {PLAYER} to represent the player in the event.
    }
    
    public function onPrestige(PrisonPrestigeEvent $event) {
        $player = $event->getPlayer(); // returns a 'Player' instance, not a 'PrisonPlayer' instance.
        $currentPrestige = $event->getCurrentPrestige; // returns an int representing the current prestige of a player.
        $newPrestige = $event->getNewPrestige(); // returns an int representing what the players prestige will be set to when the event executes.
        $addedperms = $event->getAddedPermissions(); // returns a string array representing all added permissions for the specific prestige.
        $removedperms = $event->getRemovedPermissions(); // returns a string array representing all removed permissions for the specific prestige.
        $commands = $event->getCommands(); // returns a string array of all commands ran during prestige.
        
        $event->setAddedPermissions(["prisons.*"]); // sets the added permissions for that specific players prestige to 'prisons.*', you can specify multiple.
        $event->setRemovedPermissions(["prisons.prestige"]); // sets the removed permissions for that specific players prestige to 'prisons.prestige'.
        $event->setCommands(["say {PLAYER} has prestiged to {$newPrestige!"}]); // within all commands use {PLAYER} to represent the player in the event.
    } 

}
```

# Support
Contact me on discord ```TPE#1061``` if you have any further queries related to this plugin.
 


