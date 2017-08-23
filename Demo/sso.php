<?php
session_start();

$app_key = 'test';  //子应用app key，需要在用户中心申请
$app_secret = 'test2017';  //子应用app secret，需要在用户中心申请

//如果用户已经登陆过，重定向回来源页面
if(!empty($_SESSION['user'])) {
	header('Location: ' . $_GET['redirect_url']);
    exit;
}


//如果Service Ticket不存在，重定向到用户中心获取
if(empty($_GET['code'])) {
	header('Location: http://datacenter.linghit.com/login?type=4&service=' . urlencode(getCurrentUrl()));
	exit;
}

//调用api/userInfo接口，获取登陆的用户信息
$url = addParametersToUrl('http://datacenter.linghit.com/api/userInfo', ['appKey' => $app_key, 'appSecret' => $app_secret, 'code' => $_GET['code']]);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
curl_setopt($curl, CURLOPT_TIMEOUT, 5);
//SSL setting
$ssl = parse_url($url, PHP_URL_SCHEME) == 'https';
if($ssl) {
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
}

$result =  curl_exec($curl);
curl_close($curl);
$result = json_decode($result, true);

if($result['errcode'] > 0) {
    exit('登陆失败');
} else {
    //生成局部会话
    $_SESSION['user'] = $result;

    //重定向到来源页面
    header('Location: ' . $_GET['redirect_url']);
    exit;
}


//获取当前url
function getCurrentUrl() {
	$pageURL = 'http';

	if (@$_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	}
	else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}

	return $pageURL;
}

//给url添加参数
function addParametersToUrl($url, $parameters) {
	foreach ($parameters as $key => $value) {
		$url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
		$url = substr($url, 0, -1);
		if (strpos($url, '?') === false) {
			$url = $url . '?' . $key . '=' . $value;
		} else {
			$url = $url . '&' . $key . '=' . $value;
		}
	}

	return $url;
}