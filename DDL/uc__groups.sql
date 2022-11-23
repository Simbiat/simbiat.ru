CREATE TABLE `uc__groups`
(
    `groupid`   INT UNSIGNED AUTO_INCREMENT COMMENT 'ID of the group' PRIMARY KEY,
    `groupname` VARCHAR(25) NOT NULL COMMENT 'Human-ready group name',
    CONSTRAINT `groupname` UNIQUE (`groupname`)
);
