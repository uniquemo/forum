<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','flower');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//判断是否登录了
	if(!isset($_COOKIE['username'])){
		_alert_close('请先登录');
	}
	//送花
	if(isset($_GET['action']) && ($_GET['action'] == 'send') && isset($_POST['code'])){
		_check_code($_POST['code'],$_SESSION['code']);
		//引入验证文件
		include ROOT_PATH.'includes/check.func.php';
		if(!!$_rows = _fetch_array("select tg_uniqid from tg_user where tg_username='{$_COOKIE['username']}' limit 1")){
			//为了防止cookie伪造，还要比对一下唯一标示符uniqid()
			_uniqid($_rows['tg_uniqid'],$_COOKIE['uniqid']);
			$_clean = array();
			$_clean['touser'] = $_POST['touser'];
			$_clean['fromuser'] = $_COOKIE['username'];
			$_clean['flower'] = $_POST['flower'];
			$_clean['content'] = _check_content($_POST['content']);
			$_clean = _mysql_string($_clean);
			//写入表
			_query("insert into tg_flower(
											tg_touser,
											tg_fromuser,
											tg_flower,
											tg_content,
											tg_date
										) 
									values(
											'{$_clean['touser']}',
											'{$_clean['fromuser']}',
											'{$_clean['flower']}',
											'{$_clean['content']}',
											NOW()
										)"
								);
			if(_affected_rows() == 1){
				_close();
				//_session_destroy();
				_alert_close('送花成功');
			}else{
				_close();
				//_session_destroy();
				_alert_back('送花失败');
			}
		}else{
			_alert_close('非法登录');
		}
	}
	//获取数据
	if(isset($_GET['id'])){
		if(!!$_rows = _fetch_array("select tg_username from tg_user where tg_id='{$_GET['id']}' limit 1")){
			$_html = array();
			$_html['touser'] = $_rows['tg_username'];
			$_html = _html($_html);
		}else{
			_alert_close('不存在此用户');
		}
	}else{
		_alert_close('非法操作');
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
<script type="text/javascript" src="js/message.js"></script>
</head>

<body>
      
      <div id="message">
      	<h3>送花</h3>
            <form method="post" action="?action=send">
            <input type="hidden" name="touser" value="<?php echo $_html['touser'];?>"/>
            <dl>
            	<dd>
                  	<input type="text" readonly="readonly" class="text" value="To：<?php echo $_html['touser'];?>"/>
                        <select name="flower">
                        	<?php 
						foreach(range(1,100) as $_num){
							echo '<option value="'.$_num.'"> x'.$_num.'朵</option>';
						}
					?>
                        </select>
                  </dd>
                  <dd><textarea name="content">灰常欣赏你，送你花啦~~~</textarea></dd>
                  <dd>验 证 码：<input type="text" name="code" class="text yzm"/><img id="code" src="code.php" /><input type="submit" class="submit" value="送花"/></dd>
            </dl>
            </form>
      </div>

</body>
</html>