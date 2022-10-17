CREATE TABLE `sys__logs`
(
    `time`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When action occurred',
    `type`      TINYINT(2) UNSIGNED                   NOT NULL COMMENT 'Type of the action',
    `action`    VARCHAR(255)                          NOT NULL COMMENT 'Short description of the action',
    `userid`    INT UNSIGNED                          NULL COMMENT 'Optional user ID, if available',
    `ip`        VARCHAR(45)                           NULL COMMENT 'IP, if available',
    `useragent` TEXT                                  NULL COMMENT 'Full useragent, if available',
    `extra`     TEXT                                  NULL COMMENT 'Any extra information available for the entry',
    CONSTRAINT `audit_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `log_type` FOREIGN KEY (`type`) REFERENCES `sys__log_types` (`typeid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Table storing logs';

CREATE INDEX `audit_userid` ON `sys__logs` (`userid`);

CREATE INDEX `time` ON `sys__logs` (`time` DESC);
