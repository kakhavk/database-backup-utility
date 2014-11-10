dbbackup-mysql-postgresql
=========================

Backup Database from MySQL or PostgreSQL

See example bellow 

require_once "Backup.php";
$backupCls=new Backup();

define('DBHOST','localhost'); 
define('DBUSER','username'); 
define('DBPASS','password'); 
define('DBNAME','databasename');


$dbType="pgsql";
$osType="linux";
$backupPath="/tmp/";

$backupCls->setDbType($dbType);
$backupCls->setBackupType('gzip');
$backupCls->save($backupPath, $osType);

