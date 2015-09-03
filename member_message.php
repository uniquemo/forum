<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','member_message');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//判断是否登录了
	if(!isset($_COOKIE['username'])){
		_alert_back('请先登录');
	}
	
	//批删除短信
	if(isset($_GET['action']) && ($_GET['action'] == 'delete') && isset($_POST['ids'])){
		$_clean = array();
		$_clean['ids'] = _mysql_string(implode(',',$_POST['ids']));
		if(!!$_rows2 = _fetch_array("select tg_uniqid from tg_user where tg_username='{$_COOKIE['username']}' limit 1")){
			//为了防止cookie伪造，还要比对一下唯一标示符uniqid()
			_uniqid($_rows2['tg_uniqid'],$_COOKIE['uniqid']);
			//批删除
			_query("delete from tg_message where tg_id in ({$_clean['ids']})");
			if(_affected_rows()){
				_close();
				_location('短信删除成功','member_message.php');
			}else{
				_close();
				_alert_back('短信删除失败');
			}
		}else{
			_alert_back('非法登录');
		}
	}
	
	//设置分页参数
	global $_pagenum,$_pagesize;
	//第一个参数获取总条数，第二个参数指定每页多少条
	_page("select tg_id from tg_message where tg_touser='{$_COOKIE['username']}'",10);
	
	//从数据库里提取数据，获取结果集
	$_result = _query("select tg_id,tg_fromuser,tg_content,tg_state,tg_date from tg_message where tg_touser='{$_COOKIE['username']}' order by tg_date desc limit $_pagenum,$_pagesize");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/member_message.js"></script>
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
            	<h2>短信管理中心</h2>
                  <form method="post" action="?action=delete">
                  <table cellspacing="1">
                  	<tr><th>发信人</th><th>短信内容</th><th>时间</th><th>状态</th><th>操作</th></tr>
                        <?php 
					$_html = array();
					while(!!$_rows = _fetch_array_list($_result)){
						$_html['id'] = $_rows['tg_id'];
						$_html['fromuser'] = $_rows['tg_fromuser'];
						$_html['content'] = $_rows['tg_content'];
						$_html['date'] = $_rows['tg_date'];
						$_html = _html($_html);
						if(empty($_rows['tg_state'])){
							$_html['state'] = '<img src="images/read.gif" alt="未读" title="未读">';
							$_html['content_html'] = '<strong>'._title($_html['content'],14).'</strong>';
						}else{
							$_html['state'] = '<img src="images/noread.gif" alt="已读" title="已读">';
							$_html['content_html'] = _title($_html['content'],14);
						}
				?>
                        <tr>
                        	<td><?php echo $_html['fromuser'];?></td>
                              <td><a href="member_message_detail.php?id=<?php echo $_html['id'];?>" title="<?php echo $_html['content'];?>"><?php echo $_html['content_html'];?></a></td>
                              <td><?php echo $_html['date'];?></td>
                              <td><?php echo $_html['state'];?></td>
                              <td><input name="ids[]" value="<?php echo $_html['id'];?>" type="checkbox"/></td>
                        </tr>
				<?php }
					_free_result($_result);
				?>
                        <tr><td colspan="5"><label for="all">全选<input type="checkbox" name="chkall" id="all"/></label><input type="submit" value="批删除"/></td></tr>
                  </table>
                  </form>
                  <?php 
				//_padding()函数调用分页，1：表示数字分页，2：表示文本分页
				_paging(2);
			?>
            </div>
      </div>
      
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>	