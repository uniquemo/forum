<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','member');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//是否正常登陆
	if(isset($_COOKIE['username'])){
		//获取数据
		$_rows = _fetch_array("select tg_username,tg_sex,tg_face,tg_email,tg_url,tg_qq,tg_level,tg_reg_time from tg_user where tg_username='{$_COOKIE['username']}' limit 1");
		if($_rows){
			$_html = array();
			$_html['username'] = $_rows['tg_username'];
			$_html['sex'] = $_rows['tg_sex'];
			$_html['face'] = $_rows['tg_face'];
			$_html['email'] = $_rows['tg_email'];
			$_html['url'] = $_rows['tg_url'];
			$_html['qq'] = $_rows['tg_qq'];
			$_html['reg_time'] = $_rows['tg_reg_time'];
			switch($_rows['tg_level']){
				case 0:
					$_html['level'] = '普通会员';
					break;
				case 1:
					$_html['level'] = '管理员';
					break;
				default:
					$_html['level'] = '出错了';
			}
			$_html = _html($_html);
		}else{
			_alert_back('此用户不存在');
		}
	}else{
		_alert_back('非法登陆');
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
	require ROOT_PATH.'includes/title.inc.php';
?>
</head>

<body>

	<?php
      	require ROOT_PATH."includes/header.inc.php";
	?>
      
      <div id="member">
      	<?php
            	require ROOT_PATH."includes/member.inc.php";
		?>
            <div id="member_main">
            	<h2>会员管理中心</h2>
                  <dl>
                  	<dd>用 户 名：<?php echo $_html['username'];?></dd>
                        <dd>性  别：<?php echo $_html['sex'];?></dd>
                        <dd>头  像：<?php echo $_html['face'];?></dd>
                        <dd>电子邮件：<?php echo $_html['email'];?></dd>
                        <dd>主  页：<?php echo $_html['url'];?></dd>
                        <dd>Q  Q：<?php echo $_html['qq'];?></dd>
                        <dd>注册时间：<?php echo $_html['reg_time'];?></dd>
                        <dd>身  份：<?php echo $_html['level'];?></dd>
                  </dl>
            </div>
      </div>
	
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>	