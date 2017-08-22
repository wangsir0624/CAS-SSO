<!DOCTYPE HTML>
<html>
<head>
    <title>登陆测试</title>
    <meta charset="UTF-8" />
    <script src="//g.alicdn.com/dingding/dinglogin/0.0.5/ddLogin.js"></script>
</head>
<body>
<div id="login_container"></div>

<script type="text/javascript">
    var obj = DDLogin({
        id:"login_container",//这里需要你在自己的页面定义一个HTML标签并设置id，例如<div id="login_container"></div>或<span id="login_container"></span>
        goto: "{!! urlencode($goto) !!}",
        style: "border:none;background-color:#FFFFFF;",
        width : "365",
        height: "400"
    });

    var hanndleMessage = function (event) {
        var loginTmpCode = event.data; //拿到loginTmpCode后就可以在这里构造跳转链接进行跳转了
        var origin = event.origin;

        location.href = "{!! $goto !!}&loginTmpCode=" + loginTmpCode;
    };

    if (typeof window.addEventListener != 'undefined') {
        window.addEventListener('message', hanndleMessage, false);
    } else if (typeof window.attachEvent != 'undefined') {
        window.attachEvent('onmessage', hanndleMessage);
    }
</script>
</body>
</html>