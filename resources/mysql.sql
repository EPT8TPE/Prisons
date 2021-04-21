-- #!mysql
-- #{ prisons
-- #  { init
CREATE TABLE IF NOT EXISTS prisons_players (
    uuid VARCHAR(36) PRIMARY KEY,
    username VARCHAR(16),
    prisonrank STRING DEFAULT 'a',
    prestige INT DEFAULT 0
);
-- #  }

-- #  { initplayer
SELECT *
FROM prisons_players;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :username string
-- #      :prisonrank string
-- #      :prestige int
INSERT INTO prisons_players(uuid, username, prisonrank , prestige)
VALUES (:uuid, :username, :prisonrank, :prestige);
-- #    }

-- #  { update
-- #      :uuid string
-- #      :username string
-- #      :prisonrank string
-- #      :prestige int
UPDATE prisons_players 
SET username=:username,
    prisonrank=:prisonrank,
    prestige=:prestige
WHERE uuid = :uuid;
-- #  }
-- # }
