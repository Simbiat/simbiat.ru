CREATE TABLE `ffxiv__freecompany_names`
(
    `freecompanyid` VARCHAR(20) NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
    `name`          VARCHAR(50) NOT NULL COMMENT 'Previous name of the company',
    PRIMARY KEY (`freecompanyid`, `name`),
    CONSTRAINT `fc_names_id` FOREIGN KEY (`freecompanyid`) REFERENCES `ffxiv__freecompany` (`freecompanyid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Past names of the companies' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
