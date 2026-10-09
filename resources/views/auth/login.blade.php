<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <title>Login Kasir - {{ $storeSettings->store_name }}</title>
            <link rel="icon" type="image/png" sizes="256x256" href="{{ asset('assets/img/jazzel-favicon.png?v=2') }}">
            <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
            <style>
                :root {
                    --ink: #3a3334;
                    --muted: #766d6f;
                    --teal: #b94245;
                    --teal-dark: #8d2a22;
                    --line: rgba(255, 255, 255, .58);
                }

                * { box-sizing: border-box; }

                html, body {
                    width: 100%;
                    height: 100%;
                    min-height: 100%;
                }

                body {
                    margin: 0;
                    color: var(--ink);
                    font-family: "Segoe UI", Tahoma, sans-serif;
                    background: #d9d5cc;
                }

                .login-page {
                    position: fixed;
                    inset: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    overflow-x: hidden;
                    overflow-y: auto;
                    -webkit-overflow-scrolling: touch;
                    padding: 90px 24px 72px;
                    background-image:
                        linear-gradient(90deg, rgba(67, 28, 29, .5), rgba(72, 43, 44, .18) 48%, rgba(67, 28, 29, .42)),
                        url('https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=2200&q=85');
                    background-size: cover;
                    background-position: center;
                }

                .login-page::before {
                    content: "";
                    position: absolute;
                    inset: 0;
                    background: rgba(255, 255, 255, .12);
                    backdrop-filter: blur(3px);
                    -webkit-backdrop-filter: blur(3px);
                }

                .brand {
                    position: absolute;
                    z-index: 2;
                    top: 27px;
                    left: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    color: #fff;
                    text-shadow: 0 1px 8px rgba(0, 0, 0, .35);
                    transform: translateX(-50%);
                }

                .brand-logo {
                    width: 125px;
                    height: auto;
                    max-height: 92px;
                    object-fit: contain;
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
                .login-heading h1 { margin: 0; font-size: 27px; font-weight: 500; color: #000; text-shadow: none; }
                .location { margin: 5px 0 0; font-size: 15px; font-weight: 500; color: #000; }
                .location i { margin-right: 4px; }

                .status, .errors {
                    padding: 9px 11px;
                    margin-bottom: 13px;
                    border-radius: 7px;
                    font-size: 13px;
                }

                .status { color: #075b3d; background: rgba(217, 250, 235, .9); }
                .errors { color: #8a1d1d; background: rgba(255, 224, 224, .9); }
                .errors p { margin: 0; }

                .field { margin-bottom: 15px; }
                .field label { display: block; margin-bottom: 6px; font-size: 14px; color: #111; }
                .input-wrap { position: relative; }
                .input-wrap > i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #75808a; font-size: 16px; }
                .input-wrap input {
                    width: 100%;
                    height: 43px;
                    padding: 0 37px 0 36px;
                    border: 1px solid var(--line);
                    border-radius: 6px;
                    outline: none;
                    color: #2b3339;
                    background: rgba(255, 255, 255, .67);
                    font: inherit;
                    font-size: 14px;
                }
                .input-wrap input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(185, 66, 69, .22); }
                .input-wrap input::placeholder { color: #6d7479; }
                .password-toggle { position: absolute; top: 50%; right: 11px; padding: 0; border: 0; transform: translateY(-50%); color: #68727a; background: transparent; cursor: pointer; }
                .password-toggle:hover { color: var(--teal); }

                .login-button {
                    width: 100%;
                    height: 43px;
                    margin-top: 2px;
                    border: 0;
                    border-radius: 20px;
                    color: #fff;
                    background: var(--teal);
                    font-size: 14px;
                    font-weight: 700;
                    cursor: pointer;
                    transition: background .2s, transform .2s;
                }
                .login-button:hover { color: #fff; background: var(--teal-dark); transform: translateY(-1px); }

                .login-links { margin-top: 14px; text-align: center; font-size: 13px; line-height: 1.9; }
                .login-links a { color: #8d2a22; text-decoration: none; }
                .login-links a:hover { text-decoration: underline; }
                .copyright { position: absolute; z-index: 1; bottom: 17px; color: rgba(255, 255, 255, .9); font-size: 11px; text-shadow: 0 1px 4px #333; }

                @media (max-width: 560px) {
                    .login-page { padding: 82px 15px 65px; background-position: 58% center; }
                    .brand { top: 18px; }
                    .brand-logo { width: 180px; }
                    .login-card { padding: 23px 19px 20px; }
                    .login-heading h1 { font-size: 24px; }
                    .location { font-size: 14px; }
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
                        <h1 id="login-title">TOKO JAZZEL</h1>
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
