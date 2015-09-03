<?php
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','article_modify');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	if(isset($_SERVER['HTTP_REFERER'])){
		$_skinurl = $_SERVER['HTTP_REFERER'];
	}
	//必须从上一页点击过来，而且必须有id
	if(empty($_skinurl) || !isset($_GET['id'])){
		_alert_back('非法操作');
	}else{
		//最好判断一下Id必须是1,2,3中的一个
		//生成一个cookie，用来保存皮肤的种类
		setcookie('skin',$_GET['id']);
		_location(null,$_skinurl);
	}
	
?>