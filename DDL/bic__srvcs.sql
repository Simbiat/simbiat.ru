CREATE TABLE `bic__srvcs` (
  `Srvcs` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Код сервиса',
  `Description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Описание сервиса',
  PRIMARY KEY (`Srvcs`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Коды сервисов доступных участникам обмена' `PAGE_COMPRESSED`='ON';