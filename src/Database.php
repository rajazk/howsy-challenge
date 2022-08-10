<?php
namespace App;
class Database{
	
	private $host  = '';
    private $user  = '';
    private $password   = "";
    private $database  = ""; 
    private $conn;

	public function __construct($config)
	{

		if($config){ 
			$config = (object)$config;
			// Setting config variables
			$this->database = $config->db;
			$this->user 	= $config->user;
			$this->password = $config->password;
			$this->host 	= $config->host;
		}

	}
    public function connect(){		

		$conn = new \mysqli($this->host, $this->user, $this->password, $this->database);
		if($conn->connect_error){
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
    }
}
?>