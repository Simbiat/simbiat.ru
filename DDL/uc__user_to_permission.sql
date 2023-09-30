CREATE TABLE `uc__user_to_permission`
(
    `userid`     INT UNSIGNED DEFAULT 1                          NOT NULL COMMENT 'User ID',
    `permission` VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Short name of permission, used as an ID',
    PRIMARY KEY (`userid`, `permission`),
    CONSTRAINT `permission_to_permission_user` FOREIGN KEY (`permission`) REFERENCES `uc__permissions` (`permission`),
    CONSTRAINT `permission_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Permissions assigned to users directly' `PAGE_COMPRESSED` = 'ON';
