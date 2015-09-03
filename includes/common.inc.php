<?php
	//防止恶意调用
	if(!defined('IN_TG')){
		exit('Access defined!');
	}
	
	//设置编码
	header('Content-Type:text/html;charset=utf-8');
	
	//转换硬路径常量
	define('ROOT_PATH',substr(dirname(__FILE__),0,-8));
	
	//创建一个自动转义状态的常量
	define('GPC',get_magic_quotes_gpc());
	
	//拒绝php低版本
	if(PHP_VERSION<'4.1.0'){
		exit('Version is too low');
	}
	
	//引入函数库
	require ROOT_PATH.'includes/global.func.php';
	require ROOT_PATH.'includes/mysql.func.php';
	
	//执行耗时
	define('START_TIME',_runtime());
	
	//数据库连接
	define('DB_USER','root');
	define('DB_PWD','');
	define('DB_HOST','localhost');
	define('DB_NAME','testguest');
	
	//创建数据库连接
	_connect();
	//选择一款数据库
	_select_db();
	//选择字符集
	_set_names();
	
	//短信提醒
	if(isset($_COOKIE['username'])){
		$_message = _fetch_array("select count(tg_id) as count from tg_message where tg_state=0 and tg_touser='{$_COOKIE['username']}'");
		if(empty($_message['count'])){
			$GLOBALS['message'] = '<strong class="noread"><a href="member_message.php">(0)</a></strong>';
		}else{
			$GLOBALS['message'] = '<strong class="read"><a href="member_message.php">('.$_message['count'].')</a></strong>';
		}
	}
	
	//网站系统设置初始化
	if(!!$_rows = _fetch_array("select 
									tg_webname,
									tg_article,
									tg_blog,
									tg_photo,
									tg_skin,
									tg_string,
									tg_post,
									tg_re,
									tg_code,
									tg_register
								from 
									tg_system 
								where 
									tg_id=1 
								limit 1"
																	)){
		$_system = array();
		$_system['webname'] = $_rows['tg_webname'];
		$_system['article'] = $_rows['tg_article'];		//文章列表每页显示的条数
		$_system['blog'] = $_rows['tg_blog'];		//博友每页显示的数目
		$_system['photo'] = $_rows['tg_photo'];		//相册每页显示的数目
		$_system['skin'] = $_rows['tg_skin'];		//皮肤
		$_system['post'] = $_rows['tg_post'];	//发帖限制时间
		$_system['re'] = $_rows['tg_re'];		//回帖限制时间
		$_system['code'] = $_rows['tg_code'];	//是否启用验证码
		$_system['register'] = $_rows['tg_register'];		//是否启用注册功能
		$_system['string'] = $_rows['tg_string'];			//非法字符
		$_system = _html($_system);
		
		//如果有skin的cookie，那么就替代系统数据库的皮肤
		if(isset($_COOKIE['skin'])){
			$_system['skin'] = $_COOKIE['skin'];
		}
	}else{
		exit('系统表异常，请管理员检查！');
	}
	
?>		





