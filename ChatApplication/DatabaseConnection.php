<?php
	
class DatabaseConnection {
		
	private static $_instance = null;
	private $_conn;
	private $_host = 'localhost';
	private $_db = 'board';
	private $_userName = 'root';
	private $_password = '';
		
	private function __construct() {
		$this->_conn = new PDO('mysql:host='.$this->_host.';dbname='.$this->_db.'',$this->_userName,$this->_password);
	}
		
	public static function getInstance() {
		if(!self::$_instance)	{
			self::$_instance = new DatabaseConnection();
		}
		return self::$_instance;
	}
		
	public function getConnection() {
		return $this->_conn;
	}
}

?>