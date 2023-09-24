CREATE TABLE `uc__groups`
(
    `groupid`     INT UNSIGNED AUTO_INCREMENT COMMENT 'ID of the group' PRIMARY KEY,
    `groupname`   VARCHAR(25) NOT NULL COMMENT 'Human-ready group name',
    `description` TEXT        NOT NULL COMMENT 'Description of the group',
    CONSTRAINT `groupname` UNIQUE (`groupname`)
) ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
