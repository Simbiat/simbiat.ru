CREATE TABLE `sys__settings`
(
    `setting`     VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL PRIMARY KEY,
    `value`       VARCHAR(5000)                                    NULL,
    `description` TEXT COLLATE utf8mb4_uca1400_nopad_ai_ci         NULL
) `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
