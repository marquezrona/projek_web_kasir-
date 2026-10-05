<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <title>Login Kasir - {{ $storeSettings->store_name }}</title>
            <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
            <style>
                :root {
                    --ink: #17212b;
                    --muted: #66727e;
                    --teal: #07536a;
                    --teal-dark: #053e51;
                    --line: rgba(255, 255, 255, .58);
                }

                * { box-sizing: border-box; }

                html, body { min-height: 100%; }

                body {
                    margin: 0;
                    color: var(--ink);
                    font-family: "Segoe UI", Tahoma, sans-serif;
                    background: #d9d5cc;
                }

                .login-page {
                    min-height: 100vh;
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    overflow: hidden;
                    padding: 90px 24px 72px;
                    background-image:
                        linear-gradient(90deg, rgba(9, 30, 39, .45), rgba(13, 28, 35, .16) 48%, rgba(9, 25, 30, .4)),
                        url('https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=2200&q=85');
                    background-size: cover;
                    background-position: center;
                }

                .login-page::before {
                    content: "";
                    position: absolute;
                    inset: 0;
                    background: rgba(255, 255, 255, .08);
                    backdrop-filter: blur(1.5px);
                }

                .brand {
                    position: absolute;
                    z-index: 2;
                    top: 27px;
                    left: 28px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    color: #fff;
                    text-shadow: 0 1px 8px rgba(0, 0, 0, .35);
                }

                .brand-logo {
                    width: 58px;
                    height: 58px;
                    border: 2px solid rgba(255, 255, 255, .9);
                    border-radius: 50%;
                    background: #fff;
                    object-fit: cover;
                    box-shadow: 0 1px 8px rgba(0, 0, 0, .25);
                }

                .login-card {
                    position: relative;
                    z-index: 1;
                    width: min(100%, 440px);
                    padding: 27px 24px 23px;
                    border: 1px solid rgba(255, 255, 255, .62);
                    border-radius: 14px;
                    background: rgba(243, 244, 240, .7);
                    box-shadow: 0 18px 45px rgba(0, 0, 0, .24);
                    backdrop-filter: blur(15px);
                }

                .login-heading { text-align: center; margin-bottom: 21px; }
                .login-heading h1 { margin: 0; font-size: 24px; font-weight: 400; color: #fff; text-shadow: 0 1px 5px rgba(0, 0, 0, .25); }
                .login-heading p { margin: 3px 0 0; color: #fff; font-size: 18px; text-shadow: 0 1px 5px rgba(0, 0, 0, .22); }
                .location { margin: 3px 0 0; font-size: 13px; color: #111; }
                .location i { margin-right: 4px; }

                .status, .errors {
                    padding: 9px 11px;
                    margin-bottom: 13px;
                    border-radius: 7px;
                    font-size: 12px;
                }

                .status { color: #075b3d; background: rgba(217, 250, 235, .9); }
                .errors { color: #8a1d1d; background: rgba(255, 224, 224, .9); }
                .errors p { margin: 0; }

                .field { margin-bottom: 15px; }
                .field label { display: block; margin-bottom: 6px; font-size: 13px; color: #111; }
                .input-wrap { position: relative; }
                .input-wrap > i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #75808a; font-size: 16px; }
                .input-wrap input {
                    width: 100%;
                    height: 40px;
                    padding: 0 37px 0 36px;
                    border: 1px solid var(--line);
                    border-radius: 6px;
                    outline: none;
                    color: #2b3339;
                    background: rgba(255, 255, 255, .67);
                    font: inherit;
                    font-size: 13px;
                }
                .input-wrap input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(7, 83, 106, .15); }
                .input-wrap input::placeholder { color: #6d7479; }
                .password-toggle { position: absolute; top: 50%; right: 11px; padding: 0; border: 0; transform: translateY(-50%); color: #68727a; background: transparent; cursor: pointer; }
                .password-toggle:hover { color: var(--teal); }

                .login-button {
                    width: 100%;
                    height: 40px;
                    margin-top: 2px;
                    border: 0;
                    border-radius: 20px;
                    color: #fff;
                    background: var(--teal);
                    font-size: 13px;
                    font-weight: 700;
                    cursor: pointer;
                    transition: background .2s, transform .2s;
                }
                .login-button:hover { background: var(--teal-dark); transform: translateY(-1px); }

                .login-links { margin-top: 14px; text-align: center; font-size: 12px; line-height: 1.9; }
                .login-links a { color: #123e4e; text-decoration: none; }
                .login-links a:hover { text-decoration: underline; }
                .copyright { position: absolute; z-index: 1; bottom: 17px; color: rgba(255, 255, 255, .9); font-size: 10px; text-shadow: 0 1px 4px #333; }

                @media (max-width: 560px) {
                    .login-page { padding: 82px 15px 65px; background-position: 58% center; }
                    .brand { top: 23px; left: 18px; }
                    .login-card { padding: 23px 19px 20px; }
                }
            </style>
        </head>
        <body>
            <main class="login-page">
                <div class="brand" aria-label="{{ $storeSettings->store_name }}">
                    <img class="brand-logo" src="{{ $storeSettings->logo_url }}" alt="Logo {{ $storeSettings->store_name }}">
                </div>

                <section class="login-card" aria-labelledby="login-title">
                    <header class="login-heading">
                        <h1 id="login-title">LOGIN KASIR SEMBAKO</h1>
                        <p>{{ $storeSettings->store_name }}</p>
                        <div class="location"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i>{{ $storeSettings->store_address }}</div>
                    </header>

                    @if (session('status'))
                        <div class="status">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="errors" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="field">
                            <label for="email">Nama Pengguna (Username)</label>
                            <div class="input-wrap">
                                <i class="bi bi-person" aria-hidden="true"></i>
                                <input id="email" type="text" name="email" value="{{ old('email') }}" placeholder="Masukkan Username" autocomplete="username" required autofocus>
                            </div>
                        </div>

                        <div class="field">
                            <label for="password">Kata Sandi (Password)</label>
                            <div class="input-wrap">
                                <i class="bi bi-lock" aria-hidden="true"></i>
                                <input id="password" type="password" name="password" placeholder="Masukkan Kata Sandi" autocomplete="current-password" required>
                                <button class="password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false"><i class="bi bi-eye-slash" aria-hidden="true"></i></button>
                            </div>
                        </div>

                        <button class="login-button" type="submit">LOGIN MASUK</button>
                    </form>

                    <div class="login-links">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Lupa Kata Sandi?</a><br>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Daftar Akun Baru (Jika Belum)</a>
                        @endif
                    </div>
                </section>

                <div class="copyright">&copy; Copyright Alttoch &middot; 27.15, 2023</div>
            </main>

            <script>
                const toggle = document.querySelector('.password-toggle');
                const password = document.querySelector('#password');
                toggle?.addEventListener('click', () => {
                    const visible = password.type === 'text';
                    password.type = visible ? 'password' : 'text';
                    toggle.setAttribute('aria-pressed', String(!visible));
                    toggle.setAttribute('aria-label', visible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
                    toggle.querySelector('i').className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            </script>
        </body>
        </html>
