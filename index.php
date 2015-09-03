<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','index');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//读取xml文件
	$_html = _html(_get_xml('new.xml'));
	
	//读取帖子列表
	//设置分页参数
	global $_pagenum,$_pagesize,$_system;
	//第一个参数获取总条数，第二个参数指定每页多少条
	_page("select tg_id from tg_article where tg_reid=0",$_system['article']);
	//从数据库里提取数据，获取结果集
	$_result = _query("select 
							tg_id,tg_title,tg_type,tg_readcount,tg_commentcount 
						from 
							tg_article 
						where
							tg_reid=0
						order by 
							tg_date desc 
						limit 
							$_pagenum,$_pagesize");
							
	//最新图片，找到时间点最后上传的那张图片，并且是非公开的
	$_photo = _fetch_array("select 
								tg_id as id,
								tg_name as name,
								tg_url as url 
							from 
								tg_photo 
							where 
								tg_sid in (select tg_id from tg_dir where tg_type=0)
							order by 
								tg_date desc 
							limit 
								1");
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
      
      <div id="list">
      	<h2>帖子列表</h2>
            <a href="post.php" class="post">发表帖子</a>
            <ul class="article">
            	<?php 
				$_htmllist = array();
				while(!!$_rows = _fetch_array_list($_result)){
					$_htmllist['id'] = $_rows['tg_id'];
					$_htmllist['type'] = $_rows['tg_type'];
					$_htmllist['readcount'] = $_rows['tg_readcount'];
					$_htmllist['commentcount'] = $_rows['tg_commentcount'];
					$_htmllist['title'] = $_rows['tg_title'];
					$_htmllist = _html($_htmllist);
					echo '<li class="icon'.$_htmllist['type'].'"><em>阅读数(<strong>'.$_htmllist['readcount'].'</strong>) 评论数(<strong>'.$_htmllist['commentcount'].'</strong>)</em> <a href="article.php?id='.$_htmllist['id'].'">'._title($_htmllist['title'],20).'</a></li>';
				}
				_free_result($_result);
			
			?>            	
            </ul>
            <?php
            	//_padding()函数调用分页，1：表示数字分页，2：表示文本分页
			_paging(2);
		?>
      </div>
      
      <div id="user">
      	<h2>新进会员</h2>
            <dl>
            	<dd class="user"><?php echo $_html['username'];?>（<?php echo $_html['sex'];?>）</dd>
                  <dt><img src="<?php echo $_html['face'];?>" alt="<?php echo $_html['sex'];?>"/></dt>
                  <dd class="message"><a href="javascript:;" name="message" title="<?php echo $_html['id'];?>">发消息</a></dd>
                  <dd class="friend"><a href="javascript:;" name="friend" title="<?php echo $_html['id'];?>">加为好友</a></dd>
                  <dd class="guest">写留言</dd>
                  <dd class="flower"><a href="javascript:;" name="flower" title="<?php echo $_html['id'];?>">给他送花</a></dd>
                  <dd class="email">邮件：<a href="mailto:<?php echo $_html['email'];?>"><?php echo $_html['email'];?></a></dd>
                  <dd class="url">网址：<a href="<?php echo $_html['url'];?>" target="_blank"><?php echo $_html['url'];?></a></dd>
            </dl>
      </div>
      
      <div id="pics">
      	<h2>最新图片 -- <?php echo $_photo['name'];?></h2>
            <a href="photo_detail.php?id=<?php echo $_photo['id'];?>"><img src="thumb.php?filename=<?php echo $_photo['url'];?>&percent=0.4" alt="<?php echo $_photo['name'];?>"/></a>
      </div>
      
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>