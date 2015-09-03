// JavaScript Document
window.onload = function(){
	code();
	
	var fm = document.getElementsByTagName('form')[0];
	
	fm.onsubmit = function(){
		//验证码
		if(fm.code.value.length != 4){
			alert('验证码必须4位');
			fm.code.value = '';	//清空
			fm.code.focus();	//将焦点移至表单字段
			return false;
		}
		if(fm.content.value.length<10 || fm.content.value.length>200){
			alert('短信内容不得小于10位或者大于200位');
			fm.content.focus();	//将焦点移至表单字段
			return false;
		}
		return true;
	};
};