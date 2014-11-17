<?php
# Database Database Backup class
# Version 1.1
# Writen By Kakhaber Kashmadze <info@soft.ge>
# Licensed under MIT License

# This version on Linux works with archive type gzip and with databases MySQL and PostgreSQL.
# On windows environment path variable must be set for mysqldump and pg_dump

class Backup{
	
    private $dbType='mysql'; /* By default database type is mysql. Allowed types for mysql is mysql, for postgresql is pgsql */
    private $backupType='gzip'; /* Type of archive */
    private $dbParams=array("host","user","password","database"); /* Database parameters */
    
	function __construct(){
		;
	}
	
	/* Return database parameters */
	function retDbParams(){
		return $this->dbParams;
	}
	
	/* Set database parameters */
	function setDbParams($dbParams){
		$this->dbParams=$dbParams;
	}
	
    /* Returns Database Type */
    function retDbType(){
        return $this->dbType;
    }
    
    /* Sets Database Type: mysql or pgsql */
    function setDbType($dbType){
        $dbtype=strtolower(trim($dbType));
        if($dbtype=='mysqli') $dbtype='mysql';
        if($dbtype=='postgresql') $dbtype='pgsql';
        
        $this->dbType=$dbtype;
    }
    
    /* Return backup archvie type */
    function retBackupType(){
        return $this->backupType;
    }
    /* Set backup archvie type */
    function setBackupType($backupType){
        $this->backupType=$backupType;
    }
	
    /* Download file */
    function downFile($file, $fileNm, $ctype) {
        if (file_exists($file)) {
            if(ob_get_level()!==0) ob_clean();
            header('Content-Description: File Transfer');
            header('Content-Type: '.$ctype.'');
            header('Content-Length: ' . filesize($file));
            header('Content-Disposition: attachment; filename=' . $fileNm);
            readfile($file);
            unlink($file);
            exit;
        }

    }
    /* Backup database and download */
    function save($backupPath){
		
    	$dbparams=$this->retDbParams();
    	
		$dbhost = $dbparams[0];
		$dbuser = $dbparams[1];
		$dbpass = $dbparams[2];
		$dbname = $dbparams[3];
		
        
		$backup_path=$backupPath;
		$fileName=$dbname."_".date("Y-m-d-H-i-s").".sql";		
		$backup_file=$backup_path.$fileName;
		
		if($this->retDbType()=='mysql'){
			$conn=@mysql_connect($dbhost,$dbuser,$dbpass);
			if(!$conn){
				echo "Error when connecting to server";
				return false;
			}elseif(!mysql_select_db($dbname, $conn)){
				echo "Error selecting database";
				return false;
			}
		}elseif($this->retDbType()=='pgsql'){
			$conn=@pg_pconnect("host=".$dbhost." port=5432 dbname=".$dbname." user=".$dbuser." password=".$dbpass."");
			if(!$conn){
				echo "Error when connecting to database";
				return false;
			}
		}
		
            
            if($this->retDbType()=='mysql')
                $command = "mysqldump --opt -h ".$dbhost." -u ".$dbuser." -p".$dbpass." ".$dbname." > ".$backup_file;
            elseif($this->retDbType()=='pgsql'){
            	if(!stristr(PHP_OS, 'WIN')) exec("export PGPASSWORD=".$dbpass);            		            	
                else exec("set PGPASSWORD=".$dbpass);
                $command = "pg_dump -h ".$dbhost." -U ".$dbuser." ".$dbname." > ".$backup_file;
            }            
			exec($command);
			
			if(filesize($backup_file)<=0) {
				echo "\nError: size of file is ".filesize($backup_file);
				return false;
			}
			if($this->retBackupType()=='gzip'){
				$backupFileGzip=$backup_file.'.gz';
				$downloadFileName=$fileName.'.gz';
				$fp=fopen($backup_file, "rb");
				if($fp){
			
					if(filesize($backup_file)>0){
			
						$gz = gzopen($backupFileGzip,'wb9');
						while (!feof($fp)) {
							gzwrite($gz, fread($fp, 8096));
						}
						
						unlink($backup_file);
						fclose($fp);
						gzclose($gz);
						$this->downFile($backupFileGzip, $downloadFileName, "application/x-gzip");
					}else{
						echo "\nError: size of file is ".filesize($backup_file);
						return false;
					}
				}else{
					echo "\nError";
					return false;
				}
			
			}
		return false;
	}

	
	
	
	
	
	
	
	
	
	
	
}
