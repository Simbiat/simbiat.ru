CREATE TABLE `bic__acc_type` (
  `RegulationAccountType` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Тип счета в соответствии с нормативом',
  `Description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Описание типа',
  PRIMARY KEY (`RegulationAccountType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Список типов счетов' `PAGE_COMPRESSED`='ON';