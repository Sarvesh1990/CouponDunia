<?php

class DBConnection{
	public $database;
	private static $instance;

	private function __construct(){
		$DEBUG=0;
		try{
			if($DEBUG)
				echo "Inside try of constructior of DBConnection class";
			$databaseConfig = parse_ini_file('database_connection_details.ini');
			$this->database = mysqli_connect($databaseConfig['hostname'],$databaseConfig['user'],$databaseConfig['password'],$databaseConfig['database']);
		}
		catch(Exception $ex){
			echo 'Error : '.$ex->getMessage();
		}
	}

	public static function getInstance(){
		if(!isset(self::$instance)){
			self::$instance = new DBConnection;
		}
		return self::$instance;
	}

	public static function close(){
		$database = null;
	}

	public static function getData($mysqlQuery){
		$conn = self::getInstance()->database;
		$result = mysqli_query($conn, $mysqlQuery);
		if(!$result){
			return mysqli.error($conn);
		}
		return $result;
	}
}