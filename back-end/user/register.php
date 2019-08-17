<?php
    // Author: 65wu (https://github.com/65wu)

	header('Content-Type:application/json');  
    header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers:Origin,X-Requested-With,Content-Type,Accept");
	header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');   	

		$content = urldecode(file_get_contents("php://input"));		
		$params = json_decode($content,true);
		
		// 过滤 OPTIONS
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
} else {
	
		//获取请求信息
		$username = $params["username"];  
        $psw = $params["password"];  
		$nickname = $params["nickname"];
		$real = $params["realname"];
        $group = $params["groupname"];
		

		
    
              
				//连接数据库，并创建用户信息
				$dbcon = mysql_connect("123.207.25.59","ncuhome","hackweek");   //连接数据库  
                mysql_select_db("test");  //选择数据库  
                mysql_query("set names 'utf8'",$dbcon); //设定字符集

				//取出数据库信息做判断
                $sql_username = "select username from user where username = '$params[username]'"; //SQL语句  
                $exist_username = mysql_query($sql_username);    //执行SQL语句  
                $num_username = mysql_num_rows($exist_username); //统计执行结果影响的行数 

				#$sql_nickname = "select nickname from user where nickname = '$params[nickname]'"; 
				#$exist_nickname = mysql_query($sql_nickname);    
				#$num_nickname = mysql_num_rows($exist_nickname);  
				
				
                if($num_username)       //如果已经存在该用户名  
                {  
                    $arr = array("success"=>false,"message"=>"用户名已存在");
                }
                
                #else if($num_nickname)    //如果已经存在该昵称  
                #{  				
                #    $arr = array("success"=>false,"message"=>"昵称已存在");
                #}				
				

				
                else    //不存在当前注册用户名称  
                {  
					
					
					
					//生成token
					//生成一串随机字符串
					$v = 1;
					$key = mt_rand();
					$hash = hash_hmac("sha1", $v . mt_rand() . time(), $key, true);
					$unfinished_token = str_replace('=', '', strtr(base64_encode($hash), '+/', '-_'));
					
					
					//获取当前时间
					$time=date("y-m-d h:i:sa");
					
					
					
					//加密函数
					function encode($string = '', $skey = 'cxphp') {
					    $strArr = str_split(base64_encode($string));
					    $strCount = count($strArr);
					    foreach (str_split($skey) as $key => $value)
					        $key < $strCount && $strArr[$key].=$value;
					    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
					 }
					
					//时间加密后的字符串		
					$enstring = encode($time);
					
					//其中"&"作为分隔标志，以方便去掉后半部分的time加密后的字符串,生成时效性token完毕
					$token = $unfinished_token."&".$enstring;
						
					
					$sql_insert = "insert into user (username,password,nickname,realname,groupname,token) values('$params[username]','$params[password]','$params[nickname]','$params[realname]','$params[groupname]','$token')";  
                    $arr_insert = mysql_query($sql_insert);  

					
					$sql_user_id = "select user_id from user where username = '$params[username]'";
                    $result_id = mysql_query($sql_user_id);  
                    $row = mysql_fetch_array($result_id);
                    $user_id = $row[0];
					
					
                    if($arr_insert)//注册成功！  
                    {  
                       $arr = array("success"=>true,"id"=>$user_id,"token"=>$token);//返回token
                    }  
                    else 
                    {  
                        $arr = array("success"=>false,"message"=>"请求失败！");
                    }  
                }  
             

        
	
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);//以JSON格式提交给ajax
}
?>
