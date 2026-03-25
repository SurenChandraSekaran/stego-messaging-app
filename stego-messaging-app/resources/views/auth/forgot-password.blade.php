<!DOCTYPE html>
<html>
<head>
    <title>StegoChat - Reset Password</title>

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

.success{
    color: green;
    font-size: 13px;
}
</style>
</head>

<body>

<h1 style="font-family: Lucida Handwriting; font-size: 60px; text-align: center;">
StegChat
</h1>

<div class="container">

<p style="font-size: 14px;">
Forgot your password? Enter your email and we’ll send you a reset link.
</p>

<!-- Success Message -->
@if (session('status'))
    <div class="success">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
@csrf

<label>Email</label>
<input type="email" name="email" value="{{ old('email') }}" required autofocus>

@error('email')
<div class="error">{{ $message }}</div>
@enderror

<button type="submit">Send Reset Link</button>

<div class="links">
<a href="{{ route('login') }}">Back to Login</a>
</div>

</form>

</div>

</body>
</html>