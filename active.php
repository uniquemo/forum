<?php
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','active');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//如果直接进入则返回错误，只能通过注册进入
	if(!isset($_GET['active'])){
		_alert_back('非法操作');
	}
	//开始激活处理
	if(isset($_GET['action']) && isset($_GET['active']) && $_GET['action'] == 'ok'){
		$_active = _mysql_string($_GET['active']);
		if(_fetch_array("select tg_active from tg_user where tg_active='$_active' limit 1")){
			//将tg_active设置为空
			_query("update tg_user set tg_active=NULL where tg_active='$_active' limit 1");
			if(_affected_rows() == 1){
				_close();
				_location('账户激活成功','login.php');
			}else{
				_close();
				_location('账户激活失败','register.php');
			}
		}else{
			_alert_back('非法操作');
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
</head>

<body>

	<?php
      	require ROOT_PATH."includes/header.inc.php";
	?>
      
      <div id="active">
      	<h2>激活账户</h2>
            <p>本页面是为了模拟您的邮件的功能，点击以下超链接激活您的账户</p>
            <p><a href="active.php?action=ok&amp;active=<?php if(isset($_GET['active'])) echo $_GET['active']?>"><?php echo 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]?>active.php?action=ok&amp;active=<?php if(isset($_GET['active'])) echo $_GET['active']?></a></p>
      </div>
      
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>
	
</body>
</html>