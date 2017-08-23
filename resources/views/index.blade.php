<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="Keywords" content="灵机,用户中心,linghit,usercenter" />
    <meta name="Description" content="用户中心" />
    <title>登陆成功</title>
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<!-- 页面内容 -->
<div class="page-wrap">
    <div class="info-block">
        <img class="logo" src="./assets/img/linghit_logo.png" />
        <p class="tips-text">{{ !empty($userEntity->realname) ? $userEntity->realname : $mobile }}，您已登陆成功</p>
        <!-- 注销按钮 -->
        <a class="logout-btn" href="{{ url('logout') }}">注销</a>
    </div>
</div>
</body>
</html>
