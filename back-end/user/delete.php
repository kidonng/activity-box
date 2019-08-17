<?php
    // Author: 65wu (https://github.com/65wu)

	header('Content-Type:application/json');  
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers:Origin,X-Requested-With,Content-Type,Accept");
	header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE'); 


	//连接数据库，并创建用户信息	
	mysql_connect  ("123.207.25.59","ncuhome","hackweek");   //连接数据库	
	mysql_select_db("test");                                 //选择数据库	
	mysql_query    ("set names 'utf8'");                     //设定字符集	

  	
	
	$par = $_SERVER["QUERY_STRING"];
	$query = explode('=',$par);
	$user_id = $query[1];//希望删除的id
	
	
	
	if($user_id!='')//token成功传入的话,选择数据库中token对应用户的信息并删除	
	{		
	
		$sql = "DELETE FROM user WHERE user_id='$user_id'";
		$result = mysql_query($sql);
	
		$arr = array("success"=>true);
		
	}	
	
	
	else//user_id未传入
	{
		$arr = array("success"=>false,"message"=>"操作失败");
	}	
	
	
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);
?>	
