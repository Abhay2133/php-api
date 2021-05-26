<?php
public function read2json ()
	{
		$file = fopen($this->path, "r") or die("Error");
		$fl = fgets($file);
		$fl = str_replace("\r\n", "", $fl);
		$cols = explode(",", $fl);
		//print_r($cols);
		$json = array();
		$a = 0;
		while(!feof($file))
		{
			$str = fgets($file);
			$str = str_replace("\n", "", $str);
			$str = str_replace("\r", "", $str);
			//echo $str;
			$val = explode(",", $str);
			$data = array();
			for ($i = 0; $i < count($cols); $i++ )
			{
				$data[$cols[$i]] = $val[$i];
			}
			$json[$a++] = $data;
		}
		fclose($file) or die("error");
		echo json_encode($json);
	}
?>