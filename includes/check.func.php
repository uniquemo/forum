<?php

	//防止恶意调用
	if(!defined('IN_TG')){
		exit('Access defined!');
	}
	
	if(!function_exists('_alert_back')){
		exit('_alert_back()函数不存在，请检查');
	}
	
	if(!function_exists('_mysql_string')){
		exit('_mysql_string()函数不存在，请检查');
	}
	
	/**
	*检测唯一标识符
	*/
	function _check_uniqid($_first_uniqid,$_end_uniqid){
		if((strlen($_first_uniqid) != 40) || ($_first_uniqid != $_end_uniqid)){
			_alert_back('唯一标识符异常');
		}
		
		return _mysql_string($_first_uniqid);
	}
	
	/**
	*_check_username()  表示检测并过滤用户名
	*@access public 
	*@param string $_string  受污染的用户名
	*@param int $_min_num  最小位数
	*@param int $_max_num  最大位数
	*@return string  过滤后的用户名
	*/
	function _check_username($_string,$_min_num=2,$_max_num=20){
		global $_system;
		//去掉两边的空格
		$_string = trim($_string);
		
		//长度小于2位，或者大于20位都不行
		if(mb_strlen($_string,'utf-8')<$_min_num || mb_strlen($_string,'utf-8')>$_max_num){
			_alert_back('用户名长度不能小于'.$_min_num.'位或大于'.$_max_num.'位');
		}
		
		//限制敏感字符
		$_char_pattern = '/[<>\'\" ]/';
		if(preg_match($_char_pattern,$_string)){
			_alert_back('用户名不得包含敏感字符');
		}
		
		//限制敏感用户名
		$_mg = explode('|',$_system['string']);
		//告诉用户，有哪些不能注册
		$_mg_string = '';
		foreach($_mg as $value){
			$_mg_string .= '['.$value.']'.'\n';
		}
		//这里采用绝对匹配
		if(in_array($_string,$_mg)){
			_alert_back($_mg_string.'敏感用户名不得注册');
		}
		
		//将用户名转义输入
		return _mysql_string($_string);
	}
	
	/**
	*_check_password()  表示检测密码
	*@access public 
	*@param string $_first_pass  
	*@param string $_end_pass 
	*@param int $_min_num 
	*@return string  返回加密后的密码
	*/
	function _check_password($_first_pass,$_end_pass,$_min_num=6){
		//判断密码
		if(strlen($_first_pass)<$_min_num){
			_alert_back('密码不得小于'.$_min_num.'位');
		}
		
		//密码和密码确认必须一致
		if($_first_pass != $_end_pass){
			_alert_back('密码和确认密码不一致');
		}
		
		//将密码返回
		return _mysql_string(sha1($_first_pass));
	}
	
	function _check_modify_password($_string,$_min_num=6){
		if(!empty($_string)){
			if(strlen($_string)<$_min_num){
				_alert_back('密码不得小于'.$_min_num.'位');
			}
		}else{
			return null;
		}
		return sha1($_string);
	}

	/**
	*_check_question()  表示检测密码提示
	*@access public 
	*@param string $_string
	*@param int $_min_num
	*@param int $_max_num
	*@return $_string  返回密码提示
	*/
	function _check_question($_string,$_min_num=2,$_max_num=20){
		$_string = trim($_string);
		//长度小于4位，或者大于20位都不行
		if(mb_strlen($_string,'utf-8')<$_min_num || mb_strlen($_string,'utf-8')>$_max_num){
			_alert_back('密码提示不能小于'.$_min_num.'位或大于'.$_max_num.'位');
		}
		
		return _mysql_string($_string);
	}
	
	/**
	*用于检测密码回答
	*/
	function _check_answer($_ques,$_answ,$_min_num=2,$_max_num=20){
		$_answ = trim($_answ);
		//长度小于4位，或者大于20位都不行
		if(mb_strlen($_answ,'utf-8')<$_min_num || mb_strlen($_answ,'utf-8')>$_max_num){
			_alert_back('密码回答不能小于'.$_min_num.'位或大于'.$_max_num.'位');
		}
		
		//密码提示与回答不能一致
		if($_ques == $_answ){
			_alert_back('密码提示与回答不能相同');
		}
		
		//加密返回
		return _mysql_string(sha1($_answ));
	}
	
	/**
	*检测性别
	*/
	function _check_sex($_string){
		return _mysql_string($_string);
	}
	
	/**
	*检测头像
	*/
	function _check_face($_string){
		return _mysql_string($_string);
	}

	/**
	*用于检测邮箱是否合法
	*/
	function _check_email($_string,$_min_num=5,$_max_num=40){
		//参考bnbbs@163.com
		if(!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/',$_string)){
			_alert_back('邮件格式不正确');
		}
		if(strlen($_string)<$_min_num || strlen($_string)>$_max_num){
			_alert_back('邮件长度不合法');
		}
		
		return _mysql_string($_string);
	}
	
	/**
	*用于检测qq是否合法
	*/
	function _check_qq($_string){
		if(empty($_string)){
			return null;
		}else{
			if(!preg_match('/^[1-9]{1}[0-9]{4,9}$/',$_string)){
				_alert_back('qq号码不正确');
			}
		}
		
		return _mysql_string($_string);
	}
	
	/**
	*用于检测url是否合法
	*/
	function _check_url($_string,$_max_num=40){
		if(empty($_string) || ($_string == 'http://')){
			return null;
		}else{
			if(!preg_match('/^https?:\/\/(\w+\.)?[\w\-\.]+(\.\w+)+$/',$_string)){
				_alert_back('url格式不正确');
			}
			if(strlen($_string)>$_max_num){
				_alert_back('网址太长');
			}
		}
		
		return _mysql_string($_string);
	}
	
	function _check_content($_string){
		if(mb_strlen($_string,'utf-8')<10 || mb_strlen($_string,'utf-8')>200){
			_alert_back('短信内容不得小于10位或者大于200位');
		}
		return $_string;
	}
	
	function _check_post_title($_string,$_min=2,$_max=40){
		if(mb_strlen($_string,'utf-8')<$_min || mb_strlen($_string,'utf-8')>$_max){
			_alert_back('title不得小于'.$_min.'位或者大于'.$_max.'位');
		}
		return $_string;
	}
	
	function _check_post_content($_string,$_num=10){
		if(mb_strlen($_string,'utf-8')<$_num){
			_alert_back('content不得小于'.$_num.'位');
		}
		return $_string;
	}
	
	function _check_autograph($_string,$_num=200){
		if(mb_strlen($_string,'utf-8')>$_num){
			_alert_back('autograph不得大于'.$_num.'位');
		}
		return $_string;
	}
	
	function _check_dir_name($_string,$_min=2,$_max=20){
		//去掉两边的空格
		$_string = trim($_string);
		
		//长度小于2位，或者大于20位都不行
		if(mb_strlen($_string,'utf-8')<$_min || mb_strlen($_string,'utf-8')>$_max){
			_alert_back('名称长度不能小于'.$_min.'位或大于'.$_max.'位');
		}
		return $_string;
	}
	
	function _check_dir_password($_string,$_min_num=6){
		if(strlen($_string)<$_min_num){
			_alert_back('密码不得小于'.$_min_num.'位');
		}
		return sha1($_string);
	}
	
	function _check_photo_url($_string){
		if(empty($_string)){
			_alert_back('地址不能为空');
		}
		return $_string;
	}
	
?>