<!DOCTYPE html>
<html>
<head>
    <title>StegChat - Verify Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #7185E3;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 420px;
            margin: 80px auto;
            background: #f2f2f2;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }

        .status-alert {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            color: #065f46;
            padding: 12px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: left;
        }

        p {
            color: #444;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
            text-align: left;
        }

        .actions-wrapper {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.2s;
        }

        button:hover {
            background: #444;
        }

        .btn-logout {
            background: transparent;
            color: #555;
            border: 1px solid #ccc;
            font-weight: normal;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .btn-logout:hover {
            background: #e5e5e5;
            color: #333;
        }
    </style>
</head>
<body>

    <h1 style="font-family: 'Lucida Handwriting', cursive; font-size: 80px; text-align: center; margin-top: 40px; color: black;">
        StegChat
    </h1>

    <div class="container">
        <h2 style="margin-top: 0; color: #222; font-size: 22px;">Verify Your Identity</h2>
        
        <p>
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive it, you can click the button below to request another one.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="status-alert">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="actions-wrapper">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit">Resend Verification Email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Log Out</button>
            </form>
        </div>
    </div>

</body>
</html>