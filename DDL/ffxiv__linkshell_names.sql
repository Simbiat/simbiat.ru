CREATE TABLE `ffxiv__linkshell_names`
(
    `linkshellid` VARCHAR(40) NOT NULL COMMENT 'Linkshell ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/linkshell/linkshellid/ or https://eu.finalfantasyxiv.com/lodestone/crossworld_linkshell/linkshellid/)',
    `name`        VARCHAR(50) NOT NULL COMMENT 'Previous Linkshell name',
    PRIMARY KEY (`linkshellid`, `name`),
    CONSTRAINT `ls_names_id` FOREIGN KEY (`linkshellid`) REFERENCES `ffxiv__linkshell` (`linkshellid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Past names of linkshells' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
