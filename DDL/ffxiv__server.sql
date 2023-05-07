CREATE TABLE `ffxiv__server`
(
    `serverid`   TINYINT(2) UNSIGNED AUTO_INCREMENT COMMENT 'Server ID as registered by the tracker' PRIMARY KEY,
    `server`     VARCHAR(20) NOT NULL COMMENT 'Server name',
    `datacenter` VARCHAR(10) NOT NULL COMMENT 'Data center name',
    CONSTRAINT `server` UNIQUE (`server`, `datacenter`)
) COMMENT 'List of servers/data centers' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
