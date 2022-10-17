CREATE TABLE `ffxiv__linkshell`
(
    `linkshellid` VARCHAR(40)                                     NOT NULL COMMENT 'Linkshell ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/linkshell/linkshellid/ or https://eu.finalfantasyxiv.com/lodestone/crossworld_linkshell/linkshellid/)' PRIMARY KEY,
    `name`        VARCHAR(50)                                     NOT NULL COMMENT 'Linkshell name',
    `manual`      TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag indicating whether entity was added manually',
    `serverid`    TINYINT(2) UNSIGNED                             NULL COMMENT 'ID of the server Linkshell resides on',
    `crossworld`  TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Flag indicating whether linkshell is crossworld',
    `formed`      DATE                                            NULL COMMENT 'Linkshell formation day as seen on Lodestone',
    `registered`  DATE                DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When Linkshsell was initially added to tracker',
    `updated`     TIMESTAMP           DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'When Linkshsell was last updated on the tracker',
    `deleted`     DATE                                            NULL COMMENT 'Date when Linkshell was marked as deleted',
    `communityid` VARCHAR(40)                                     NULL COMMENT 'Community ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/community_finder/communityid/)',
    CONSTRAINT `serverid_ls` FOREIGN KEY (`serverid`) REFERENCES `ffxiv__server` (`serverid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Linkshells (both crossworld and not) found on Lodestone';

CREATE INDEX `communityid` ON `ffxiv__linkshell` (`communityid`);

CREATE INDEX `crossworld` ON `ffxiv__linkshell` (`crossworld`);

CREATE INDEX `deleted` ON `ffxiv__linkshell` (`deleted`);

CREATE FULLTEXT INDEX `name` ON `ffxiv__linkshell` (`name`);

CREATE INDEX `name_order` ON `ffxiv__linkshell` (`name`);

CREATE INDEX `registered` ON `ffxiv__linkshell` (`registered`);

CREATE INDEX `updated` ON `ffxiv__linkshell` (`updated` DESC);
