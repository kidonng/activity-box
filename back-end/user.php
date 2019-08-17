<?php
    // Author: 65wu (https://github.com/65wu)

	header('Content-Type:application/json');  
    header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers:Origin,Authorization,Content-Type,Accept");
	header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');   
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	  exit;
	}
		$content = urldecode(file_get_contents("php://input"));		
		$params = json_decode($content,true);
		#parse_str($content,$params);
		
		//获取请求信息
		$username = $params["username"] ;
		$psw      = $params["password"] ;
		$nickname = $params["nickname"] ; 
		$realname = $params["realname"] ;
		$groupname= $params["groupname"];
		$bio      = $params["bio"];
		$avatar   = $params["avatar"];
		
		$token_old = $_SERVER['HTTP_AUTHORIZATION'];
		$token     = $token_old;
		
		
		//连接数据库，并创建用户信息
		$dbcon = mysql_connect("localhost","hackweek","hackweek");   //连接数据库 
		mysql_select_db("hackweek");  //选择数据库  
		mysql_query("set names 'utf8'",$dbcon); //设定字符集
		
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
					
	if(!empty($nickname))//注册
	{            
			
			                
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
					
				
				$sql_insert = "insert into user (username,password,nickname,realname,groupname,token,bio) values('$params[username]','$params[password]','$params[nickname]','$params[realname]','$params[groupname]','$token','$params[bio]')";  
                   $arr_insert = mysql_query($sql_insert);  
				
				$sql_id = "select id from user where username = '$params[username]'";
                   $result_id = mysql_query($sql_id);  
                   $row = mysql_fetch_array($result_id);
                   $id = $row[0];
				
				
                   if($arr_insert)//注册成功！  
                   {  
                      $arr = array("success"=>true,"token"=>$token);//返回token
                   }  
                   else 
                   {  
                       $arr = array("success"=>false,"message"=>"请求失败！");
                   }  
               }  
            
	}
	
	
	
	
	
	else//登录
	{
		
		
		//从数据库取出手机号和密码做判断
		$sql = "select username,password from user where username = '$params[username]' and password = '$params[password]'";  
		$result = mysql_query($sql);  
		$num = mysql_num_rows($result); 
		
		
		$sql_token = "select token,id from user where username = '$params[username]'";		
		$result_token = mysql_query($sql_token);  
		$row = mysql_fetch_array($result_token);//从数据库取出原有的token和id
		$token_old = $row[0];
		$id = $row[1];
		
		
		if($num)//如果用户名和密码正确  
		{  
			
			$b = explode('&', $token_old);//切割token字符串取得后半部分时间字符串
			$c = $b[1];//时间字符串
								
					
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
				
					
					if($dif>7)//token过期并重新生成
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
						$token_new = $unfinished_token."&".$enstring;//新的token
						
						
						$result = mysql_query("UPDATE user SET token = '$token_new' WHERE username = '$params[username]' and password = '$params[password]'");  
						
						
						
						if($row)//数据库更新token成功后响应
						{
							$arr = array("success"=>true,"token"=>$token_new);
						}
		
		
						else
						{
							$arr = array("success"=>false,"message"=>"请求失败");
						}	
						
						
					}
					
					
					else
					{
						$arr = array("success"=>true,"token"=>$token_old);
					}	
					
					
				
		
			
		}  
		else 
		{  
			$arr = array("success"=>false,"message"=>"用户名或密码错误！");
		}  
		
			
		
	}	
}
else if ($_SERVER['REQUEST_METHOD'] == 'PUT')//修改用户信息
{
    
    
    if($username!='')//当前端传来的username不为空
    {	
		
		$sql = "select token from user where username = '$username'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);	
		$token_sql = $row[0];//这个是数据库里的username 的token值
		
		
		
		if($token_sql==$token_old)//验证token是否相同
		{	$judge = 1;//用于判断数据库操作是否成功
			
				
					
					if(!empty($psw))//修改密码	
					{	
						$judge=2;
						$result = mysql_query("UPDATE user SET password = '$psw' WHERE username = '$username'");
						$sql_token = "select token from user where username = '$username'";	
						$result_token = mysql_query($sql_token);  
						$row = mysql_fetch_array($result_token);//从数据库取出原有的token,在修改密码时返回
						$token_old = $row[0];
						
						
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
						$token_new = $unfinished_token."&".$enstring;//新的token
						
						
						$result = mysql_query("UPDATE user SET token = '$token_new' WHERE username = '$username'");
									
					}
		
		
					if(!empty($nickname))//修改昵称
					{
						$result = mysql_query("UPDATE user SET nickname = '$nickname' WHERE username = '$username'");
					}
					
					
					if(!empty($bio))//修改简介
					{
						$result = mysql_query("UPDATE user SET bio = '$bio' WHERE username = '$username'");
					}
					
					
					if(!empty($avatar))//修改头像
					{
						$result = mysql_query("UPDATE user SET avatar = '$avatar' WHERE username = '$username'");
					}
					
					
					if(!empty($realname))//修改真实姓名						
					{
						$result = mysql_query("UPDATE user SET realname = '$realname' WHERE username = '$username'");
					}
					
					$sql    = "select groupname,realname from user where username = '$username'";	
					$result = mysql_query($sql);  
					$row    = mysql_fetch_array($result);
		
					//从数据库中取出groupname和realname做判断是否验证		
					$groupname_old = $row[0];
					$realname_old = $row[1];
		
					if($groupname!='')
					{	
						if($groupname_old=='')//如果用户信息中组织名是空的话，就允许Ta修改相关信息
						{		
							//修改组织名
						
							$result = mysql_query("UPDATE user SET groupname = '$groupname' WHERE username = '$username'");
							
						}
						
						
						
						
					}
			
					if($realname!='')
					{	
						if($realname_old=='')//如果用户信息中组织名是空的话，就允许Ta修改相关信息
						{		
							//修改组织名
						
			        		$result = mysql_query("UPDATE user SET realname = '$realname' WHERE username = '$username'");
			        		
			        	}
                    	
			        	
			        	
			        	
			        }
			
			
		
			if($judge==2)
			{
				$arr = array("success"=>true,"token"=>$token_new);
			}
			
			else if($judge==1)
			{
				$arr = array("success"=>true);
			}
			
			else
			{
				$arr = array("success"=>false,"message"=>"修改失败");//用户已验证，但修改了groupname,数据库拒绝了用户的修改
			}	
    	}
		
		else
		{
			$arr = array("success"=>false,"message"=>"修改失败");//验证失败，token不符合
		}	
		
    }
    
    
    else
    {
    	$arr = array("success"=>false,"message"=>"修改失败");//未给出username参数
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'GET')//查询用户信息
{
	$par = urldecode($_SERVER["QUERY_STRING"]);
    $query = explode('=',$par);
    $username = $query[1];//希望查询访问的用户名
    
    
    
    $token = '';//初始化 token 的值，以便判断是否拿到 token 的值
    
    $token_old = $_SERVER['HTTP_AUTHORIZATION'];
    $token = $token_old;
    
    
    if($token_old!='')//token成功传入的话,选择数据库中token对应用户的信息
    {		
    	$sql = "select username,nickname,realname,groupname,bio,avatar from user where token = '$token_old'";
		//获取相应的值
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);	
    }
    
    
    else
    {	
    	$sql = "select username,nickname,realname,groupname,bio,avatar from user where username = '$username'";
		//获取相应的值
        $result = mysql_query($sql);
		$row = mysql_fetch_array($result);	
    }	
    
    
  
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
              			"success"   => true,
    					"username"  => $username,
    					"nickname"  => $nickname,
    					"realname"  => $realname,
    					"groupname" => $groupname,
    					"bio"       => $bio,
    					"avatar"    => $avatar,
    					"verified"  => $verified
    				);
    					
    }			
    
    
    
    else if($username!='')
    {	
    
    
    		
    	$arr = array(
                       	"success"    => true,
    					"nickname"   => $nickname,
    					"groupname"  => $groupname,
    					"bio"        => $bio,
    					"avatar"     => $avatar,
    					"verified"   => $verified
    				);
    	
    	
    		
    }
    
    else if($username=='')
    {
    	$arr = array("success"=>false,"message"=>"用户不存在");
    }
    else
    {
    	$arr = array("success"=>false,"message"=>"请求失败");
    }
     
}
else if($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	$par = urldecode($_SERVER["QUERY_STRING"]);
    $query = explode('=',$par);
    $username = $query[1];//希望删除的username
    
    
    
    if($username!='')//username成功传入的话,选择数据库中username对应用户的信息并删除
    {		
	
		$sql = "select token from user where username = '$username'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);	
		$token_sql = $row[0];//这个是数据库里的username 的token值
		
		if($token_sql==$token_old)//验证token是否相同
		{	
			//用更新代替了删除，好像删除所有记录全没了，这里按需求设置nickname为ghost	
			$sql = "UPDATE user SET password = '' WHERE username = '$username'";
		    $result = mysql_query($sql);
		
			$sql = "UPDATE user SET nickname = 'ghost' WHERE username = '$username'";
			$result = mysql_query($sql);
			
			$sql = "UPDATE user SET realname = '' WHERE username = '$username'";
			$result = mysql_query($sql);
			
			$sql = "UPDATE user SET groupname = '' WHERE username = '$username'";
			$result = mysql_query($sql);
			
			$sql = "UPDATE user SET token = '' WHERE username = '$username'";
			$result = mysql_query($sql);
			
			$sql = "UPDATE user SET avatar = '' WHERE username = '$username'";
		    $result = mysql_query($sql);
		
			$sql = "UPDATE user SET bio = '' WHERE username = '$username'";
		    $result = mysql_query($sql);
			
			$sql = "UPDATE user SET username = '' WHERE username = '$username'";
			$result = mysql_query($sql);
			
		
			$arr = array("success"=>true);
    	}
		
		else
		{
			$arr = array("success"=>false,"message"=>"删除失败");
		}
    }	
    
    
    else//username未传入
    {
    	$arr = array("success"=>false,"message"=>"删除失败");
    }	
}
	
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);//以JSON格式提交给ajax
?>
