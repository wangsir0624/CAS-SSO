<?php
session_start();

//如果未登陆，重定向到登陆页面sso.php
if(empty($_SESSION['user'])) {
	//登陆成功后会自动跳转到redirect_url页面
	header('Location: http://passport.demo.com/sso.php?redirect_url=' . urlencode('http://passport.demo.com/index.php'));
	exit;
}

var_dump($_SESSION['user']);

//尤其注意，网站在注销登陆时，必须使用用户中心的注销登陆链接
echo '<a href="http://datacenter.linghit.com/logout?service=' . urlencode('http://passport.demo.com') . '">注销登陆</a>';