<?php
require_once "Backup.php";
$backupCls=new Backup();

define('DBHOST','localhost');
define('DBUSER','username');
define('DBPASS','password');
define('DBNAME','databasename');

$dbType="mysql";
$osType="linux";
$backupPath="/tmp/";
/* For example in MS Windows backup path must be C:\Windows\Temp, or other path where is located temporary files */


$backupCls->setDbType($dbType);
$backupCls->setBackupType('gzip');
$backupCls->save($backupPath, $osType);
?>
