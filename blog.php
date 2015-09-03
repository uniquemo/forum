<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','blog');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//设置分页参数
	global $_pagenum,$_pagesize,$_system;
	//第一个参数获取总条数，第二个参数指定每页多少条
	_page("select tg_id from tg_user",$_system['blog']);
	
	//从数据库里提取数据，获取结果集
	$_result = _query("select tg_id,tg_username,tg_sex,tg_face from tg_user order by tg_reg_time desc limit $_pagenum,$_pagesize");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/blog.js"></script>
</head>

<body>

	<?php
      	require ROOT_PATH."includes/header.inc.php";
	?>
      
      <div id="blog">
      	<h2>博友列表</h2>
            <?php 
			$_html = array();
			while(!!$_rows = _fetch_array_list($_result)){
				$_html['id'] = $_rows['tg_id'];
				$_html['username'] = $_rows['tg_username'];
				$_html['sex'] = $_rows['tg_sex'];
				$_html['face'] = $_rows['tg_face'];	
				$_html = _html($_html);	
		?>
            <dl>
            	<dd class="user"><?php echo $_html['username']?>（<?php echo $_html['sex']?>）</dd>
                  <dt><img src="<?php echo $_html['face'];?>" alt="momo"/></dt>
                  <dd class="message"><a href="javascript:;" name="message" title="<?php echo $_html['id']?>">发消息</a></dd>
                  <dd class="friend"><a href="javascript:;" name="friend" title="<?php echo $_html['id']?>">加为好友</a></dd>
                  <dd class="guest">写留言</dd>
                  <dd class="flower"><a href="javascript:;" name="flower" title="<?php echo $_html['id']?>">给他送花</a></dd>
            </dl>
            <?php }
			_free_result($_result);
			//_padding()函数调用分页，1：表示数字分页，2：表示文本分页
			_paging(2);
		?>
            
      </div>
	
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>	