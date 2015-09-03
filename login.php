<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','login');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//登录状态
	_login_state();
	
	global $_system;
	
	//开始处理登录状态
	if(isset($_GET['action']) && $_GET['action'] == 'login'){
		//为了防止恶意注册，跨站攻击
            if(!empty($_system['code'])){
			_check_code($_POST['code'],$_SESSION['code']);
		}
		//引入验证文件
		include ROOT_PATH.'includes/login.func.php';
		//接收数据
		$_clean = array();
		$_clean['username'] = _check_username($_POST['username'],2,20);
		$_clean['password'] = _check_password($_POST['password'],6);
		$_clean['time'] = _check_time($_POST['time']);
		//print_r($_clean);
		//到数据库验证
		//用户名密码正确，且已经激活了账户的
		if(!!$_rows = _fetch_array("select tg_username,tg_uniqid,tg_level from tg_user where tg_username='{$_clean['username']}' and tg_password='{$_clean['password']}' and tg_active='' limit 1")){
			//登录成功后，记录登录信息
			//首先获取本机名
			$hostname=gethostbyaddr($_SERVER['REMOTE_ADDR']);
			//通过本机名获取Ip
			$ip = gethostbyname("$hostname");
			_query("update tg_user set 
										tg_last_time=NOW(),
										tg_last_ip='$ip',
										tg_login_count=tg_login_count+1
									where
										tg_username='{$_rows['tg_username']}'
									");
			
			//_session_destroy();	//清楚验证码的session
			_setcookie($_rows['tg_username'],$_rows['tg_uniqid'],$_clean['time']);
			if($_rows['tg_level'] == 1){
				$_SESSION['admin'] = $_rows['tg_username'];
			}
			_close();
			_location(null,'member.php');
		}else{
			_close();
			//_session_destroy();	//清楚验证码的session
			_location('用户名密码不正确或者该账户未被激活','login.php');
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/code.js"></script>
<script type="text/javascript" src="js/login.js"></script>
</head>

<body>

	<?php
      	require ROOT_PATH."includes/header.inc.php";
	?>
      
      <div id="login">
      	<h2>登录</h2>
            <form method="post" name="login" action="login.php?action=login">
            	<dl>
                  	<dt>  </dt>
                        <dd>用 户 名：<input type="text" name="username" class="text"/></dd>
                        <dd>密　　码：<input type="password" name="password" class="text"/></dd>
                        <dd>保　　留：<input type="radio" name="time" value="0" checked="checked"/> 不保留　<input type="radio" name="time" value="1" /> 一天　<input type="radio" name="time" value="2" /> 一周　<input type="radio" name="time" value="3" /> 一月</dd>
                  	<?php
                        	if(!empty($_system['code'])){
				?>
                        <dd>验 证 码：<input type="text" name="code" class="text code"/><img id="code" src="code.php" /></dd>
                        <?php
					}
				?>
                        <dd><input type="submit" value="登录" class="button"/><input type="button" value="注册" id="location" class="button location"/></dd>
                  
                  </dl>
            </form>
      </div>
	
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>