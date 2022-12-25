CREATE TABLE `uc__user_to_group`
(
    `userid`  INT UNSIGNED DEFAULT 1 NOT NULL COMMENT 'User ID',
    `groupid` INT UNSIGNED DEFAULT 2 NOT NULL COMMENT 'Group ID',
    PRIMARY KEY (`userid`, `groupid`),
    CONSTRAINT `group_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `groupid` FOREIGN KEY (`groupid`) REFERENCES `uc__groups` (`groupid`) ON UPDATE CASCADE ON DELETE CASCADE
);
