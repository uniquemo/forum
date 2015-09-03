// JavaScript Document
window.onload = function(){
	var fm = document.getElementsByTagName('form')[0];
	var pass = document.getElementById('pass');
	
	fm[1].onclick = function(){
		pass.style.display = 'none';
	};
	fm[2].onclick = function(){
		pass.style.display = 'block';
	};
	
	fm.onsubmit = function(){
		if(fm.name.value.length<2 || fm.name.value.length>20){
			alert('相册目录名不得小于2位或者大于20位');
			fm.name.value = '';	//清空
			fm.name.focus();	//将焦点移至表单字段
			return false;
		}
		if(fm[2].checked){
			//私密选中，这个时候才需要判断密码
			if(fm.password.value.length<6){
				alert('相册密码不得小于6位');
				fm.password.value = '';	//清空
				fm.password.focus();	//将焦点移至表单字段
				return false;
			}
		}
		return true;
	};
};