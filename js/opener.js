// JavaScript Document

window.onload = function(){
	var img = document.getElementsByTagName('img');
	for(var i=0;i<img.length;i++){
		img[i].onclick = function(){
			_opener(this.alt);
		}
	}
};
function _opener(src){
	//opener表示父窗口
	opener.document.getElementById('faceimg').src = src;
	opener.document.register.face.value = src;
}