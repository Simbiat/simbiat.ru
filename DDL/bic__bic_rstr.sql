CREATE TABLE `bic__bic_rstr`
(
    `BIC`      INT(9) UNSIGNED ZEROFILL         NOT NULL COMMENT 'БИК участника',
    `Rstr`     VARCHAR(4)                       NOT NULL COMMENT 'Код ограничения, наложенного на участника',
    `RstrDate` DATE DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'Дата начала действия ограничения участника',
    `DateOut`  DATE                             NULL COMMENT 'Дата окончания действия ограничения участника',
    PRIMARY KEY (`BIC`, `Rstr`, `RstrDate`),
    CONSTRAINT `rstr_to_bic` FOREIGN KEY (`BIC`) REFERENCES `bic__list` (`BIC`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `rstr_to_rstr` FOREIGN KEY (`Rstr`) REFERENCES `bic__rstr` (`Rstr`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Список ограничений наложенных на участника';

CREATE INDEX `RstrDate` ON `bic__bic_rstr` (`RstrDate` DESC);
