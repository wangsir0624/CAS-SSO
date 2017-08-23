<!DOCTYPE HTML>
<html>
<head>
    <title>注销登陆</title>
    <meta charset="UTF-8" />
    @foreach($logoutUrls as $url)
    <script type="text/javascript" src="{{ $url }}"></script>
    @endforeach
</head>
<body>
    <script type="text/javascript">
        window.setTimeout('location.href="{{ $destination }}"', {{ $delay * 1000 }});
    </script>
</body>
</html>