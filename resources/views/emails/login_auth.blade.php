<!DOCTYPE html>
<html>
<head>
    <title><strong>Welcome Back to WeDoCare!</strong></title>
</head>
<body>
    <h1>Hello, {{ $mail_data['username'] }}!</h1>
    <p>You have received this message because your account is attempting to login to our healthcare forum platform. To proceed to login, you may enter this code at the verification screen: </p>
    <h1> {{ $mail_data['code'] }} </h1>
    <p>If this is not you, please contact our customer support immediately.</p>
    <p>This is an automated message. Please do not reply to this message.</p>
</body>
</html>
