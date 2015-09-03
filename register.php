<?php
	session_start();
	//定义一个常量，用来授权调用includes里面的文件
	define('IN_TG',true);
	//定义一个常量，用来指定本页的内容
	define('SCRIPT','register');
	
	//引入公共文件
	//转换成硬路径，引用速度更快
	require dirname(__FILE__).'/includes/common.inc.php';
	
	//登录状态
	_login_state();
	
	global $_system;
	
	$_uniqid = '';
	//判断是否提交了
	if(isset($_GET['action']) && $_GET['action'] == 'register'){
		if(empty($_system['register'])){
			exit('不要非法注册！');
		}
		//为了防止恶意注册，跨站攻击
		_check_code($_POST['code'],$_SESSION['code']);
		
		//引入验证文件
		include ROOT_PATH.'includes/check.func.php';
		
		//创建一个空数组，用来存放提交过来的合法数据
		$_clean = array();
		//可以通过唯一标识符来防止恶意注册，伪装表单跨站攻击等
		//这个存放入数据库的唯一标识符还有第二个用处，就是登陆cookie的验证
		$_clean['uniqid'] = _check_uniqid($_POST['uniqid'],$_SESSION['uniqid']);
		//active也是一个唯一标识符，用来刚注册的用户进行激活处理，方可登陆
		$_clean['active'] = _sha1_uniqid();
		$_clean['username'] = _check_username($_POST['username']);
		$_clean['password'] = _check_password($_POST['password'],$_POST['notpassword'],6);
		$_clean['question'] = _check_question($_POST['question'],2,20);
		$_clean['answer'] = _check_answer($_POST['question'],$_POST['answer'],2,20);
		$_clean['sex'] = _check_sex($_POST['sex']);
		$_clean['face'] = _check_face($_POST['face']);
		$_clean['email'] = _check_email($_POST['email'],5,40);
		$_clean['qq'] = _check_qq($_POST['qq']);
		$_clean['url'] = _check_url($_POST['url'],40);
		//print_r($_clean);
		
		//首先获取本机名
		$hostname=gethostbyaddr($_SERVER['REMOTE_ADDR']);
		//通过本机名获取Ip
		$ip = gethostbyname("$hostname");
		
		//在新增之前，要判断用户名是否重复
		_is_repeat(
			"select tg_username from tg_user where tg_username='{$_clean['username']}' limit 1",
			"对不起，该用户名已被注册"
		);
		
		//新增用户
		//在双引号里直接放变量是可以的，比如$_username，但如果是数组，就必须加上{}，比如{$_clean['username']}
		_query(
				"insert into tg_user(
										tg_uniqid,
										tg_active,
										tg_username,
										tg_password,
										tg_question,
										tg_answer,
										tg_sex,
										tg_face,
										tg_email,
										tg_qq,
										tg_url,
										tg_reg_time,
										tg_last_time,
										tg_last_ip
									) 
								values(
										'{$_clean['uniqid']}',
										'{$_clean['active']}',
										'{$_clean['username']}',
										'{$_clean['password']}',
										'{$_clean['question']}',
										'{$_clean['answer']}',
										'{$_clean['sex']}',
										'{$_clean['face']}',
										'{$_clean['email']}',
										'{$_clean['qq']}',
										'{$_clean['url']}',
										NOW(),
										NOW(),
										'$ip'
									)"
		);
		
		if(_affected_rows() == 1){
			//获取刚刚新增的id
			$_clean['id'] = _insert_id();
			//关闭
			_close();
			//_session_destroy();
			//生成xml
			_set_xml('new.xml',$_clean);
			//跳转
			_location('恭喜你，注册成功','active.php?active='.$_clean['active']);
		}else{
			_close();
			//_session_destroy();
			_location('很遗憾，注册失败','register.php');	
		}
	}else{
		$_SESSION['uniqid'] = $_uniqid = _sha1_uniqid();
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
<script type="text/javascript" src="js/register.js"></script>
</head>

<body>

	<?php
      	require ROOT_PATH."includes/header.inc.php";
	?>
      
      <div id="register">
      	<h2>会员注册</h2>
            <?php
            	if(!empty($_system['register'])){
		?>
            <form method="post" name="register" action="register.php?action=register">
            	
                  <!--<input type="hidden" name="action" value="register"/>-->
                  <input type="hidden" name="uniqid" value="<?php echo $_uniqid;?>"/>
            	<dl>
                  	<dt>请认真填写以下内容</dt>
                        <dd>用 户 名：<input type="text" name="username" class="text"/>（*必填，至少两位）</dd>
                        <dd>密　　码：<input type="password" name="password" class="text"/>（*必填，至少六位）</dd>
                        <dd>确认密码：<input type="password" name="notpassword" class="text"/>（*必填，同上）</dd>
                        <dd>密码提示：<input type="text" name="question" class="text"/>（*必填，至少两位）</dd>
                        <dd>密码回答：<input type="text" name="answer" class="text"/>（*必填，至少两位）</dd>
                        <dd>性　　别：<input type="radio" name="sex" value="男" checked="checked"/> 男　<input type="radio" name="sex" value="女"/> 女</dd>
                        <dd class="face"><input type="hidden" name="face" value="face/m01.gif"/><img id="faceimg" src="face/m01.gif" alt="头像选择" /></dd>
                        <dd>电子邮件：<input type="text" name="email" class="text"/>（*必填，激活账户）</dd>
                        <dd>　Q Q　：<input type="text" name="qq" class="text"/></dd>
                        <dd>主页地址：<input type="text" name="url" class="text" value="http://"/></dd>
                        <dd>验 证 码：<input type="text" name="code" class="text yzm"/><img id="code" src="code.php" /></dd>
                        <dd><input type="submit" class="submit" value="注册"/></dd>
                  </dl>
                  
            </form>
            <?php
			}else{
				echo '<h4 style="text-align:center;">本站关闭了注册功能</h4>';
			}
		?>
      </div>
      
      <?php
      	require ROOT_PATH."includes/footer.inc.php";
	?>

</body>
</html>