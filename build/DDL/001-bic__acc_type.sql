USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `bic__acc_type` (
  `RegulationAccountType` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Тип счета в соответствии с нормативом',
  `Description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Описание типа',
  PRIMARY KEY (`RegulationAccountType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Список типов счетов' `PAGE_COMPRESSED`='ON';