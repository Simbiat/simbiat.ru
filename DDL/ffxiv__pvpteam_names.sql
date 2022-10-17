CREATE TABLE `ffxiv__pvpteam_names`
(
    `pvpteamid` VARCHAR(40) NOT NULL COMMENT 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)',
    `name`      VARCHAR(50) NOT NULL COMMENT 'Previous PvP Team name',
    PRIMARY KEY (`pvpteamid`, `name`),
    CONSTRAINT `pvp_name_id` FOREIGN KEY (`pvpteamid`) REFERENCES `ffxiv__pvpteam` (`pvpteamid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Past names of PvP teams';
