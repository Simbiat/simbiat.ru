CREATE TABLE `ffxiv__freecompany_character` (
  `characterid` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `freecompanyid` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
  `rankid` tinyint(2) unsigned DEFAULT NULL COMMENT 'ID calculated based on rank icon on Lodestone',
  `current` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Whether character is currently in the group',
  PRIMARY KEY (`characterid`,`freecompanyid`),
  KEY `fc_xchar_fc` (`freecompanyid`),
  KEY `fc_char_rank` (`rankid`),
  CONSTRAINT `fc_char_rank` FOREIGN KEY (`rankid`) REFERENCES `ffxiv__freecompany_rank` (`rankid`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `fc_xchar_fc` FOREIGN KEY (`freecompanyid`) REFERENCES `ffxiv__freecompany` (`freecompanyid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fc_xchar_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Characters linked to Free Companies, past and present' `PAGE_COMPRESSED`='ON';