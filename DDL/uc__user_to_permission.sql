CREATE TABLE `uc__user_to_permission`
(
    `userid`     INT UNSIGNED DEFAULT 1 NOT NULL COMMENT 'User ID',
    `permission` VARCHAR(25)            NOT NULL COMMENT 'Short name of permission, used as an ID',
    PRIMARY KEY (`userid`, `permission`),
    CONSTRAINT `permission_to_permission_user` FOREIGN KEY (`permission`) REFERENCES `uc__permissions` (`permission`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `permission_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Permissions assigned to users directly';
