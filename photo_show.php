<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','photo_show');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//删除相片
	if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])){
		if(!!$_rows = _fetch_array("select tg_uniqid,tg_article_time from tg_user where tg_username='{$_COOKIE['username']}' limit 1")){
			
			//为了防止cookie伪造，还要比对一下唯一标示符uniqid()
			_uniqid($_rows['tg_uniqid'],$_COOKIE['uniqid']);
			//取得这张图片的发布者
			if(!!$_rows = _fetch_array("select tg_username,tg_url,tg_id,tg_sid from tg_photo where tg_id='{$_GET['id']}' limit 1")){
				$_html = array();
				$_html['id'] = $_rows['tg_id'];
				$_html['sid'] = $_rows['tg_sid'];
				$_html['username'] = $_rows['tg_username'];
				$_html['url'] = $_rows['tg_url'];
				$_html = _html($_html);
				//进行图片的删除，判断删除图片的身份是否合法
				if((isset($_COOKIE['username']) && $_html['username'] == $_COOKIE['username']) || isset($_SESSION['admin'])){
					//首先删除图片的数据库信息
					_query("delete from tg_photo where tg_id='{$_html['id']}'");
					if(_affected_rows() == 1){
						//删除图片的物理地址
						if(file_exists($_html['url'])){
							unlink($_html['url']);
						}else{
							_alert_back('磁盘已不存在此图');
						}
						_close();
						_location('删除图片数据库信息成功','photo_show.php?id='.$_html['sid']);
					}else{
						_close();
						_alert_back('删除图片数据库信息失败');	
					}
				}else{
					_alert_back('无权删除');
				}
			}else{
				_alert_back('不存在此图片');
			}
		}else{
			_alert_back('非法登录');
		}
	}
	
	//取值
	if(isset($_GET['id'])){
		if(!!$_rows = _fetch_array("select
										tg_id,tg_name,tg_type
									from
										tg_dir
									where
										tg_id='{$_GET['id']}'
									limit
										1
										")){
			$_dirhtml = array();
			$_dirhtml['id'] = $_rows['tg_id'];
			$_dirhtml['name'] = $_rows['tg_name'];
			$_dirhtml['type'] = $_rows['tg_type'];
			$_dirhtml = _html($_dirhtml);
			
			//对比加密相册的验证信息
			if(isset($_POST['password'])){
				if(!!$_rows = _fetch_array("select
												tg_id
											from
												tg_dir
											where
												tg_password='".sha1($_POST['password'])."'
											limit
												1
										")){
					//验证通过
					//生成cookie
					setcookie('photo'.$_dirhtml['id'],$_dirhtml['name']);
					//重定向
					_location(null,'photo_show.php?id='.$_dirhtml['id']);
				}else{
					_alert_back('相册密码不正确');
				}
			}
		}else{
			_alert_back('不存在此相册');
		}
	}else{
		_alert_back('非法操作');
	}
	
	global $_pagesize,$_pagenum,$_system,$_id;
	$_id = 'id='.$_dirhtml['id'].'&';
	$_percent = 0.3;
	_page("SELECT tg_id FROM tg_photo WHERE tg_sid='{$_dirhtml['id']}'",$_system['photo']); 
	$_result = _query("SELECT 
								tg_id,tg_username,tg_name,tg_url,tg_readcount,tg_commentcount 
						FROM 
								tg_photo 
						WHERE
								tg_sid='{$_dirhtml['id']}'
						ORDER BY 
								tg_date DESC 
						LIMIT 
								$_pagenum,$_pagesize
							");		
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
      	<h2><?php echo $_dirhtml['name'];?></h2>   
            
            
            <?php 
		//判断相册是否需要密码
		if(empty($_dirhtml['type']) || 
						(isset($_COOKIE['photo'.$_dirhtml['id']]) && $_COOKIE['photo'.$_dirhtml['id']] == $_dirhtml['name']) ||
						(isset($_SESSION['admin']))){		
			$_html = array();
			while (!!$_rows = _fetch_array_list($_result)) {
				$_html['id'] = $_rows['tg_id'];
				$_html['username'] = $_rows['tg_username'];
				$_html['name'] = $_rows['tg_name'];
				$_html['url'] = $_rows['tg_url'];
				$_html['readcount'] = $_rows['tg_readcount'];
				$_html['commentcount'] = $_rows['tg_commentcount'];
				$_html = _html($_html);
		?>
            <dl>
            	<dt><a href="photo_detail.php?id=<?php echo $_html['id'];?>"><img src="thumb.php?filename=<?php echo $_html['url'];?>&percent=<?php echo $_percent;?>" /></a></dt>
                  <dd><a href="photo_detail.php?id=<?php echo $_html['id'];?>"><?php echo $_html['name'];?></a></dd>
                  <dd>阅读量(<strong><?php echo $_html['readcount'];?></strong>) 评论(<strong><?php echo $_html['commentcount'];?></strong>) 上传者：<?php echo $_html['username'];?></dd>
            	<?php
                  	//只有图片的上传者和管理员才能看到
				if((isset($_COOKIE['username']) && $_html['username'] == $_COOKIE['username']) || isset($_SESSION['admin'])){
			?>
                  <dd>[<a href="photo_show.php?action=delete&id=<?php echo $_html['id'];?>">删除</a>]</dd>
                  <?php }?>
            </dl>
            <?php }
			 _free_result($_result);
			 _paging(1);
		?>
            
            
            <p><a href="photo_add_img.php?id=<?php echo $_dirhtml['id'];?>">上传图片</a></p>
            
            <?php
		}else{
			echo '<form method="post" action="photo_show.php?id='.$_dirhtml['id'].'">';
			echo '<p>请输入密码：<input type="password" name="password"/> <input type="submit" value="确认"/></p>';
			echo '</form>';
		}
		?>
      </div>
	
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>	