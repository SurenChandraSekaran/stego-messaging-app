<!DOCTYPE html>
<html>
<head>
    <title>StegChat - Register</title>

<style>

body{
    font-family: Arial;
    background-color: #7185E3;
}

.container{
    width: 400px;
    margin: 50px auto;
    background: #f2f2f2;
    padding: 20px;
    border-radius: 6px;
}

input{
    width: 90%;
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

<h1 style="font-family: Lucida Handwriting; font-size: 80px; text-align: center;">StegoChat</h1>

<div class="container">

<form method="POST" action="{{ route('register') }}">
@csrf

<label>Name</label>
<input type="text" name="name" value="{{ old('name') }}" required autofocus>
@error('name')
<div class="error">{{ $message }}</div>
@enderror


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


<label>Confirm Password</label>
<input type="password" name="password_confirmation" required>
@error('password_confirmation')
<div class="error">{{ $message }}</div>
@enderror


<button type="submit">Register</button>

<div class="links">
<a href="{{ route('login') }}">Already registered? Login here</a>
</div>

</form>

</div>

</body>
</html>