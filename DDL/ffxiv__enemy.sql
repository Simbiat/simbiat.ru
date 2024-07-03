CREATE TABLE `ffxiv__enemy` (
  `enemyid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Internal ID of the enemy',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Name of the enemy',
  PRIMARY KEY (`enemyid`),
  UNIQUE KEY `FFXIVEnemyName` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='List of some monsters, that are used for character ''deaths'', when they are marked as deleted' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;