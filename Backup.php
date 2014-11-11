<?php

# Database Database Backup class
# Version 1.0
# Writen By Kakhaber Kashmadze <info@soft.ge>
# Licensed under MIT License

# This version on Linux works with archive type: zip and gz, on Windows with archive type: zip ,
# and works with databases MySQL and PostgreSQL, also is possible customize for other database types.
# On windows environment variable path must be set for mysqldump and pg_dump

class Backup{
	
    private $dbType='mysql'; /* By default database type is mysql. Allowed types for mysql is mysql, for postgresql is pgsql */
    private $backupType='zip'; /* By default archive type is zip. In this version allowed types on linux is zip, gzip, on windows zip */
    
	function __construct(){
		;
	}
	
    /* Returns Database Type: mysql or pgsql */
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
    
    /* Return backup archvie type: zip or gzip */
    function retBackupType(){
        return $this->backupType;
    }
    /* Set backup archvie type: zip or gzip */
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
    function save($backupPath, $osType='linux'){
		
		$dbhost = DBHOST;
		$dbuser = DBUSER;
		$dbpass = DBPASS;
		$dbname = DBNAME;
		
        
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
		
		if($osType=='linux'){
        
            if($this->retBackupType()=='gzip'){
                $backup_file = $backup_file. '.gz';
                
                
                
                if($this->retDbType()=='mysql')
                    $command = "mysqldump --opt -h ".$dbhost." -u ".$dbuser." -p".$dbpass." ".$dbname." | gzip > ".$backup_file;
                elseif($this->retDbType()=='pgsql'){
                    system("export PGPASSWORD=".$dbpass);
                    $command = "pg_dump -h ".$dbhost." -U ".$dbuser." ".$dbname." | gzip > ".$backup_file;
                }
                system($command);

                        
                if(fopen($backup_file, "r")){
                   $fileName=$fileName.'.gz';
                   $this->downFile($backup_file, $fileName, "application/x-gzip");               
                   return true;
                }else{
                   echo "<br />Error saving file ".$backup_file;
                }            
            }elseif($this->retBackupType()=='zip'){
                
                    if($this->retDbType()=='mysql')
                        $command = "mysqldump --opt -h ".$dbhost." -u ".$dbuser." -p".$dbpass." ".$dbname." > ".$backup_file;
                    elseif($this->retDbType()=='pgsql'){
                        system("export PGPASSWORD=".$dbpass);
                        $command = "pg_dump -h ".$dbhost." -U ".$dbuser." ".$dbname." > ".$backup_file;
                    }
                    
                    system($command);

                    $backupFileZip=$backup_file.'.zip';
                    $downloadFileName=$fileName.'.zip';
                    
                    if(fopen($backup_file, "r")){
                        
                        if(filesize($backup_file)>0){
                            
                            $zip = new ZipArchive;
                            if ($zip->open($backupFileZip, ZipArchive::CREATE) === TRUE) {
                                $zip->addFile($backup_file, $fileName);
                                $zip->close();
                                
                                unlink($backup_file);
                                $this->downFile($backupFileZip, $downloadFileName, "application/zip");
                            }else{
                                echo "\nError: size file has not created ";
                                return false;
                            }
                            

                            
                        }else{
                            echo "\nError: size of file is ".filesize($backup_file);
                            return false;
                        }
                    }else{
                       echo "\nError";
                       return false;
                    }
            }

            
            
		}elseif($osType=='windows'){
            
            if($this->retDbType()=='mysql')
                $command = "mysqldump --opt -h ".$dbhost." -u ".$dbuser." -p".$dbpass." ".$dbname." > ".$backup_file;
            elseif($this->retDbType()=='pgsql'){
                exec("set PGPASSWORD=".$dbpass);
                $command = "pg_dump -h ".$dbhost." -U ".$dbuser." ".$dbname." | > ".$backup_file;
            }
            
			exec($command);
            
            if($this->retBackupType()=='zip'){
                $backupFileZip=$backup_file.'.zip';
                $downloadFileName=$fileName.'.zip';
                
                if(fopen($backup_file, "r")){
                    
                    if(filesize($backup_file)>0){
                        
                        $zip = new ZipArchive;
                        if ($zip->open($backupFileZip, ZipArchive::CREATE) === TRUE) {
                            $zip->addFile($backup_file, $backupFileZip);
                            $zip->close();
                            
                            unlink($backup_file);
                            $this->downFile($backupFileZip, $downloadFileName, "application/zip");
                        }else{
                            echo "\nError: size file has not created ";
                            return false;
                        }
                        

                        
                    }else{
                        echo "\nError: size of file is ".filesize($backup_file);
                        return false;
                    }
                }else{
                   echo "\nError";
                   return false;
                }
            
            }
		}
		return false;
	}

	
	
	
	
	
	
	
	
	
	
	
}
