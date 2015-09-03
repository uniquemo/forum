<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','member_modify');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//修改资料
	if(isset($_GET['action']) && $_GET['action'] == 'modify'){
		//为了防止恶意注册，跨站攻击
		_check_code($_POST['code'],$_SESSION['code']);
		
		if(!!$_rows = _fetch_array("select tg_uniqid from tg_user where tg_username='{$_COOKIE['username']}' limit 1")){
			//为了防止cookie伪造，还要比对一下唯一标示符uniqid()
			_uniqid($_rows['tg_uniqid'],$_COOKIE['uniqid']);
			
			//引入验证文件
			include ROOT_PATH.'includes/check.func.php';
			//创建一个空数组，用来存放提交过来的合法数据
			$_clean = array();
			$_clean['password'] = _check_modify_password($_POST['password'],6);
			$_clean['sex'] = _check_sex($_POST['sex']);
			$_clean['face'] = _check_face($_POST['face']);
			$_clean['email'] = _check_email($_POST['email'],5,40);
			$_clean['qq'] = _check_qq($_POST['qq']);
			$_clean['url'] = _check_url($_POST['url'],40);
			$_clean['switch'] = $_POST['switch'];
			$_clean['autograph'] = _check_autograph($_POST['autograph'],200);
			
			//修改资料
			if(empty($_clean['password'])){
				_query("update tg_user set 
											tg_sex='{$_clean['sex']}',
											tg_face='{$_clean['face']}',
											tg_email='{$_clean['email']}',
											tg_qq='{$_clean['qq']}',
											tg_url='{$_clean['url']}',
											tg_switch='{$_clean['switch']}',
											tg_autograph='{$_clean['autograph']}'
										where
											tg_username='{$_COOKIE['username']}'
										");
			}else{
				_query("update tg_user set 
											tg_password='{$_clean['password']}',
											tg_sex='{$_clean['sex']}',
											tg_face='{$_clean['face']}',
											tg_email='{$_clean['email']}',
											tg_qq='{$_clean['qq']}',
											tg_url='{$_clean['url']}',
											tg_switch='{$_clean['switch']}',
											tg_autograph='{$_clean['autograph']}'
										where
											tg_username='{$_COOKIE['username']}'
										");
			}
		}
		//判断是否修改成功
		if(_affected_rows() == 1){
			//关闭
			_close();
			//_session_destroy();
			//跳转
			_location('恭喜你，修改成功','member.php');
		}else{
			_close();
			//_session_destroy();
			_location('很遗憾，没有任何数据被修改','member_modify.php');	
		}
	}
	
	//是否正常登陆
	if(isset($_COOKIE['username'])){
		//获取数据
		$_rows = _fetch_array("select tg_switch,tg_autograph,tg_username,tg_sex,tg_face,tg_email,tg_url,tg_qq from tg_user where tg_username='{$_COOKIE['username']}'");
		if($_rows){
			$_html = array();
			$_html['switch'] = $_rows['tg_switch'];
			$_html['autograph'] = $_rows['tg_autograph'];
			$_html['username'] = $_rows['tg_username'];
			$_html['sex'] = $_rows['tg_sex'];
			$_html['face'] = $_rows['tg_face'];
			$_html['email'] = $_rows['tg_email'];
			$_html['url'] = $_rows['tg_url'];
			$_html['qq'] = $_rows['tg_qq'];
			$_html = _html($_html);
			
			//性别选择
			if($_html['sex'] == '男'){
				$_html['sex_html'] = '<input type="radio" name="sex" value="男" checked="checked" />男　<input type="radio" name="sex" value="女" />女';
			}else if($_html['sex'] == '女'){
				$_html['sex_html'] = '<input type="radio" name="sex" value="男" />男　<input type="radio" name="sex" value="女" checked="checked" />女';
			}
			
			//头像选择
			$_html['face_html'] = '<select name="face">';
			foreach(range(1,9) as $_num){
				//$_html['face_html'] .= '<option value="face/m0'.$_num.'.gif">face/m0'.$_num.'.gif</option>';
				$_face = 'face/m0'.$_num.'.gif';
				if($_face == $_html['face']){
					$_html['face_html'] .= '<option value="'.$_face.'" selected="selected">'.$_face.'</option>';
				}else{
					$_html['face_html'] .= '<option value="'.$_face.'">'.$_face.'</option>';
				}
			}
			foreach(range(10,64) as $_num){
				//$_html['face_html'] .= '<option value="face/m'.$_num.'.gif">face/m'.$_num.'.gif</option>';
				$_face = 'face/m'.$_num.'.gif';
				if($_face == $_html['face']){
					$_html['face_html'] .= '<option value="'.$_face.'" selected="selected">'.$_face.'</option>';
				}else{
					$_html['face_html'] .= '<option value="'.$_face.'">'.$_face.'</option>';
				}
			}
			$_html['face_html'] .= '</select>';
			
			//签名开关
			if($_html['switch'] == 1){
				$_html['switch_html'] = '<input type="radio" name="switch" value="1" checked="checked"/>启用　<input type="radio" name="switch" value="0" />禁用';
			}else if($_html['switch'] == 0){
				$_html['switch_html'] = '<input type="radio" name="switch" value="1" />启用　<input type="radio" name="switch" value="0" checked="checked"/>禁用';
			}
		}else{
			_alert_back('此用户不存在');
		}
	}else{
		_alert_back('非法登陆');
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
	require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/code.js"></script>
<script type="text/javascript" src="js/member_modify.js"></script>
</head>

<body>

	<?php
      	require ROOT_PATH."includes/header.inc.php";
	?>
      
      <div id="member">
      	<?php
            	require ROOT_PATH."includes/member.inc.php";
		?>
            <div id="member_main">
            	<h2>会员管理中心</h2>
                  <form method="post" action="member_modify.php?action=modify">
                  <dl>
                  	<dd>用 户 名：<?php echo $_html['username'];?></dd>
                        <dd>密  码：<input class="text" type="password" name="password"/>(留空则不修改)</dd>
                        <dd>性  别：<?php echo $_html['sex_html'];?></dd>
                        <dd>头  像：<?php echo $_html['face_html'];?></dd>
                        <dd>电子邮件：<input class="text" type="text" name="email" value="<?php echo $_html['email'];?>"/></dd>
                        <dd>主  页：<input class="text" type="text" name="url" value="<?php echo $_html['url'];?>"/></dd>
                        <dd>Q  Q：<input class="text" type="text" name="qq" value="<?php echo $_html['qq'];?>"/></dd>
                        <dd>
                        	个性签名：<?php echo $_html['switch_html'];?>(可以使用ubb代码)
                              <p><textarea name="autograph"><?php echo $_html['autograph'];?></textarea></p>
                        </dd>
                        <dd>验 证 码：<input type="text" name="code" class="text yzm"/><img id="code" src="code.php" />　<input type="submit" class="submit" value="修改资料"/></dd>
                  </dl>
                  </form>
            </div>
      </div>
	
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>	