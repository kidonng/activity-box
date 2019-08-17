<?php
    // Author: 65wu (https://github.com/65wu)

	header('Content-Type:application/json');  
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers:Origin,X-Requested-With,Content-Type,Accept");
	header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');   	
	

		$content = urldecode(file_get_contents("php://input"));
		$params = json_decode($content,true);


		$username = $params["username"] ;
		$psw      = $params["password"] ;
		$nickname = $params["nickname"] ; 
		$realname = $params["realname"] ;
		$groupname= $params["groupname"];
		$bio      = $params["bio"];

		
		$headers   = apache_request_headers();	
		$token_old = $headers['Authorization'];
		$token     = $token_old;
		
		
		//连接数据库，并创建用户信息
		mysql_connect  ("123.207.25.59","ncuhome","hackweek");   //连接数据库 
		mysql_select_db("test");                                 //选择数据库  
		mysql_query    ("set names 'utf8'");                     //设定字符集
		
		$sql_token    = "select token from user where token = '$token_old'";
		$result_token = mysql_query($sql_token);  
		$row          = mysql_fetch_array($result_token);        //从数据库取出原有的token
		
		
		$a = $row[0];//a是数据库里存储的用户token
		$b = explode('&', $a);//切割token字符串取得后半部分时间字符串
		$c = $b[1];//时间字符串
		
		if($a == $token_old)//当前端传来的token与数据库存储的token相同时执行
		{	
	
			$judge = 1;//用于判断数据库操作是否成功
			
				
					
					if(!empty($psw))//修改密码	
					{
						$result = mysql_query("UPDATE user SET password = '$psw' WHERE token = '$token_old'");
						$num = mysql_num_rows($result);
						if(!$num)
						{
							$judge = 0;
						}
					}
	
	
					if(!empty($nickname))//修改昵称
					{
						$result = mysql_query("UPDATE user SET nickname = '$nickname' WHERE token = '$token_old'");
						$num = mysql_num_rows($result);
						if(!$num)
						{
							$judge = 0;
						}
					}
					
					
					if(!empty($bio))//修改简介
                    {
                    	$result = mysql_query("UPDATE user SET bio = '$bio' WHERE token = '$token_old'");
                    	$num = mysql_num_rows($result);
                    	if(!$num)
                    	{
                    		$judge = 0;
                    	}
                    }

	
					$sql    = "select realname,groupname from user where token = '$token_old'";	
					$result = mysql_query($sql);  
	                $row    = mysql_fetch_array($result);

					//从数据库中取出groupname和realname做判断是否验证
					$realname_old  = $row[0];		
					$groupname_old = $row[1];
	
					
					if(($realname_old=='')||($groupname_old==''))//如果用户信息中真实姓名和组织名有一个是空的话，就允许Ta修改相关信息
					{	
						if(!empty($realname))//修改真实姓名						
						{
							$result = mysql_query("UPDATE user SET realname = '$realname' WHERE token = '$token_old'");
							$num = mysql_num_rows($result);
							if(!$num)
							{
								$judge = 0;
							}
						}
	
	
						
						if(!empty($groupname))//修改组织名
						{
							$result = mysql_query("UPDATE user SET groupname = '$groupname' WHERE token = '$token_old'");
							$num = mysql_num_rows($result);
							if(!$num)
							{
								$judge = 0;
							}
						}
					}
					
					
					//token更新
					//解密	
					function decode($string = '', $skey = 'cxphp') {
						$strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
						$strCount = count($strArr);
						foreach (str_split($skey) as $key => $value)
							$key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
						return base64_decode(join('', $strArr));
					}
					
					
					$time_past = strtotime(decode($c));//数据库里的时间
					$time_now  = strtotime(date("y-m-d h:i:sa"));//当前时间
					$dif=ceil(($time_now-$time_past)/86400); //60s*60min*24h,时间差值并换成以以天作单位的整数 
					
					
					if($dif>1)//token过期并重新生成
					{
					
						//生成一串随机字符串	
						$v = 1;	
						$key = mt_rand();	
						$hash = hash_hmac("sha1", $v . mt_rand() . time(), $key, true);	
						$unfinished_token = str_replace('=', '', strtr(base64_encode($hash), '+/', '-_'));		
					
						
						//加密函数	
						function encode($string = '', $skey = 'cxphp') 
						{	
							$strArr = str_split(base64_encode($string));	
							$strCount = count($strArr);	
							foreach (str_split($skey) as $key => $value)	
								$key < $strCount && $strArr[$key].=$value;	
							return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));	
						}	
							
						//时间加密后的字符串			
						$enstring = encode($time_now);	
						$token_new = $unfinished_token."&".$enstring;//获得了新的token
						$token = $token_new;
						
						//在数据库里更新token
						$result = mysql_query("UPDATE user SET token = $token_new WHERE username = '$params[username]' and password = '$params[password]'");
						$num = mysql_num_rows($result);
				    
						if(!$num)//数据库更新token操作失败
						{
							$judge = 0;
						}	
					
					}
			
			if($judge=1)
			{
				$arr = array("success"=>true,"token"=>$token);
			}
			else
			{
				$arr = array("success"=>false,"message"=>"操作失败");
			}	
			
		}
		
		
		else
		{
			$arr = array("success"=>false,"message"=>"权限不足");
		}
	
		

	
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);//以JSON格式提交给ajax
	
?>	
