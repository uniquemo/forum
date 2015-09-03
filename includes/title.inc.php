<?php
	//防止恶意调用
	if(!defined('IN_TG')){
		exit('Access defined!');
	}
	//防止非Html页面调用
	if(!defined('SCRIPT')){
		exit('Script Error!');
	}
	global $_system;
?>	
<title><?php echo $_system['webname'];?></title>
<link rel="shortcut icon" href="favicon.ico"/>
<link rel="stylesheet" type="text/css" href="styles/<?php echo $_system['skin']?>/basic.css" />
<link rel="stylesheet" type="text/css" href="styles/<?php echo $_system['skin']?>/<?php echo SCRIPT;?>.css" />	
