CREATE TABLE `ffxiv__enemy`
(
    `enemyid` INT UNSIGNED AUTO_INCREMENT COMMENT 'Internal ID of the enemy' PRIMARY KEY,
    `name`    VARCHAR(50) NOT NULL COMMENT 'Name of the enemy',
    CONSTRAINT `FFXIVEnemyName` UNIQUE (`name`)
) COMMENT 'List of some monsters, that are used for character ''deaths'', when they are marked as deleted'
    ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON';
