CREATE TABLE `talks__thread_to_tags`
(
    `threadid` INT UNSIGNED NOT NULL COMMENT 'Thread ID',
    `tagid`    INT UNSIGNED NOT NULL COMMENT 'Tag ID',
    PRIMARY KEY (`threadid`, `tagid`),
    CONSTRAINT `thr_tag_tag` FOREIGN KEY (`tagid`) REFERENCES `talks__tags` (`tagid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `thr_tag_thread` FOREIGN KEY (`threadid`) REFERENCES `talks__threads` (`threadid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Threads to tags junction table' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON';
