USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__permissions` (
  `permission` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Short name of permission, used as an ID',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Description of the permission',
  PRIMARY KEY (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='List of permissions' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;