USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `bic__srvcs` (
  `Srvcs` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Код сервиса',
  `Description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Описание сервиса',
  PRIMARY KEY (`Srvcs`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Коды сервисов доступных участникам обмена' `PAGE_COMPRESSED`='ON';