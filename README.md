<a href="https://poggit.pmmp.io/p/Prisons"><img src="https://poggit.pmmp.io/shield.dl.total/Prisons"></a> <a href="https://poggit.pmmp.io/p/Prisons"><img src="https://poggit.pmmp.io/shield.state/Prisons"></a>

# Prisons
A pocketmine plugin implementing the legacy prison rank up system in addition to a form of prestiging.

# How to install
1. Download the phar [here](https://poggit.pmmp.io/p/Prisons).
2. Add it to your servers 'plugins' directory.
3. Restart your server.

# Features

- Players have the ability to rankup for a specific price configurable in the config.

- Player can 'ascend' which is essentially prestiging, this resets their prison rank to A and resets their money, the rank up prices are
then multiplied by an amount specified in the config.

- You have the option to configure permissions that are removed and given during rank up and ascension.

- All messages are configurable within the config!

- Rank names can be altered within the config.

- You have the option for a chat format which shows the players prison rank and ascension level.

- A scorehud addon.

- Only allow players to mine in mines created by the mine reset plugin (unless you have a specific permission or are in a specified plot world).

# Commands

- /rankup - If the player has a sufficient amount of they are ranked up to the next prison rank.

- /ascend - If the player is at the prison rank 'z' and has a sufficient amount of money then they ascend, resetting 
their money to 0, teleporting them to a spawn location specified within the config and resetting their prison rank to 
'a'.

# Permissions
prisons.*:

    default: op
    description: Allows the usage of all prison commands.

prisons.rankup:
    
    default: true
    description: Allows the usage of the /rankup command.

prisons.ascend:
    
    default: true
    description: Allows the usage of the /ascend command.

prisons.nomine.bypass:

    default: op
    description: Allows players to mine/place blocks in areas that are not mine areas.

# Dependancies

- [EconomyAPI](https://poggit.pmmp.io/p/EconomyAPI/) is required in order for this plugin to work, if it is not installed,
the plugin will not work.

- [PurePerms](https://poggit.pmmp.io/p/PurePerms) is also required in order for this plugin to work, if it not installed,  
the plugin will not work.

- [MineReset](https://poggit.pmmp.io/p/MineReset) is also required in order for this plugin to work, if not installed the 
plugin will not work.

- Optional |
           v 

- [PureChat](https://poggit.pmmp.io/p/PureChat/1.4.11) is required only if you wish to implement the 'chat format' feature 
that comes with this plugin.

- [ScoreHud])(https://poggit.pmmp.io/p/ScoreHud/5.2.0) is required only if you wish to use prison's scorehud feature.

# Chat Format & ScoreHud

- Use {PRISON_RANK} and {PRISON_ASCENSION} in order to display the rank/ascension level of player in chat.

- You must download the scorehud addon from [here](https://github.com/TPEimperialPE/Prisons/releases/tag/Main), download
the 'PrisonAddon.php' file and put it into your addon's folder and restart your server. 

- Use {PRISON_RANK} and {PRISON_ASCENSION} in order to display the rank/ascension level of a player on the scorehud.

# API

The following functions can be used by developers in the following way, what the functions do is fairly self explanatory:

    Prisons::get()->getRank(string $name);
    Prisons::get()->getAscension(string $name);
    Prisons::get()->setRank(string $name, string $rank);
    Prisons::get()->setAscension(string $name, int $ascensionLevel);

# Support

If you require any help or assistance, are recieving any errors or have an idea/suggestion, message me on discord TPE#1061.


