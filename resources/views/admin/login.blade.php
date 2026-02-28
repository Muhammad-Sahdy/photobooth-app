@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        overflow-x: hidden;
    }

    .login-container {
        min-height: 100vh;
        background: linear-gradient(180deg, #f5f3ef 0%, #e8e6e1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        position: relative;
        overflow: hidden;
    }

    .login-container::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
        z-index: 2;
        pointer-events: none;
    }

    .login-card {
        position: relative;
        z-index: 10;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.9);
        border-radius: 24px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        padding: 48px 44px;
        width: 100%;
        max-width: 420px;
    }

    .login-header {
        margin-bottom: 36px;
        text-align: center;
    }

    .login-title {
        font-size: 36px;
        font-weight: 300;
        color: #4a4845;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .login-subtitle {
        font-size: 13px;
        color: #8b8680;
        font-weight: 400;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .divider {
        width: 40px;
        height: 1.5px;
        background: #c8c5c0;
        margin: 16px auto 0;
        border-radius: 2px;
    }

    .error-box {
        background: #fff0f0;
        border: 1px solid #ffb3b3;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 24px;
        font-size: 14px;
        color: #c44747;
        font-weight: 400;
        letter-spacing: 0.2px;
    }

    .form-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
    }

    .form-field label {
        font-size: 13px;
        color: #6b6865;
        font-weight: 400;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .form-field input {
        padding: 13px 16px;
        font-size: 15px;
        border: 1.5px solid #d4d2cd;
        border-radius: 12px;
        background: white;
        color: #4a4845;
        transition: all 0.3s ease;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        width: 100%;
    }

    .form-field input::placeholder {
        color: #a8a6a1;
    }

    .form-field input:focus {
        outline: none;
        border-color: #8b8680;
        box-shadow: 0 0 0 3px rgba(139, 134, 128, 0.1);
    }

    .btn-login {
        width: 100%;
        padding: 14px 28px;
        font-size: 14px;
        background: #8b8680;
        color: white;
        border: 1.5px solid #8b8680;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 400;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-top: 8px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }

    .btn-login:hover {
        background: #6f6c68;
        border-color: #6f6c68;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    @media (max-width: 480px) {
        .login-card {
            padding: 36px 28px;
        }

        .login-title {
            font-size: 28px;
        }
    }
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1 class="login-title">Dashboard Admin</h1>
            <p class="login-subtitle">Masuk ke akun Anda</p>
            <div class="divider"></div>
        </div>

        @if($errors->any())
        <div class="error-box">
            {{ $errors->first('email') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="form-field">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="admin@example.com"
                    required
                    autocomplete="email">
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</div>
@endsection