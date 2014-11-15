<?php
ini_set("display_errors", 1);
require_once "Backup.php";
$backupCls=new Backup();

$dbParams=array("localhost","username","password","databasename");
$backupCls->setDbParams($dbParams);
$backupCls->setDbType("MySQL");
$backupCls->save("/tmp/"); /* For MS Windows backup path maybe C:\Windows\Temp, or other path where is located temporary files */
?>
