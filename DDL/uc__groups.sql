CREATE TABLE `uc__groups`
(
    `groupid`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `groupname` VARCHAR(25) NOT NULL,
    CONSTRAINT `groupname` UNIQUE (`groupname`)
);
