/*Static CRON tasks*/

INSERT IGNORE INTO `cron__tasks` (`task`, `function`, `object`, `parameters`, `allowedreturns`, `maxTime`, `description`) VALUES
('bicUpdate', 'update', '\\Simbiat\\bictracker\\Library', NULL, NULL, 3600, 'Job to update BIC library'),
('cleanAvatars', 'cleanAvatars', '\\Simbiat\\Talks\\Cron', NULL, NULL, 3600, 'Removing excessive avatars'),
('cleanUploads', 'cleanFiles', '\\Simbiat\\Talks\\Cron', NULL, NULL, 3600, 'Removing unused and orphaned uploaded files'),
('cookiesClean', 'cookiesClean', '\\Simbiat\\Maintenance', NULL, NULL, 3600, 'Job to purge old cookies'),
('dbMaintenance', 'optimize', '\\Simbiat\\optimizeTables', '{\"extramethods\":[{\"method\":\"setMaintenance\",\"arguments\":[\"sys__settings\",\"setting\",\"maintenance\",\"value\"]},{\"method\":\"setJsonPath\",\"arguments\":[\".\\/data\\/tables.json\"]}]}', NULL, 3600, 'Job to optimize tables'),
('ffAddJobs', 'UpdateJobs', '\\Simbiat\\fftracker\\Cron', NULL, NULL, 3600, 'Add new jobs to table'),
('ffAddServers', 'UpdateServers', '\\Simbiat\\fftracker\\Cron', NULL, NULL, 3600, 'Adds new servers'),
('ffUpdateEntity', 'UpdateEntity', '\\Simbiat\\fftracker\\Cron', NULL, '[\"character\", \"freecompany\", \"linkshell\", \"crossworldlinkshell\", \"pvpteam\", \"achievement\"]', 3600, 'Update FFXIV entities'),
('ffUpdateOld', 'UpdateOld', '\\Simbiat\\fftracker\\Cron', NULL, NULL, 3600, 'Update oldest FFXIV entities'),
('ffUpdateStatistics', 'UpdateStatistics', '\\Simbiat\\fftracker\\Cron', NULL, NULL, 3600, 'Update FFXIV statistics'),
('filesClean', 'filesClean', '\\Simbiat\\Maintenance', NULL, NULL, 3600, 'Delete old files'),
('lockPosts', 'lockPosts', '\\Simbiat\\Talks\\Cron', NULL, NULL, 3600, 'Locking posts for editing'),
('logsClean', 'logsClean', '\\Simbiat\\Maintenance', NULL, NULL, 3600, 'Job to purge old logs'),
('sessionClean', 'sessionClean', '\\Simbiat\\Maintenance', NULL, NULL, 3600, 'Job to purge old sessions'),
('sitemap', 'generate', '\\Simbiat\\Sitemap\\Generate', NULL, NULL, 3600, 'Job to generate text and XML sitemap files'),
('statisticsClean', 'statisticsClean', '\\Simbiat\\Maintenance', NULL, NULL, 3600, 'Delete old statistical data');

