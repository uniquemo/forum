<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','member_message_detail');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//判断是否登录了
	if(!isset($_COOKIE['username'])){
		_alert_back('请先登录');
	}
	
	//删除短信模块
	if(isset($_GET['action']) && ($_GET['action'] == 'delete') && (isset($_GET['id']))){
		//验证短信是否合法
		if(!!$_rows = _fetch_array("select tg_id from tg_message where tg_id='{$_GET['id']}' limit 1")){
			//当你进行危险操作的时候，要对唯一标示符进行验证
			if(!!$_rows2 = _fetch_array("select tg_uniqid from tg_user where tg_username='{$_COOKIE['username']}' limit 1")){
				//为了防止cookie伪造，还要比对一下唯一标示符uniqid()
				_uniqid($_rows2['tg_uniqid'],$_COOKIE['uniqid']);
				//删除单条短信
				_query("delete from tg_message where tg_id='{$_GET['id']}' limit 1");
				if(_affected_rows() == 1){
					_close();
					_location('短信删除成功','member_message.php');
				}else{
					_close();
					_alert_back('短信删除失败');
				}
			}else{
				_alert_back('非法登录');
			}
		}else{
			_alert_back('此短信不存在');
		}
	}
	
	if(isset($_GET['id'])){
		//获取数据
		$_rows = _fetch_array("select tg_id,tg_state,tg_fromuser,tg_content,tg_date from tg_message where tg_id='{$_GET['id']}' limit 1");
		if($_rows){
			//将它的state状态设置为1即可
			if(empty($_rows['tg_state'])){	//未读
				_query("update tg_message set tg_state=1 where tg_id='{$_GET['id']}' limit 1");
				if(!_affected_rows()){
					_alert_back('异常');
				}
			}
			$_html = array();
			$_html['id'] = $_rows['tg_id'];
			$_html['fromuser'] = $_rows['tg_fromuser'];
			$_html['content'] = $_rows['tg_content'];
			$_html['date'] = $_rows['tg_date'];
			$_html = _html($_html);
		}else{
			_alert_back('此短信不存在');
		}
	}else{
		_alert_back('非法登录');
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/member_message_detail.js"></script>
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
            	<h2>短信详情中心</h2>
                  <dl>
                  	<dd>发 信 人：<?php echo $_html['fromuser'];?></dd>
                        <dd>内  容：<strong><?php echo $_html['content'];?></strong></dd>
                        <dd>发信时间：<?php echo $_html['date'];?></dd>
                        <dd class="button"><input type="button" value="返回列表" id="return" /><input id="delete" name="<?php echo $_html['id'];?>" type="button" value="删除短信" /></dd>
                  </dl>
            </div>
      </div>
      
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>	