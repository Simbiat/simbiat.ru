USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__freecompany_character` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `fc_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
  `rank_id` tinyint(2) unsigned DEFAULT NULL COMMENT 'ID calculated based on rank icon on Lodestone',
  `current` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Whether character is currently in the group',
  PRIMARY KEY (`character_id`,`fc_id`),
  KEY `fc_xchar_fc` (`fc_id`),
  KEY `fc_char_rank` (`rank_id`),
  CONSTRAINT `fc_char_rank` FOREIGN KEY (`rank_id`) REFERENCES `ffxiv__freecompany_rank` (`rank_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `fc_xchar_fc` FOREIGN KEY (`fc_id`) REFERENCES `ffxiv__freecompany` (`fc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fc_xchar_id` FOREIGN KEY (`character_id`) REFERENCES `ffxiv__character` (`character_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Characters linked to Free Companies, past and present' `PAGE_COMPRESSED`='ON';