CREATE TABLE `ffxiv__guardian`
(
    `guardianid` TINYINT(2) UNSIGNED AUTO_INCREMENT COMMENT 'Guardian ID as registered by the tracker' PRIMARY KEY,
    `guardian`   VARCHAR(25) NOT NULL COMMENT 'Guardian name',
    CONSTRAINT `guardian` UNIQUE (`guardian`)
) COMMENT 'Guardians as per lore';
