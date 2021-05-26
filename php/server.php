<?php

	require "PDO.php";
	$data = json_decode(file_get_contents("php://input"));

	$res = array("status" => 200);
	
	if($_REQUEST["signup"])
	{
		$uid = $data->uid;
		$pswrd = $data->pswrd;
		$pswrd = password_hash($pswrd, PASSWORD_DEFAULT);
		$time = time();
		$loginToken = bin2hex(random_bytes(10));
		$users = new PDOmysql ("users", "users_details");
		$res["table"] = $users->createNewTable("(uid varchar(10) PRIMARY key,pswrd varchar(255) not null,loginToken varchar(20), lastLoginTime int(20))");
		$res["data"] = $users->insert("(\"$uid\", \"$pswrd\", \"$loginToken\", $time)", "(uid, pswrd, loginToken, lastLoginTime)", $_REQUEST["debug"]);
	}
	else
	if( $_REQUEST["genToken"] )
	{
		$token = bin2hex(random_bytes(10));
		$res["token"] = $token;
	}
	else
	if( $_REQUEST["login"] )
	{
		$uid = $data->uid;
		$pswrd = $data->pswrd;
		$users = new PDOmysql ("users", "users_details");
		$select = $users->select ("uid, pswrd", "uid = \"$uid\"", $_REQUEST["debug"]);
		if( count($select) > 0)
		{
			$res["data"]  = password_verify( $pswrd, $select[0]["pswrd"]) ? "Login Succes !" : "Wrong Password !";
			if($res["data"] === "Login Succes !"){
				$token = bin2hex(random_bytes(10));
				$time = time();
				$res["update"] = $users->update("loginToken = \"$token\", lastLoginTime = $time", "uid = \"$uid\"");
				$res["token"] = $token;
			}
		}
		else
			$res["data"]  = "Wrong Username !";
	}
		
		/*
		else {
			
			$json["snackbar"] = $users->add("$uid,$pswrd,NULL");
			$json["status"] = 200;
		}
	}
	else
	if($_REQUEST["read"])
	{
		$db->read2json();
	}
	else
	if($_REQUEST["login"]){
		$uid = $data->uid;
		$pswrd = $data->pswrd;
		
		if( $db->searchCol("uid", $uid) && $db->searchCol("pswrd", $pswrd) )
		{
			$json["snackbar"] =  "Login Success !";
			$json["status"] = 200;
		}
		else{
			$json["snackbar"] =  "Username or password is wrong ! ";
			$json["status"] = 403;
		}
	}
	else
	if(strlen($_GET["hasuid"])){
		$uid = $_GET["hasuid"];
		$debug = $_GET["debug"];
		
		$db = new DB("users.csv");
		$search_data = $db->searchCol("username", $uid, $debug);
		//print_r($search_data);
		if($search_data["pos"] > 0)
		{
			$json["uid_info"] = "Username already taken !";
			$json["status"] = 409;
		}
		else
		{
			$json["uid_info"] = "Username available";
			$json["status"] = 200;
		}
	}
	else
	if( $_REQUEST["update"])
	{
		$old_data = $data->od;
		$new_data = $data->nd;
		$col = $data->col;
		$json["success"] = $db->update ($col, $old_data, $new_data, $_REQUEST["debug"]);
	}
	*/
	echo json_encode($res);
?>