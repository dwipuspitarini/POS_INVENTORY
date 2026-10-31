<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Kasir Toko Bangunan</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .login-container p {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 7px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 7px;
            background: #222;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #444;
        }

        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

<div class="login-container">

    <h2>Kasir Toko Bangunan</h2>

    <p>Silakan masuk ke sistem</p>

    <form action="{{ route('login.process') }}" method="POST">

        @csrf

        <div class="form-group">
            <label for="username">Username</label>

            <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                placeholder="Masukkan username"
                required
            >

            @error('username')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">
            Login
        </button>

    </form>

</div>

</body>
</html>