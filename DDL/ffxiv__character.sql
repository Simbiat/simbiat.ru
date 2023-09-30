CREATE TABLE `ffxiv__character`
(
    `characterid`   INT UNSIGNED                                      NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)' PRIMARY KEY,
    `userid`        INT UNSIGNED                                      NULL COMMENT 'ID of the user, that is linked to the character',
    `serverid`      TINYINT(2) UNSIGNED                               NULL COMMENT 'ID of the server character resides on',
    `name`          VARCHAR(50) COLLATE utf8mb4_uca1400_nopad_ai_ci   NOT NULL COMMENT 'Character''s name',
    `manual`        TINYINT(1) UNSIGNED  DEFAULT 0                    NOT NULL COMMENT 'Flag indicating whether entity was added manually',
    `avatar`        VARCHAR(66) COLLATE utf8mb4_uca1400_nopad_as_ci   NOT NULL COMMENT 'ID portion of the link to character avatar (requires adding of ''l0_640x873.jpg'' or ''c0_96x96.jpg'' to the end of the field and ''https://img2.finalfantasyxiv.com/f/'' to the beginning to be turned into actual image link)',
    `registered`    DATE                 DEFAULT CURRENT_TIMESTAMP()  NOT NULL COMMENT 'When character was initially added to tracker',
    `updated`       DATETIME(6)          DEFAULT CURRENT_TIMESTAMP(6) NOT NULL ON UPDATE CURRENT_TIMESTAMP(6) COMMENT 'When character was last updated on the tracker',
    `deleted`       DATE                                              NULL COMMENT 'Date when character was marked as deleted',
    `enemyid`       INT UNSIGNED                                      NULL COMMENT 'ID of an enemy that "killed" the character',
    `biography`     TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci          NULL COMMENT 'Text from "Character profile" section on Lodestone',
    `titleid`       SMALLINT UNSIGNED                                 NULL COMMENT 'ID of achievement title currently being used by character',
    `clanid`        TINYINT(2) UNSIGNED                               NULL COMMENT 'Clan ID identifying both clan and race of the character',
    `genderid`      TINYINT(1) UNSIGNED  DEFAULT 1                    NOT NULL COMMENT '0 for female and 1 for male',
    `namedayid`     SMALLINT(3) UNSIGNED DEFAULT 1                    NOT NULL COMMENT 'ID of nameday (birthday) of character',
    `guardianid`    TINYINT(2) UNSIGNED  DEFAULT 4                    NOT NULL COMMENT 'ID of Guardian chosen by character',
    `cityid`        TINYINT(1) UNSIGNED  DEFAULT 5                    NOT NULL COMMENT 'ID of character''s starting city',
    `gcrankid`      TINYINT(2) UNSIGNED                               NULL COMMENT 'ID of character''s Grand Company''s affiliation and current rank there',
    `pvp_matches`   INT(10)              DEFAULT 0                    NULL COMMENT 'Number of PvP matches character participated in',
    `Alchemist`     TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Alchemist job',
    `Armorer`       TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Armorer job',
    `Astrologian`   TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Astrologian job',
    `Bard`          TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Bard job',
    `BlackMage`     TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Black Mage job',
    `Blacksmith`    TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Blacksmith job',
    `BlueMage`      TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Blue Mage job',
    `Botanist`      TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Botanist job',
    `Carpenter`     TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Carpenter job',
    `Culinarian`    TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Culinarian job',
    `Dancer`        TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Dancer job',
    `DarkKnight`    TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Dark Knight job',
    `Dragoon`       TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Dragoon job',
    `Fisher`        TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Fisher job',
    `Goldsmith`     TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Goldsmith job',
    `Gunbreaker`    TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Gunbreaker job',
    `Leatherworker` TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Leatherworker job',
    `Machinist`     TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Machinist job',
    `Miner`         TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Miner job',
    `Monk`          TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Monk job',
    `Ninja`         TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Ninja job',
    `Paladin`       TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Paladin job',
    `Reaper`        TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Reaper job',
    `RedMage`       TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Red Mage job',
    `Sage`          TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Sage job',
    `Samurai`       TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Samurai job',
    `Scholar`       TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Scholar job',
    `Summoner`      TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Summoner job',
    `Warrior`       TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Warrior job',
    `Weaver`        TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of Weaver job',
    `WhiteMage`     TINYINT UNSIGNED     DEFAULT 0                    NOT NULL COMMENT 'Level of White Mage job',
    CONSTRAINT `cityid` FOREIGN KEY (`cityid`) REFERENCES `ffxiv__city` (`cityid`) ON UPDATE CASCADE,
    CONSTRAINT `clanid` FOREIGN KEY (`clanid`) REFERENCES `ffxiv__clan` (`clanid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `enemyid` FOREIGN KEY (`enemyid`) REFERENCES `ffxiv__enemy` (`enemyid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `gcrankid` FOREIGN KEY (`gcrankid`) REFERENCES `ffxiv__grandcompany_rank` (`gcrankid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `guardianid` FOREIGN KEY (`guardianid`) REFERENCES `ffxiv__guardian` (`guardianid`) ON UPDATE CASCADE,
    CONSTRAINT `namedayid` FOREIGN KEY (`namedayid`) REFERENCES `ffxiv__nameday` (`namedayid`) ON UPDATE CASCADE,
    CONSTRAINT `serverid` FOREIGN KEY (`serverid`) REFERENCES `ffxiv__server` (`serverid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `titleid` FOREIGN KEY (`titleid`) REFERENCES `ffxiv__achievement` (`achievementid`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `userid` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE SET NULL
) COMMENT 'Characters found on Lodestone' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE FULLTEXT INDEX `biography` ON `ffxiv__character` (`biography`);

CREATE INDEX `deleted` ON `ffxiv__character` (`deleted`);

CREATE FULLTEXT INDEX `name` ON `ffxiv__character` (`name`);

CREATE INDEX `name_order` ON `ffxiv__character` (`name`);

CREATE INDEX `registered` ON `ffxiv__character` (`registered`);

CREATE INDEX `updated` ON `ffxiv__character` (`updated` DESC);