INSERT IGNORE INTO `cron__schedule` (`task`, `arguments`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`, `status`, `runby`, `sse`, `registered`, `updated`, `nextrun`, `lastrun`, `lastsuccess`, `lasterror`) VALUES
('bicUpdate', '', 43200, NULL, NULL, 5, 'Updating BIC library', 0, NULL, 0, '2018-02-15 07:41:10', '2018-02-15 07:41:10', '2022-10-21 23:00:00', '2022-10-21 14:13:58', '2022-10-08 07:56:25', '2022-10-21 14:13:59'),
('cleanAvatars', '', 86400, NULL, NULL, 0, 'Removing excessive avatars', 0, NULL, 0, '2022-11-30 13:52:45', '2022-11-30 13:52:45', '2022-11-30 22:00:00', NULL, NULL, NULL),
('cleanUploads', '', 86400, NULL, NULL, 0, 'Cleaning uploaded files', 0, NULL, 0, '2022-11-30 09:19:53', '2022-11-30 09:19:53', '2022-11-30 22:01:00', NULL, NULL, NULL),
('cookiesClean', '', 86400, NULL, NULL, 9, 'Removing old cookies', 0, NULL, 0, '2018-06-21 18:23:56', '2018-06-21 18:23:56', '2022-10-22 05:00:00', '2022-10-21 14:14:22', '2022-10-21 14:14:22', NULL),
('dbMaintenance', '[\"simbiatr_simbiat\", true, true]', 2592000, NULL, NULL, 0, NULL, 0, NULL, 0, '2018-02-15 07:40:43', '2018-02-15 07:40:43', '2022-10-23 05:00:00', '2022-10-21 14:21:47', '2022-10-21 14:31:28', NULL),
('ffAddJobs', '', 604800, NULL, '[3]', 1, 'Checking for new jobs on Lodestone', 0, NULL, 0, '2021-12-03 15:55:43', '2021-12-03 15:55:43', '2022-10-26 07:00:00', '2022-10-21 14:21:34', '2022-10-21 14:21:40', '2022-07-06 07:00:03'),
('ffAddServers', '', 604800, NULL, '[3]', 1, 'Checking for new servers on Lodestone', 0, NULL, 0, '2021-12-03 15:57:01', '2021-12-03 15:57:01', '2022-10-26 07:00:00', '2022-10-21 14:21:40', '2022-10-21 14:21:42', '2022-07-06 07:00:03'),
('ffUpdateOld', '[5]', 60, NULL, NULL, 0, 'Updating old FFXIV entities', 0, NULL, 0, '2021-04-25 16:57:14', '2021-04-25 16:57:14', '2022-10-21 14:22:14', '2022-10-21 14:21:00', '2022-10-21 14:21:40', '2022-07-10 14:52:58'),
('ffUpdateStatistics', '', 86400, NULL, NULL, 2, 'Updating FFXIV statistics', 0, NULL, 0, '2021-04-25 16:43:10', '2021-04-25 16:43:10', '2022-10-21 16:43:10', '2022-10-21 14:14:22', '2022-10-21 14:18:40', '2022-07-09 16:50:20'),
('filesClean', '', 604800, NULL, NULL, 0, 'Removing old files', 0, NULL, 0, '2021-12-31 17:46:38', '2021-12-31 17:46:38', '2022-10-21 17:46:38', '2022-10-21 14:19:28', '2022-10-21 14:19:28', NULL),
('lockPosts', '', 3600, NULL, NULL, 9, 'Locking posts', 0, NULL, 0, '2022-11-30 09:17:41', '2022-11-30 09:17:41', '2022-11-30 22:00:00', NULL, NULL, NULL),
('logsClean', '', 2592000, NULL, NULL, 9, 'Removing old logs', 0, NULL, 0, '2018-06-21 18:23:56', '2018-06-21 18:23:56', '2022-11-04 05:00:00', '2022-10-08 08:01:01', '2022-10-08 08:01:01', NULL),
('sessionClean', '', 300, NULL, NULL, 9, 'Removing old sessions', 0, NULL, 0, '2018-06-21 18:23:56', '2018-06-21 18:23:56', '2022-10-21 14:25:00', '2022-10-21 14:20:00', '2022-10-21 14:20:00', '2022-10-21 14:15:00'),
('sitemap', '', 86400, NULL, NULL, 0, 'Generating sitemap files', 0, NULL, 0, '2022-10-21 13:51:44', '2022-10-21 13:51:44', '2022-10-21 21:00:00', '2022-10-21 14:21:45', '2022-10-21 14:21:47', NULL),
('statisticsClean', '', 2592000, NULL, NULL, 0, 'Removing old statistical data', 0, NULL, 0, '2021-12-31 17:46:38', '2021-12-31 17:46:38', '2022-10-27 17:46:38', '2022-10-21 14:31:28', '2022-10-21 14:31:28', NULL);

INSERT IGNORE INTO `cron__tasks` (`task`, `function`, `object`, `parameters`, `allowedreturns`, `maxTime`, `description`) VALUES
('ffNewCharacters', 'registerNewCharacters', '\\Simbiat\\fftracker\\Cron', NULL, NULL, 3600, 'Schedule jobs to add potential new characters'),
('ffNewLinkshells', 'registerNewLinkshells', '\\Simbiat\\fftracker\\Cron', NULL, NULL, 3600, 'Check for potential new linkshells and schedule jobs for them');

INSERT IGNORE INTO `cron__schedule` (`task`, `arguments`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`, `nextrun`) VALUES
('ffNewCharacters', '', 86400, NULL, NULL, 0, 'Scheduling potential new character', '2024-04-27 12:00:00'),
('ffNewLinkshells', '', 3600, NULL, NULL, 0, 'Checking for new linkshells', '2022-04-27 01:00:00');

