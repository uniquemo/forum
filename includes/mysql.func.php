<?php

	//防止恶意调用
	if(!defined('IN_TG')){
		exit('Access defined!');
	}

	/**
	*连接数据库
	*/
	function _connect(){
		//表示全局变量，意图是将此变量在函数外部也能访问
		global $_conn;
		//创建数据库连接
		if(!$_conn = @mysql_connect(DB_HOST,DB_USER,DB_PWD)){
			exit('数据库连接失败');
		}
	}
	
	/**
	*选择一款数据库
	*/
	function _select_db(){
		if(!mysql_select_db(DB_NAME)){
			exit('指定数据库找不到');
		}
	}
	
	/**
	*设置字符集
	*/
	function _set_names(){
		if(!mysql_query('SET NAMES UTF8')){
			exit('字符集错误');
		}
	}
	
	/**
	*执行sql语句，并返回结果集
	*/
	function _query($_sql){
		if(!($_result = mysql_query($_sql))){
			exit('sql执行失败'.mysql_error());
		}
		return $_result;
	}
	
	/**
	*将结果集以数组形式返回，只能获取一条数据
	*/
	function _fetch_array($_sql){
		return mysql_fetch_array(_query($_sql),MYSQL_ASSOC);
	}
	
	/**
	*可以返回指定数据集的所有数据
	*/
	function _fetch_array_list($_result){
		return mysql_fetch_array($_result,MYSQL_ASSOC);
	}
	
	/**
	*返回符合条件的条目数量
	*/
	function _num_rows($_result){
		return mysql_num_rows($_result);
	}
	
	/**
	*返回影响的记录数
	*/
	function _affected_rows(){
		return mysql_affected_rows();
	}
	
	/**
	*销毁结果集
	*/
	function _free_result($_result){
		mysql_free_result($_result);
	}
	
	/**
	*获取刚刚新增的Id
	*/
	function _insert_id(){
		return mysql_insert_id();
	}

	/**
	*判断新增的用户是否已经有了
	*/
	function _is_repeat($_sql,$_info){
		if(_fetch_array($_sql)){
			_alert_back($_info);
		}
	}
	
	function _close(){
		if(!mysql_close()){
			exit('关闭异常');
		}
	}

?>