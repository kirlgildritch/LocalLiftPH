<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: 'DM Sans', sans-serif;
            background:
                radial-gradient(circle at top, rgba(66, 165, 245, 0.16), transparent 32%),
                #07111d;
            color: #f5f9ff;
        }
        .admin-login-shell {
            width: min(100%, 440px);
            padding: 28px;
            border: 1px solid rgba(187, 222, 251, 0.16);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(10, 19, 34, 0.96), rgba(7, 14, 24, 0.94));
            box-shadow: 0 24px 56px rgba(1, 6, 14, 0.4);
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 12px;
            border: 1px solid rgba(187, 222, 251, 0.18);
            border-radius: 999px;
            color: #bbdefb;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: rgba(255, 255, 255, 0.03);
        }
        h1 {
            margin: 16px 0 10px;
            font-size: 2rem;
            letter-spacing: -0.04em;
        }
        p {
            margin: 0 0 24px;
            color: #8fa7c4;
            line-height: 1.7;
        }
        .field {
            display: grid;
            gap: 8px;
            margin-bottom: 16px;
        }
        label {
            color: #bbdefb;
            font-weight: 600;
        }
        input {
            width: 100%;
            min-height: 50px;
            padding: 0 14px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.04);
            color: #f5f9ff;
            outline: none;
        }
        input:focus {
            border-color: rgba(66, 165, 245, 0.42);
            box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.14);
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 4px 0 18px;
            color: #8fa7c4;
        }
        .remember input {
            width: 16px;
            min-height: auto;
            box-shadow: none;
        }
        .error {
            margin-top: 6px;
            color: #ffc0cb;
            font-size: 13px;
        }
        .submit-btn {
            width: 100%;
            min-height: 50px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #42a5f5, #1565c0);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }
        .back-link {
            display: inline-flex;
            margin-top: 18px;
            color: #bbdefb;
        }
    </style>
</head>
<body>
    <section class="admin-login-shell">
        <span class="eyebrow">Admin Access</span>
        <h1>Admin Login</h1>
        <p>Sign in with an admin account to access the LocalLift admin dashboard.</p>

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                >
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                <span>Remember this device</span>
            </label>

            <button type="submit" class="submit-btn">Log In</button>
        </form>

        <a href="{{ route('home') }}" class="back-link">Back to site</a>
    </section>
</body>
</html>