UPDATE `cron__tasks` SET `function`='dbOptimize', `object`='\Simbiat\Maintenance', `parameters`=null WHERE `task`='dbMaintenance';
UPDATE `cron__schedule` SET `arguments`='' WHERE `task`='dbMaintenance';

UPDATE `cron__tasks` SET `system`=1;
UPDATE `cron__schedule` SET `system`=1 WHERE `task`!='ffUpdateEntity';
UPDATE `cron__tasks` SET `object` = 'Simbiat\\Maintenance' WHERE `cron__tasks`.`task` = 'dbMaintenance';

UPDATE `cron__settings` SET `value` = '14' WHERE `cron__settings`.`setting` = 'logLife';

UPDATE `cron__schedule` SET `arguments` = '[50]' WHERE `cron__schedule`.`task` = 'ffUpdateOld' AND `cron__schedule`.`arguments` = '[5]' AND `cron__schedule`.`instance` = 1;
UPDATE `cron__schedule` SET `arguments` = '[50, \"$cronInstance\"]' WHERE `cron__schedule`.`task` = 'ffUpdateOld' AND `cron__schedule`.`arguments` = '[50]' AND `cron__schedule`.`instance` = 1;
INSERT INTO `cron__schedule`(`task`, `arguments`, `instance`, `system`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`, `nextrun`) SELECT `task`, `arguments`, 2 as `instance`, `system`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`, `nextrun` FROM `cron__schedule` WHERE `task`='ffUpdateOld' AND `instance`=1;
INSERT INTO `cron__schedule`(`task`, `arguments`, `instance`, `system`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`, `nextrun`) SELECT `task`, `arguments`, 3 as `instance`, `system`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`, `nextrun` FROM `cron__schedule` WHERE `task`='ffUpdateOld' AND `instance`=1;
INSERT INTO `cron__schedule`(`task`, `arguments`, `instance`, `system`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`, `nextrun`) SELECT `task`, `arguments`, 4 as `instance`, `system`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`, `nextrun` FROM `cron__schedule` WHERE `task`='ffUpdateOld' AND `instance`=1;

UPDATE `cron__schedule` SET `arguments`='[50, "$cronInstance"]' WHERE `task`='ffUpdateOld';
UPDATE `cron__settings` SET `value` = '4' WHERE `cron__settings`.`setting` = 'maxThreads';

INSERT IGNORE INTO `cron__tasks` (`task`, `function`, `object`, `parameters`, `allowedreturns`, `maxTime`, `description`, `system`) VALUES
('dbForBackup', 'forBackup', '\\Simbiat\\Maintenance', NULL, NULL, 3600, 'Generate DDLs and recommended dump order for current DB structure', 1);
INSERT IGNORE INTO `cron__schedule` (`task`, `arguments`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`, `nextrun`, `system`) VALUES
('dbForBackup', '', 86400, NULL, NULL, 9, 'Dumping DDLs', '2024-06-21 02:00:00', 1);

DELETE FROM `cron__tasks` WHERE `cron__tasks`.`task` = 'ffAddJobs';

UPDATE `cron__tasks` SET `object`=REPLACE(`object`, '\\Simbiat', '\\Simbiat\\Website');
UPDATE `cron__tasks` SET `object` = '\\Simbiat\\Website\\Maintenance' WHERE `cron__tasks`.`task` = 'dbMaintenance';

INSERT INTO `cron__tasks` (`task`, `function`, `object`, `parameters`, `allowedreturns`, `maxTime`, `system`, `description`) VALUES ('argon', 'argon', '\\Simbiat\\Website\\Maintenance', NULL, NULL, '3600', '1', 'Job to recalculate optimal Argon encryption settings');
INSERT INTO `cron__schedule` (`task`, `arguments`, `instance`, `system`, `frequency`, `dayofmonth`, `dayofweek`, `priority`, `message`) VALUES ('argon', '', '1', '1', '2592000', NULL, NULL, '0', 'Recalculating Argon settings');