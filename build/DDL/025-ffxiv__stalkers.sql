USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__stalkers` (
  `character_id` int(10) unsigned NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
  `echo_takedown` datetime(6) DEFAULT NULL COMMENT 'Date when echo-takedown was found in bio',
  `echo_takedown_bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Bio text snapshot where echo-takedown was found',
  `echo_claim` datetime(6) DEFAULT NULL COMMENT 'Date when echo-claim was found in bio',
  `echo_claim_bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Bio text snapshot where echo-claim was found',
  `echo_verify` datetime(6) DEFAULT NULL COMMENT 'Date when echo-verify was found in bio',
  `echo_verify_bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Bio text snapshot where echo-verify was found',
  `echo_confirmed` datetime(6) DEFAULT NULL COMMENT 'Date when it was confirmed that the character was verified by EchoVault',
  `echo_confirmed_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Evidence of character being verified by EchoVault',
  PRIMARY KEY (`character_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Characters suspected in stalking' `PAGE_COMPRESSED`='ON';