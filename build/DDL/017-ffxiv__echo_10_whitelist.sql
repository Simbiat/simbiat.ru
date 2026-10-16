USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__echo_10_whitelist` (
  `term` varchar(10) NOT NULL COMMENT 'False-positive match',
  PRIMARY KEY (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='List of terms determined to be false-positive for EchoVault' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;