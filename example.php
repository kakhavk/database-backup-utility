<?php
ini_set("display_errors", 1);
require_once "SqlDump.php";
$sqlDumpCls=new Backup();

$dbParams=array("localhost","username","password","databasename");
$sqlDumpCls->setDbParams($dbParams);
$sqlDumpCls->setDbType("MySQL");
$sqlDumpCls->save("/tmp/"); /* For MS Windows backup path maybe C:\Windows\Temp, or other path where is located temporary files */
?>
