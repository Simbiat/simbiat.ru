CREATE TABLE `uc__group_to_permission`
(
    `groupid`    INT UNSIGNED                                    NOT NULL COMMENT 'ID of the group',
    `permission` VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Short name of permission, used as an ID',
    PRIMARY KEY (`groupid`, `permission`),
    CONSTRAINT `permission_to_group` FOREIGN KEY (`groupid`) REFERENCES `uc__groups` (`groupid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `permission_to_permission_group` FOREIGN KEY (`permission`) REFERENCES `uc__permissions` (`permission`)
) COMMENT 'List of permissions assigned to user groups' `PAGE_COMPRESSED` = 'ON';
