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
		
		
			$username = $params["username"];  
			$psw = $params["password"];
				
			
			mysql_connect("123.207.25.59","ncuhome","hackweek");  
			mysql_select_db("test");  
			mysql_query("set names 'utf8'");
	
			
			//从数据库取出手机号和密码做判断
			$sql = "select username,password from user where username = '$params[username]' and password = '$params[password]'";  
			$result = mysql_query($sql);  
			$num = mysql_num_rows($result); 
			
			
			$sql_token = "select token,user_id from user where username = '$params[username]'";		
			$result_token = mysql_query($sql_token);  
			$row = mysql_fetch_array($result_token);//从数据库取出原有的token和user_id
			$token_old = $row[0];
			$user_id = $row[1];
			
			
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
							$token_new = $unfinished_token."&".$enstring;//新的token
							
							
							$result = mysql_query("UPDATE user SET token = '$token_new' WHERE username = '$params[username]' and password = '$params[password]'");  
							$num = mysql_num_rows($result);
							
							
							if($num&&$row)//数据库更新token成功后响应
							{
								$arr = array("success"=>true,"id"=>$user_id,"token"=>$token_new);
							}
	
	
							else
							{
								$arr = array("success"=>false,"message"=>"请求失败");
							}	
							
							
						}
						
						
						else
						{
							$arr = array("success"=>true,"id"=>$user_id,"token"=>$token_old);
						}	
						
						
					
	
				
			}  
			else 
			{  
				$arr = array("success"=>false,"message"=>"用户名或密码错误！");
			}  
          
		}
  
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);//以JSON格式提交给ajax
?>
