-- #!sqlite
-- #{ prisons
-- #  { init
CREATE TABLE IF NOT EXISTS prisons_players (
    username VARCHAR(32) NOT NULL,
    prisonrank VARCHAR(1) NOT NULL,
    prestige INT UNSIGNED NOT NULL,
    PRIMARY KEY(username),
    UNIQUE(username)
);
-- #  }

-- #  { initplayer
-- #    :username string
SELECT
  prisonrank,
  prestige
FROM prisons_players WHERE username=:username;
-- #  }

-- #  { player
-- #    { register
-- #      :username string
INSERT OR REPLACE INTO prisons_players(
  username,
  prisonrank,
  prestige
) VALUES (
  :username,
  'a',
  0
);
-- #    }

-- #    { update
-- #      { prestige
-- #        :username string
-- #        :prestige int
UPDATE prisons_players SET
  prestige=:prestige
WHERE username=:username;
-- #      }

-- #      { prisonrank
-- #        :username string
-- #        :prisonrank string
UPDATE prisons_players SET
  prisonrank=:prisonrank
WHERE username=:username;
-- #      }
-- #    }
-- #  }

-- #  { get
-- #    { player
-- #      { prestige
-- #        :username string
SELECT prestige FROM prisons_players WHERE username=:username;
-- #      }

-- #      { prisonrank
-- #        :username string
SELECT prisonrank FROM prisons_players WHERE username=:username;
-- #      }
-- #    }
-- #  }

-- #}