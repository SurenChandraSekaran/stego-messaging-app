<!DOCTYPE html>
<html>
<head>
    <title>StegoChat</title>

<style>
body{
    font-family: Arial;
    background-color: #7185E3;
    text-align: center;
}

.container{
    width: 400px;
    margin: 70px auto;
    background: #f2f2f2;
    padding: 30px;
    border-radius: 6px;
}

a{
    display: block;
    margin: 10px 0;
    padding: 10px;
    background: #333;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
</style>
</head>

<body>

<h1 style="font-family: Lucida Handwriting; font-size: 60px;">
StegChat
</h1>

<div class="container">

<p>Welcome</p>

<a href="{{ route('login') }}">Login</a>
<a href="{{ route('register') }}">Register</a>

</div>

</body>
</html>