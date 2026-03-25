<!DOCTYPE html>
<html>
<head>
    <title>StegoChat - Login</title>

<style>
body{
    font-family: Arial;
    background-color: #7185E3;
}

.container{
    width: 350px;
    margin: 50px auto;
    background: #f2f2f2;
    padding: 20px;
    border-radius: 6px;
}

input{
    width: 95%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button{
    width: 100%;
    padding: 10px;
    background: #333;
    color: white;
    border: none;
    border-radius: 5px;
}

.links{
    margin-top: 10px;
}

.links a{
    display: block;
    font-size: 14px;
}

.error{
    color: red;
    font-size: 13px;
}
</style>
</head>

<body>

<h1 style="font-family: Lucida Handwriting; font-size: 60px; text-align: center;">
StegChat
</h1>

<div class="container">

<form method="POST" action="{{ route('login') }}">
@csrf

<label>Email</label>
<input type="email" name="email" value="{{ old('email') }}" required>

@error('email')
<div class="error">{{ $message }}</div>
@enderror


<label>Password</label>
<input type="password" name="password" required>

@error('password')
<div class="error">{{ $message }}</div>
@enderror


<button type="submit">Log In</button>

<div class="links">
<a href="{{ route('password.request') }}">Forgot password?</a>
<a href="{{ route('register') }}">New here? Click to sign up!</a>
</div>

</form>

</div>

</body>
</html>