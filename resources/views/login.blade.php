<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - WebKasir</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f4f7fb;
            overflow-x: hidden;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* =========================
           BAGIAN KIRI
        ========================= */
        .left-section {
            width: 52%;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #1769e0, #0d47a1);
            color: white;
            padding: 55px 70px;
        }

        .left-section::before {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            right: -200px;
            top: -150px;
        }

        .left-section::after {
            content: "";
            position: absolute;
            width: 450px;
            height: 450px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
            left: -220px;
            bottom: -220px;
        }

        .brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-icon {
            width: 58px;
            height: 58px;
            border-radius: 15px;
            background: white;
            color: #1769e0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        .brand h1 {
            font-size: 30px;
            font-weight: 700;
        }

        .brand p {
            margin-top: 3px;
            font-size: 14px;
            opacity: 0.85;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            margin-top: 90px;
            max-width: 600px;
        }

        .hero-content h2 {
            font-size: 46px;
            line-height: 1.15;
            margin-bottom: 22px;
        }

        .hero-content h2 span {
            color: #73b8ff;
        }

        .hero-content > p {
            font-size: 17px;
            line-height: 1.7;
            max-width: 530px;
            color: #e5efff;
        }

        /* FEATURES */
        .features {
            display: flex;
            gap: 35px;
            margin-top: 40px;
        }

        .feature {
            text-align: center;
            width: 100px;
        }

        .feature-icon {
            width: 55px;
            height: 55px;
            margin: auto;
            border-radius: 15px;
            background: rgba(255,255,255,0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .feature p {
            font-size: 13px;
            margin-top: 10px;
            line-height: 1.4;
        }

        /* ILUSTRASI KASIR */
        .cashier-area {
            position: absolute;
            z-index: 2;
            bottom: 0;
            left: 70px;
            right: 40px;
            height: 240px;
        }

        .counter {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 85px;
            background: #c8874b;
            border-radius: 8px 8px 0 0;
            box-shadow: 0 -8px 20px rgba(0,0,0,0.15);
        }

        .monitor {
            position: absolute;
            left: 35%;
            bottom: 75px;
            width: 220px;
            height: 135px;
            background: #162b4d;
            border-radius: 15px;
            transform: perspective(500px) rotateX(4deg);
            box-shadow: 0 15px 30px rgba(0,0,0,0.25);
            padding: 10px;
        }

        .screen {
            width: 100%;
            height: 100%;
            background: #edf5ff;
            border-radius: 9px;
            padding: 15px;
            color: #17355e;
        }

        .screen-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .product-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 5px 0;
            border-bottom: 1px solid #dce7f5;
        }

        .total {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-weight: bold;
            color: #1769e0;
        }

        .scanner {
            position: absolute;
            right: 15%;
            bottom: 78px;
            width: 55px;
            height: 90px;
            background: #172c4d;
            border-radius: 15px 15px 25px 25px;
            transform: rotate(8deg);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .scanner::before {
            content: "";
            position: absolute;
            top: 18px;
            left: 12px;
            width: 31px;
            height: 18px;
            background: #314e77;
            border-radius: 5px;
        }

        .receipt {
            position: absolute;
            left: 20%;
            bottom: 78px;
            width: 70px;
            height: 95px;
            background: white;
            transform: rotate(-5deg);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            padding: 12px 8px;
        }

        .receipt::after {
            content: "----------------\A WebKasir\A Produk     Rp15.000\A ----------------\A TOTAL      Rp15.000";
            white-space: pre;
            font-size: 7px;
            color: #555;
            line-height: 1.8;
        }

        /* =========================
           BAGIAN KANAN
        ========================= */
        .right-section {
            width: 48%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #f7faff;
            position: relative;
        }

        .right-section::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: #e8f2ff;
            top: -120px;
            right: -80px;
        }

        .login-card {
            width: 100%;
            max-width: 500px;
            background: white;
            padding: 45px;
            border-radius: 22px;
            position: relative;
            z-index: 2;
            box-shadow: 0 20px 60px rgba(32, 77, 130, 0.12);
            border: 1px solid #edf2f8;
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-logo {
            width: 70px;
            height: 70px;
            margin: auto;
            border-radius: 20px;
            background: #eaf3ff;
            color: #1769e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            margin-bottom: 18px;
        }

        .login-header h2 {
            font-size: 30px;
            color: #162b4d;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #8290a5;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #263b59;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #8290a5;
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            height: 54px;
            border: 1px solid #d9e2ee;
            border-radius: 11px;
            padding: 0 18px 0 48px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
            color: #263b59;
            background: #fff;
        }

        .form-control:focus {
            border-color: #1769e0;
            box-shadow: 0 0 0 4px rgba(23,105,224,0.09);
        }

        .form-control::placeholder {
            color: #aab5c4;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 5px 0 25px;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #63728a;
        }

        .remember input {
            width: 17px;
            height: 17px;
            accent-color: #1769e0;
        }

        .forgot {
            color: #1769e0;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            height: 55px;
            border: none;
            border-radius: 11px;
            background: linear-gradient(135deg, #1769e0, #2585f5);
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 8px 20px rgba(23,105,224,0.22);
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(23,105,224,0.3);
        }

        .back-button {
            display: block;
            text-align: center;
            text-decoration: none;
            height: 52px;
            line-height: 52px;
            border: 1px solid #dce5f0;
            border-radius: 11px;
            color: #52647c;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .back-button:hover {
            border-color: #1769e0;
            color: #1769e0;
            background: #f7fbff;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 25px 0;
            color: #a1adbd;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .error-message {
            background: #fff1f1;
            border: 1px solid #ffd3d3;
            color: #d33c3c;
            padding: 12px 15px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 1000px) {
            .left-section {
                width: 45%;
                padding: 40px;
            }

            .right-section {
                width: 55%;
                padding: 25px;
            }

            .hero-content h2 {
                font-size: 36px;
            }

            .features {
                gap: 15px;
            }

            .cashier-area {
                display: none;
            }
        }

        @media (max-width: 750px) {
            .login-wrapper {
                display: block;
            }

            .left-section {
                width: 100%;
                min-height: 300px;
                padding: 30px;
            }

            .hero-content {
                margin-top: 40px;
            }

            .hero-content h2 {
                font-size: 32px;
            }

            .hero-content > p {
                font-size: 14px;
            }

            .features {
                display: none;
            }

            .right-section {
                width: 100%;
                min-height: auto;
                padding: 30px 20px;
            }

            .login-card {
                padding: 30px 25px;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <!-- =========================
         LEFT SECTION
    ========================== -->
    <section class="left-section">

        <div class="brand">
            <div class="brand-icon">
                🧾
            </div>

            <div>
                <h1>WebKasir</h1>
                <p>Sistem Kasir Modern & Praktis</p>
            </div>
        </div>

        <div class="hero-content">

            <h2>
                Kelola Transaksi,
                Lebih <span>Mudah & Cepat</span>
            </h2>

            <p>
                WebKasir membantu Anda mengelola transaksi,
                stok barang, dan laporan penjualan dengan lebih
                cepat, mudah, dan efisien.
            </p>

            <div class="features">

                <div class="feature">
                    <div class="feature-icon">🛒</div>
                    <p>Transaksi<br><b>Penjualan</b></p>
                </div>

                <div class="feature">
                    <div class="feature-icon">📦</div>
                    <p>Manajemen<br><b>Stok</b></p>
                </div>

                <div class="feature">
                    <div class="feature-icon">📊</div>
                    <p>Laporan<br><b>Penjualan</b></p>
                </div>

                <div class="feature">
                    <div class="feature-icon">🔒</div>
                    <p>Aman &<br><b>Terpercaya</b></p>
                </div>

            </div>

        </div>

        <!-- Ilustrasi POS -->
        <div class="cashier-area">

            <div class="monitor">

                <div class="screen">

                    <div class="screen-title">
                        🛒 WebKasir POS
                    </div>

                    <div class="product-row">
                        <span>Beras</span>
                        <span>Rp15.000</span>
                    </div>

                    <div class="product-row">
                        <span>Minuman</span>
                        <span>Rp8.000</span>
                    </div>

                    <div class="total">
                        <span>TOTAL</span>
                        <span>Rp23.000</span>
                    </div>

                </div>

            </div>

            <div class="scanner"></div>

            <div class="receipt"></div>

            <div class="counter"></div>

        </div>

    </section>


    <!-- =========================
         RIGHT SECTION
    ========================== -->
    <section class="right-section">

        <div class="login-card">

            <div class="login-header">

                <div class="login-logo">
                    🧾
                </div>

                <h2>Selamat Datang</h2>

                <p>
                    Silakan login untuk melanjutkan ke sistem kasir
                </p>

            </div>


            {{-- Error Login --}}
            @if ($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif


            {{-- FORM LOGIN --}}
            <form action="{{ route('login.process') }}" method="POST">

                @csrf

                <!-- EMAIL -->
                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <div class="input-wrapper">

                        <span class="input-icon">
                            ✉
                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="Masukkan email Anda"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >

                    </div>

                </div>


                <!-- PASSWORD -->
                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <span class="input-icon">
                            🔒
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password Anda"
                            required
                        >

                    </div>

                </div>


                <!-- OPTIONS -->
                <div class="form-options">

                    <label class="remember">

                        <input
                            type="checkbox"
                            name="remember"
                        >

                        <span>Ingat saya</span>

                    </label>

                    <a href="#" class="forgot">
                        Lupa password?
                    </a>

                </div>


                <!-- LOGIN -->
                <button
                    type="submit"
                    class="login-button"
                >
                    LOGIN &nbsp; →
                </button>

            </form>


            <!-- DIVIDER -->
            <div class="divider">
                atau
            </div>


            <!-- KEMBALI -->
            <a href="/" class="back-button">
                🏠 &nbsp; Kembali ke Beranda
            </a>

        </div>

    </section>

</div>

</body>
</html>