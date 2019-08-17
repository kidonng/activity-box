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
	$user_id = $query[1];//希望查询访问的用户id
	

	
	$token = '';//初始化 token 的值，以便判断是否拿到 token 的值

	$headers = apache_request_headers();//获取header的token	
	$token_old = $headers['Authorization'];
	$token = $token_old;
	
	
	if($token_old!='')//token成功传入的话,选择数据库中token对应用户的信息
	{		
		$sql = "select username,nickname,realname,groupname,bio,avatar from user where token = '$token_old'";
	}

	
	else if($user_id!='')
	{	
		$sql = "select username,nickname,realname,groupname,bio,avatar from user where user_id = '$user_id'";			
	}	
	
	
	
		//获取相应的值
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);	
	
		
		
		//依次获得查询的值
		$username  = $row[0];
		$nickname  = $row[1];
		$realname  = $row[2];
		$groupname = $row[3];
		$bio       = $row[4];
		$avatar    = $row[5];
		
			
			
		if(($realname!='')&&($groupname)!='')//判断被查询用户是否认证
		{
			$verified = true;
		}	
		
		else
		{
			$verified = false;
		}
		
		
		
		
		
	if($token_old!='')	
	{	
		$arr = array(
						"username"  => $username,
						"nickname"  => $nickname,
						"realname"  => $realname,
						"groupname" => $groupname,
						"bio"       => $bio,
						"avatar"    => $avatar,
						"verified"  => $verified
					);
						
	}			
	
	
	
	else if($user_id!='')
	{	

		if($username!='')
		{	
			$arr = array(
							"nickname"   => $nickname,
							"groupname"  => $groupname,
							"bio"        => $bio,
							"avatar"     => $avatar,
							"verified"   => $verified
						);
		}
		
		
		else
		{
			$arr = array("success"=>false,"message"=>"用户不存在");
		}
		
	}
	
	
	
	else
	{
		$arr = array("success"=>false,"message"=>"请求失败");
	}
	
	
	
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);	


?>

