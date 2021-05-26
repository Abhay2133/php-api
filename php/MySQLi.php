<?php
	class MySQLi {
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
				$this->conn = new mysqli($this->host, "root", "");
			}
			else{
				if($this->hasDB())
					$this->conn = new mysqli($this->host, "root", "", $dbname);
				else{
					$this->conn = new mysqli($this->host, "root", "");
					$this->createNewDB($dbname);
				}
			}
			
			if($this->conn->connect_error){
				die("Error : ".($this->conn)->connect_error);
			}
		}
		
		public function createNewDB ( $dbname )
		{
			$this->conn->query("CREATE DATABASE $dbname");
			$this->setDB ($dbname);
		}
		
		public function setDB ( $dbname )
		{
			$this->dbname = $dbname;
			$this->conn = new mysqli($this->host, "root", "", $dbname);
			if($this->conn->connect_error){
				die("Error : $this->conn->connect_error");
			}
		}
		
		public function setTable ( $table )
		{
			$this->table = $table;
		}
		
		public function createNewTable ( $def , $table = NULL)
		{
			if( $table == NULL )
				$table = $this->table;
			//echo "\n Table : $table \n";
			if( $this->hasTable( $table ))
				return "Table '$table' already exists ! ";
				
			//$sql = "use $this->dbname; CREATE TABLE $table $def";
			$sql = "CREATE TABLE $table $def";
			echo "\n SQL (createNewTable) : $sql \n\n";
			
			//$conn = new mysqli($this->host, "root", "", $this->dbname);
			$result = $this->conn->query( $sql );
			echo "\n \n $createTable query result : ";
			print_r($result);
			echo "\n \n";
			$this->setTable( $table );
			return "Table '$table' created ..."; 
		}
		
		public function insert ( $data, $cols, $debug)
		{
			$sql = "INSERT INTO $this->table $cols VALUES $data";
			if($debug == true){
				echo "SQL : ";
				print_r($sql);
			}
			return $this->conn->query($sql);
		}
		
		public function hasDB ()
		{
			$query = $this->getQueryOutput("Show Databases");
			if (count($query) > 0) {
				foreach ( $query as $row) {
					foreach ( $row as $col ){
						if($this->dbname === $row["Database"])
   						return true;
					}
				}
			}
			return false;
		}
		
		public function hasTable ( $table = NULL)
		{
			echo "\n \n Inside hasTable \n\n";
			if( $table == NULL )
				$table = $this->table;
				
			$query = $this->getQueryOutput("use $this->dbname; show tables;", true);
			print_r($query);
			
			if (count($query) > 0) {
				echo "\n Count(query) > 0 ( True ) \n\n";
				foreach ( $query as $row) {
					foreach ( $row as $col ){
						if($table === $row["Tables_in_$this->dbname"])
   						return true;
   						echo "$table === ".$row["Tables_in_$this->dbname"]."\n";
					}
				}
			}
			return false;
		}
		
		public function getQueryOutput ( $sql , $debug = false)
		{
			$conn = new mysqli($this->host, "root", "", $this->dbname);
			$result = $conn->query ( $sql );
			$conn->close();
			if($debug){
				echo "\n Inside getQueryOutput \n sql : $sql \n result : ";
				print_r($result);
				echo "\n";
			}
				
			$outputRows = array();
			$i = 0;
			if ($result->num_rows > 0) {
				if($debug)
					echo "\nNum_rows > 0 ( inside getQueryOutput )\n";
				while($row = $result->fetch_assoc()) {
					$outputRows[$i++] = $row;
				}
			}
			return $outputRows;
		}
		
		function __destruct ()
		{	
			if($this->conn)
				$this->conn->close();
		}
	}
?>