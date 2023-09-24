CREATE TABLE `ffxiv__nameday`
(
    `namedayid` SMALLINT(3) UNSIGNED AUTO_INCREMENT COMMENT 'Nameday ID as registered by the tracker' PRIMARY KEY,
    `nameday`   VARCHAR(32) NOT NULL COMMENT 'Nameday',
    CONSTRAINT `nameday` UNIQUE (`nameday`)
) COMMENT 'Namedays as per lore' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
