<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="Keywords" content="灵机,用户中心,linghit,usercenter" />
    <meta name="Description" content="用户中心" />
    <title>用户登陆</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <script src="//g.alicdn.com/dingding/dinglogin/0.0.5/ddLogin.js"></script>
</head>
<body>
<!-- 页面内容 -->
<div class="page-wrap">
    <div class="login-block">
        <img class="logo" src="/assets/img/linghit_logo.png" />
        <!-- 切换到账号密码登陆 -->
        <a class="change-mode" href="{!! \App\Services\Util::addParametersToUrl(\Illuminate\Support\Facades\Request::fullUrl(), ['type' => 2]) !!}">切换账号密码登陆</a>
        <!-- 登陆二维码 -->
        <div id="login_container"></div>
    </div>
</div>

<!-- 弹窗 -->
<div id="tips-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">提示</h4>
            </div>
            <div class="modal-body">
                <p>抱歉，登陆失败</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <!-- <button type="button" class="btn btn-primary">确定</button> -->
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/jquery.1.12.4.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
<script>
    @if(session('error')) {
        /* 调用弹窗 */
        $('#tips-modal').modal('show');
    }
    @endif

    var obj = DDLogin({
        id:"login_container",//这里需要你在自己的页面定义一个HTML标签并设置id，例如<div id="login_container"></div>或<span id="login_container"></span>
        goto: "{!! urlencode($goto) !!}",
        style: "border:none;background-color:#FFFFFF;margin: 0px; padding: 0px;",
        width : "320",
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