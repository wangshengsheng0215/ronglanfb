<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
亲爱的 <span style="color:rgb(108,114,236)">{{$name}}:</span> <br><br>
           欢迎使用发包狗平台，你将进行邮箱验证（5分钟内有效）<br>
邮箱验证码：<span style="color:rgb(108,114,236);font-weight: bold">{{$code}}</span> <br>
           如果您没有申请邮箱验证，请忽略此邮件。<br>
           发包狗平台<br>
           {{$time}}<br>
          （本邮件由系统自动发出，请勿回复。）
</body>
</html>
