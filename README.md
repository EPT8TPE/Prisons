# Prisons
A pocketmine plugin implementing the legacy prison rank up system in addition to a form of prestiging.

# How to install
1. Download the phar from [here](https://poggit.pmmp.io/ci/TPEimperialPE/Prisons/Prisons/dev:31).
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

# Dependancies

- [EconomyAPI](https://poggit.pmmp.io/p/EconomyAPI/) is required in order for this plugin to work, if it is not installed,
the plugin will not work.

- [PurePerms](https://poggit.pmmp.io/p/PurePerms) is also required in order for this plugin to work, if it not installed,  
the plugin will not work.

- [PureChat](https://poggit.pmmp.io/p/PureChat/1.4.11) is required only if you wish to implement the 'chat format' feature 
that comes with this plugin.

# Support

If you require any help or assistance, are recieving any errors or have an idea/suggestion, message me on discord TPE#1061.


