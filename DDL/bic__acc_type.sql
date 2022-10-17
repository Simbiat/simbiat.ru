CREATE TABLE `bic__acc_type`
(
    `RegulationAccountType` VARCHAR(4)   NOT NULL COMMENT 'Тип счета в соответствии с нормативом' PRIMARY KEY,
    `Description`           VARCHAR(100) NOT NULL COMMENT 'Описание типа'
) COMMENT 'Список типов счетов';
