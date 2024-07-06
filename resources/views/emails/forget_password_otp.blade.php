<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width-device-width, initial scale-1.0">
  <title>Document</title>
</head>
 <body>
      <h1>Welcome {{$user->name}}</h1>
       <p>email address {{$user->email}}</p>
       <p>your OTP verify Code is: {{$user->verify_code}}</p>
 </body>
</html>