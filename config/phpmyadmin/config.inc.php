<?php
/**
 * Custom PHPMyAdmin settings that differ from defaults. Explanations at https://docs.phpmyadmin.net/
 */
declare(strict_types = 1);

/**
 * Disallow editing of binary fields
 * valid values are:
 *   false    allow editing
 *   'blob'   allow editing except for BLOB fields
 *   'noblob' disallow editing except for BLOB fields
 *   'all'    disallow editing
 * default = 'blob'
 */
$cfg['ProtectBinary'] = 'all';

$cfg['DefaultConnectionCollation'] = 'utf8mb4_uca1400_nopad_as_cs';
$cfg['MaxNavigationItems'] = 1000;
$cfg['ExecTimeLimit'] = 60000000;
$cfg['LoginCookieValidityDisableWarning'] = true;
$cfg['RetainQueryBox'] = true;
$cfg['ShowDbStructureCreation'] = true;
$cfg['ShowDbStructureLastUpdate'] = true;
$cfg['ShowDbStructureLastCheck'] = true;
$cfg['DisableMultiTableMaintenance'] = true;
