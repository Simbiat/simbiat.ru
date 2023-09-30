CREATE TABLE `sys__logs`
(
    `time`      DATETIME(6) DEFAULT CURRENT_TIMESTAMP(6)         NOT NULL COMMENT 'When action occurred',
    `type`      TINYINT(2) UNSIGNED                              NOT NULL COMMENT 'Type of the action',
    `action`    VARCHAR(255) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Short description of the action',
    `userid`    INT UNSIGNED                                     NOT NULL COMMENT 'Optional user ID, if available',
    `ip`        VARCHAR(45) COLLATE utf8mb4_uca1400_nopad_as_ci  NULL COMMENT 'IP, if available',
    `useragent` TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci         NULL COMMENT 'Full useragent, if available',
    `extra`     TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci         NULL COMMENT 'Any extra information available for the entry',
    CONSTRAINT `audit_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `log_type` FOREIGN KEY (`type`) REFERENCES `sys__log_types` (`typeid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Table storing logs' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `audit_userid` ON `sys__logs` (`userid`);

CREATE INDEX `time` ON `sys__logs` (`time` DESC);
