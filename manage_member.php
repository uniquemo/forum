<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','manage_member');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//必须是管理员才能登陆
	_manage_login();
	
	//设置分页参数
	global $_pagenum,$_pagesize;
	//第一个参数获取总条数，第二个参数指定每页多少条
	_page("select tg_id from tg_user",15);
	//从数据库里提取数据，获取结果集
	$_result = _query("select tg_id,tg_username,tg_email,tg_reg_time from tg_user order by tg_reg_time desc limit $_pagenum,$_pagesize");
	
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
            	require ROOT_PATH."includes/manage.inc.php";
		?>
            <div id="member_main">
            	<h2>会员列表中心</h2>
                  <form method="post" action="?action=delete">
                  <table cellspacing="1">
                  	<tr><th>ID号</th><th>会员名</th><th>邮件</th><th>注册时间</th><th>操作</th></tr>
                        <?php 
					$_html = array();
					while(!!$_rows = _fetch_array_list($_result)){
						$_html['id'] = $_rows['tg_id'];
						$_html['username'] = $_rows['tg_username'];
						$_html['email'] = $_rows['tg_email'];
						$_html['reg_time'] = $_rows['tg_reg_time'];	
						$_html = _html($_html);	
				?>
                        <tr>
                        	<td><?php echo $_html['id'];?></td>
                              <td><?php echo $_html['username'];?></td>
                              <td><?php echo $_html['email'];?></td>
                              <td><?php echo $_html['reg_time'];?></td>
                              <td>[<a href="?action=del&id=<?php echo $_html['id']?>">删</a>] [<a href="?action=mod&id=<?php echo $_html['id']?>">修</a>]</td>
                        </tr>
                        <?php
					}
				?>
                  </table>
                  </form>
                  <?php
                  	_free_result($_result);
				_paging(2);
			?>
            </div>
      </div>
      
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>	