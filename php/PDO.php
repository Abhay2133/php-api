<?php
	class PDOmysql {
		public $dbname;
		public $table;
		public $conn;
		public $host;
		function __construct ( $dbname = NULL, $table = NULL)
		{
			$this->host = "localhost";
			$this->dbname = $dbname;
			$this->table = $table;
			if($dbname === NULL || strlen($dbname) < 1){
				$this->connect();
			}
			else{
				if($this->hasDB()){
					$this->connect ($dbname);
				}
				else{
					$this->connect ();
					$this->createNewDB($dbname);
				}
			}
			
			if($this->conn->connect_error){
				die("Error : ".($this->conn)->connect_error);
			}
		}
		
		public function connect ( $dbname = null , $returnFlag = false)
		{
			$conn_str;
			if($dbname)
				$conn_str = "mysql:host=$this->host;dbname=$dbname";
			else
				$conn_str = "mysql:host=$this->host";
				
			try {
				$conn = new PDO($conn_str , "root", "");
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				if($returnFlag)
					return $conn;
				else
					$this->conn = $conn;
			} 
			catch(PDOException $e) {
 				echo "Connection failed : " . $e->getMessage();
			}
		}
		
		public function query ( $sql )
		{
			try {
				return $this->conn->exec( $sql );
			} 
			catch(PDOException $e) {
 				return array("error_mess"  => $e->getMessage(),
 									  "error_code" => $e->getCode());
			}
		}
		
		public function createNewDB ( $dbname , $setActive = true )
		{
			$this->query("CREATE DATABASE $dbname");
			if( $setActive )
				$this->setDB ($dbname);
		}
		
		public function setDB ( $dbname )
		{
			$this->dbname = $dbname;
			$this->connect( $dbname );
		}
		
		public function setTable ( $table )
		{
			$this->table = $table;
		}
		
		public function createNewTable ( $def , $table = NULL, $setActive = true)
		{
			if( $table == NULL )
				$table = $this->table;
			if( $this->hasTable( $table ))
				return "Table '$table' already exists ! ";
			$this->query("CREATE TABLE $table $def");
			if($setActive)
				$this->setTable( $table );
		}
		
		public function insert ( $data, $cols, $debug)
		{
			$sql = "INSERT INTO $this->table $cols VALUES $data";
			if($debug == true){
				echo "SQL : ";
				print_r($sql);
			}
			return $this->query($sql);
		}
		
		public function hasDB ()
		{
			$query = $this->getQueryOutput("Show Databases");
			if (count($query) > 0) {
				foreach ( $query as $row) {
					if($this->dbname === $row["Database"])
   						return true;
				}
			}
			return false;
		}
		
		public function hasTable ( $table = NULL)
		{
			if( $table == NULL )
				$table = $this->table;
				
			$query = $this->getQueryOutput("show tables;", $this->dbname);
			
			if (count($query) > 0) {
				foreach ( $query as $row) {
					if($table === $row["Tables_in_$this->dbname"])
   					return true;
				}
			}
			return false;
		}
		
		public function select ( $cols = "*", $condition = null, $debug)
		{
			$sql = " SELECT $cols FROM $this->table ";
			$sql .= (strlen($condition) > 1) ? "WHERE $condition" : "";
			if($debug)
				echo $sql;
			return $this->getQueryOutput ( $sql , $this->dbname);
		}
		
		public function update ( $col_val, $condition )
		{
			return $this->query("UPDATE $this->table SET $col_val WHERE $condition");
		}
		
		public function getQueryOutput ( $sql , $dbname = null , $debug = false)
		{
			try {
				$conn = $this->connect ($dbname , true);
				$stmt = $conn->prepare ( $sql );
				$conn = null;
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				return $stmt->fetchAll();
			} 
			catch(PDOException $e) {
 				echo "Connection failed : " . $e->getMessage();
			}
		}
		
		function __destruct ()
		{	
			if($this->conn)
				$this->conn = null;
		}
	}
?>