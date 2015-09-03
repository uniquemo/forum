<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','photo');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//删除相册目录
	if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])){
		if(!!$_rows = _fetch_array("select tg_uniqid from tg_user where tg_username='{$_COOKIE['username']}' limit 1")){
			
			//为了防止cookie伪造，还要比对一下唯一标示符uniqid()
			_uniqid($_rows['tg_uniqid'],$_COOKIE['uniqid']);
			//删除目录
			//取得这张图片的发布者
			if(!!$_rows = _fetch_array("select tg_dir from tg_dir where tg_id='{$_GET['id']}' limit 1")){
				$_html = array();
				$_html['url'] = $_rows['tg_dir'];
				$_html = _html($_html);	
				//3、判断目录在磁盘中是否存在
				if(file_exists($_html['url'])){
					if(_remove_Dir($_html['url'])){
						//1、删除目录里的数据库图片
						_query("delete from tg_photo where tg_sid='{$_GET['id']}'");
						//2、删除目录的数据库
						_query("delete from tg_dir where tg_id='{$_GET['id']}'");
						_close();
						_location('目录删除成功','photo.php');
					}else{
						_close();
						_alert_back('目录删除失败');
					}
				}
			}else{
				_alert_back('不存在此目录');
			}
		}else{
			_alert_back('非法登录');
		}
	}
	
	//读取数据
	//设置分页参数
	global $_pagenum,$_pagesize,$_system;
	//第一个参数获取总条数，第二个参数指定每页多少条
	_page("select tg_id from tg_dir",$_system['photo']);
	
	//从数据库里提取数据，获取结果集
	$_result = _query("select tg_id,tg_name,tg_type,tg_face from tg_dir order by tg_date desc limit $_pagenum,$_pagesize");
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
      
      <div id="photo">
      	<h2>相册列表</h2>
            <?php 
			$_html = array();
			while(!!$_rows = _fetch_array_list($_result)){
				$_html['id'] = $_rows['tg_id'];
				$_html['name'] = $_rows['tg_name'];
				$_html['type'] = $_rows['tg_type'];
				$_html['face'] = $_rows['tg_face'];
				$_html = _html($_html);	
				if(empty($_html['type'])){
					$_html['type_html'] = '(公开)';
				}else{
					$_html['type_html'] = '(私密)';
				}
				if(empty($_html['face'])){
					$_html['face_html'] = '';
				}else{
					$_html['face_html'] = '<img src="'.$_html['face'].'" alt="'.$_html['name'].'"/>';
				}
				//统计相册里的照片数量
				$_html['photo'] = _fetch_array("select count(*) as count from tg_photo where tg_sid='{$_html['id']}'");
		?>
            <dl>
            	<dt><a href="photo_show.php?id=<?php echo $_html['id'];?>"><?php echo $_html['face_html'];?></a></dt>
                  <dd><a href="photo_show.php?id=<?php echo $_html['id'];?>"><?php echo $_html['name'];?> <?php echo '['.$_html['photo']['count'].']'.$_html['type_html'];?></a></dd>
                  <?php 
				if(isset($_SESSION['admin']) && isset($_COOKIE['username'])){
			?>
                  <dd>[<a href="photo_modify_dir.php?id=<?php echo $_html['id'];?>">修改</a>][<a href="photo.php?action=delete&id=<?php echo $_html['id'];?>">删除</a>]</dd>
                  <?php 
				}
			?>
            </dl>
            <?php }?>
            <?php 
			if(isset($_SESSION['admin']) && isset($_COOKIE['username'])){
		?>
            <p><a href="photo_add_dir.php">添加目录</a></p>
            <?php 
			}
		?>
      </div>
	
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>	