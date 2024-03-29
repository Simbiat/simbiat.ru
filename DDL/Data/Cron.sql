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