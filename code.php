<?php
	session_start();
	
	/*//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//运行验证码函数
	//默认验证码大小为：75*25，默认位数是4位，如果要6位，长度推荐125，如果要8位，推荐150
	//第4个参数，是否要边框，要的话true，默认是false
	//可以通过数据库的方法，来设置验证码的各种属性
	_code(125,25,6,false);*/
	
	
	
		//随机码的个数
		$_rnd_num = 4;
		
		//创建随机码
		$_nmsg = '';
		for($i=0;$i<$_rnd_num;$i++){
			$_nmsg .= dechex(mt_rand(0,15));
		}
		
		//保存在session里
		$_SESSION['code'] = $_nmsg;
		
		//长和高
		$_width = 75;
		$_height = 25;
		
		//创建一张图像
		$_img = imagecreatetruecolor($_width,$_height);
		
		//白色
		$_white = imagecolorallocate($_img,255,255,255);
		//填充
		imagefill($_img,0,0,$_white);
		
		$_flag = false;
		
		$_black = '';
		if($_flag){
			//黑色边框
			$_black = imagecolorallocate($_img,0,0,0);
			imagerectangle($_img,0,0,$_width-1,$_height-1,$_black);
		}
		
		//随机画出6个线条
		for($i=0;$i<6;$i++){
			$_rnd_color = imagecolorallocate($_img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
			imageline($_img,mt_rand(0,$_width),mt_rand(0,$_height),mt_rand(0,$_width),mt_rand(0,$_height),$_rnd_color);
		}
		
		//随机雪花
		for($i=0;$i<100;$i++){
			$_rnd_color = imagecolorallocate($_img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
			imagestring($_img,1,mt_rand(1,$_width),mt_rand(1,$_height),'*',$_rnd_color);
		}
		
		//输出验证码
		for($i=0;$i<strlen($_SESSION['code']);$i++){
			$_rnd_color = imagecolorallocate($_img,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200));
			imagestring($_img,5,$i*$_width/$_rnd_num+mt_rand(1,10),mt_rand(1,$_height/2),$_SESSION['code'][$i],$_rnd_color);
		}
		
		//输出图像
		header('Content-Type:image/png');
		imagepng($_img);
		imagedestroy($_img);
?>