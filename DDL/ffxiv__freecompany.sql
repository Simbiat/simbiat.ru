CREATE TABLE `ffxiv__freecompany`
(
    `freecompanyid`  VARCHAR(20)                                     NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)' PRIMARY KEY,
    `name`           VARCHAR(50)                                     NOT NULL COMMENT 'Free Company name',
    `manual`         TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag indicating whether entity was added manually',
    `serverid`       TINYINT(2) UNSIGNED                             NULL COMMENT 'ID of the server Free Company resides on',
    `grandcompanyid` TINYINT(2) UNSIGNED                             NULL COMMENT 'ID of Grand Company affiliated with the Free Company',
    `tag`            VARCHAR(10)                                     NULL COMMENT 'Short name of Free Company',
    `formed`         DATE                DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'Free Company formation day as seen on Lodestone',
    `registered`     DATE                DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When Free Company was initially added to tracker',
    `updated`        TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'When Free Company was last updated on the tracker',
    `deleted`        DATE                                            NULL COMMENT 'Date when Free Company was marked as deleted',
    `crest`          CHAR(64)                                        NULL COMMENT 'Name (hash) of image representing merged crest for the company (generated on each company update from 1 to 3 images on Lodestone)',
    `crest_part_1`   VARCHAR(100)                                    NULL COMMENT 'Link to 1st part of the crest (background)',
    `crest_part_2`   VARCHAR(100)                                    NULL COMMENT 'Link to 2nd part of the crest (frame)',
    `crest_part_3`   VARCHAR(100)                                    NULL COMMENT 'Link to 3rd part of the crest (emblem)',
    `rank`           TINYINT(2) UNSIGNED DEFAULT 1                   NOT NULL COMMENT 'Company level',
    `slogan`         TEXT                                            NULL COMMENT 'Public message shown on company board as seen on Lodestone',
    `activeid`       TINYINT(1) UNSIGNED                             NULL COMMENT 'ID of active time as registered on tracker',
    `recruitment`    TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company is recruiting or not',
    `communityid`    VARCHAR(40)                                     NULL COMMENT 'Community ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/community_finder/communityid/)',
    `estate_zone`    TEXT                                            NULL COMMENT 'Name of estate',
    `estateid`       SMALLINT UNSIGNED                               NULL COMMENT 'Estate ID as registered by the tracker',
    `estate_message` TEXT                                            NULL COMMENT 'Greeting on estate board as shown on Lodestone',
    `Role-playing`   TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company participates in role-playing',
    `Leveling`       TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company participates in leveling',
    `Casual`         TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company participates in casual activities',
    `Hardcore`       TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company participates in hardcore activities',
    `Dungeons`       TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company participates in dungeons',
    `Guildhests`     TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company participates in guildhests',
    `Trials`         TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company participates in trials',
    `Raids`          TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company participates in raids',
    `PvP`            TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company participates in PvP',
    `Tank`           TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company is looking for tanks',
    `Healer`         TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company is looking for healers',
    `DPS`            TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company is looking for DPSs',
    `Crafter`        TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company is looking for crafters',
    `Gatherer`       TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether company is looking for gatherers',
    CONSTRAINT `activeid2` FOREIGN KEY (`activeid`) REFERENCES `ffxiv__timeactive` (`activeid`) ON UPDATE SET NULL ON DELETE SET NULL,
    CONSTRAINT `estateid` FOREIGN KEY (`estateid`) REFERENCES `ffxiv__estate` (`estateid`) ON UPDATE SET NULL ON DELETE SET NULL,
    CONSTRAINT `grandcompanyid` FOREIGN KEY (`grandcompanyid`) REFERENCES `ffxiv__grandcompany` (`gcId`) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `serverid_fc` FOREIGN KEY (`serverid`) REFERENCES `ffxiv__server` (`serverid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Free Companies found on Lodestone' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `activeid` ON `ffxiv__freecompany` (`activeid`);

CREATE INDEX `communityid` ON `ffxiv__freecompany` (`communityid`);

CREATE INDEX `deleted` ON `ffxiv__freecompany` (`deleted`);

CREATE FULLTEXT INDEX `estate_message` ON `ffxiv__freecompany` (`estate_message`);

CREATE FULLTEXT INDEX `estate_zone` ON `ffxiv__freecompany` (`estate_zone`);

CREATE FULLTEXT INDEX `name` ON `ffxiv__freecompany` (`name`);

CREATE INDEX `name_order` ON `ffxiv__freecompany` (`name`);

CREATE INDEX `registered` ON `ffxiv__freecompany` (`registered`);

CREATE FULLTEXT INDEX `slogan` ON `ffxiv__freecompany` (`slogan`);

CREATE FULLTEXT INDEX `tag` ON `ffxiv__freecompany` (`tag`);

CREATE INDEX `updated` ON `ffxiv__freecompany` (`updated` DESC);
