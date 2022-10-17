CREATE TABLE `ban__names`
(
    `name`   VARCHAR(64)                           NOT NULL COMMENT 'Banned (prohibited) name' PRIMARY KEY,
    `added`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL COMMENT 'When name was banned',
    `reason` TEXT                                  NULL COMMENT 'Reason for the ban'
) COMMENT 'Banned user names';

CREATE INDEX `added` ON `ban__names` (`added`);
