<?php

declare(strict_types=1);

namespace TPE\Prisons\Data;

interface BaseDB {

    public const PRISONS_REGISTER_PLAYER = "prisons.player.register";
    public const GET_PLAYER = "prisons.get.player";
    public const INIT_PLAYERS = "prisons.initplayer";
    public const UPDATE_PRESTIGE = "prisons.player.update.prestige";
    public const UPDATE_RANK = "prisons.player.update.prisonrank";
    public const GET_PRISON_RANK = "prisons.get.player.prisonrank";
    public const GET_PRISON_PRESTIGE = "prisons.get.player.prestige";
    public const INIT_TABLES = "prisons.init";
}