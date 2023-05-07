CREATE TABLE `bic__acc_rstr`
(
    `Account`      VARCHAR(20)                      NOT NULL COMMENT 'Номер счёта' PRIMARY KEY,
    `AccRstr`      VARCHAR(4)                       NOT NULL COMMENT 'Код ограничения операций по счёту',
    `AccRstrDate`  DATE DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'Дата начала действия Ограничения операций по счёту',
    `DateOut`      DATE                             NULL COMMENT 'Дата конца действия Ограничения операций по счёту',
    `SuccessorBIC` INT(9) UNSIGNED ZEROFILL         NULL COMMENT 'БИК преемника',
    CONSTRAINT `acc_to_acc` FOREIGN KEY (`Account`) REFERENCES `bic__accounts` (`Account`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `acc_to_cbr` FOREIGN KEY (`SuccessorBIC`) REFERENCES `bic__list` (`BIC`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `acc_to_rstr` FOREIGN KEY (`AccRstr`) REFERENCES `bic__rstr` (`Rstr`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Список ограничений наложенных на счета' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `AccRstrDate` ON `bic__acc_rstr` (`AccRstrDate` DESC);
