<?php

class DB {
	public $path;
	
	function __construct ($name)
	{	$this->path = "./db_data/".$name;	}
	
	public function write ($data)
	{
		$file = fopen($this->path, "w") or die("error");
		fwrite($file, $data);
		fclose($file) or die("error");
	}
	
	public function add ($data)
	{
		$file = fopen($this->path, "a") or die("error");
		fwrite($file, "\n".$data);
		fclose($file) or die("error");
		return "Data added Successfully !";
	}
	
	public function showRaw ()
	{
		$file = fopen($this->path, "r") or die("error");
		while(!feof($file))
			echo fgets($file)."<br>";	
		//echo fread($file, filesize($this->path));
		fclose($file) or die("error");
	}
	
	public function getSize ()
	{
		return filesize($this->path);
	}
	
	public function searchCol($col, $data, $debug = false)
	{
		if($debug)
		{
			echo "\ninside SearchCol method ";
		}
		$cols = $this->getFirstRow();
		if($debug){
			echo "cols : ";
			print_r($cols);
		}
		$i = array_search($col, $cols);
		
		if($debug){
			echo "\n Index of Col : ";
			print_r($i);
			}
		if( $i === false )
			return "Error : Column in missing in Table !";
		$file = fopen($this->path, "r") or die("Error");
		$pos = 0;
		$pos_row = array("pos" => 0, "row" => ""); // return pos and row string 
											// in an assoc. array
		$firstLineTrash = fgets($file);
		// to move pointer to second line
		while(!feof($file))
		{
			$pos = ftell($file);
			$str = fgets($file);
			$str = str_replace("\n", "", $str);
			$str = str_replace("\r", "", $str);
			$val = explode(",", $str);
			if($debug){
				echo " \n pos : ".$pos.", Row : ";
				print_r($val);
			}
			if( $data == $val[$i]){
				$pos_row["pos"] = $pos;
				$pos_row["row"] = $val;
				break;
			}
		}
		fclose($file) or die("error");
		return $pos_row;
	}
	
	public function clear ()
	{
		$file = fopen($this->path, "w") or die("error");
		fwrite($file, "");
		fclose($file) or die("error");
	}
	
	public function update($col, $oldData, $newData, $debug = false)
	{
		if($debug)
		{
			echo "Inside Update Method ";
		}
		$oldData_pos_n_row = $this->searchCol($col, $oldData, $debug);
		if($debug){
			echo "\nOldData_pos_n_row : ";
			print_r($oldData_pos_n_row);
		}
		if($oldData_pos_n_row["pos"] === 0)
		{
			if( $debug ){
				echo "\n Old data not Found in any Row \n";
			}
			return;
		}
		$pos = $oldData_pos_n_row["pos"];
		$old_row = $oldData_pos_n_row["row"];
		$old_row_size = -1;	// for Commas
		foreach($old_row as $cell)
		{
			$old_row_size += strlen($cell) + 1;
		}
		
		$firstRow = $this->getFirstRow();
		$i = array_search($col, $firstRow);
		
		$old_row[$i] = $newData;
		$cols_str = implode(",", $old_row);
		$cols_str = $cols_str;
		
		$furtherDataPos = $pos + $old_row_size;
		$furtherData = file_get_contents($this->path, NULL, NULL, $furtherDataPos);
		$file = fopen($this->path, "rw+") or die("error");
		fseek($file, $pos);
		fwrite($file, $cols_str);
		fwrite($file, $furtherData);
		if($debug){
			echo "Current Pos (ftell) : ".ftell($file);
			echo "\nFurther Data : $furtherData \n Further data Pos : $furtherDataPos \n pos : $pos \n old_data_size : $old_row_size";
		}
		fclose($file) or die("error");
		return true;
	}
	
	public function getFirstRow () // returns assoc array
	{
		$file = fopen($this->path, "r") or die("Error");
		$fl = fgets($file);
		$fl = str_replace("\r", "", $fl);
		$fl = str_replace("\n", "", $fl);
		$cols = explode(",", $fl);
		fclose($file) or die("error");
		return $cols;
	}
	
	public function createTable ( $name, $def )
	{
		$columms = explode(",", $def);
		
		if(!file_exist($this->path) || !$this->getSize()){
			$file = fopen ($this->path, "w");
			fwrite($file, "Table:$name\n$def");
			}
		else{
			$file = fopen ($this->path, "a");
				fwrite($file, "\n\nTable:$name\n$def");
		}
			
	}
	
	

}


?>