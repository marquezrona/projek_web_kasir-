<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laravel</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
            color: #1f2937;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
        }

        /* Navbar */
        .navbar {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ff2d20;
        }

        .nav-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-right a {
            text-decoration: none;
            color: #374151;
            font-size: 14px;
        }

        .register {
            border: 1px solid #d1d5db;
            padding: 8px 18px;
            border-radius: 5px;
        }

        .register:hover {
            background: #f3f4f6;
        }

        /* Main */
        .main {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 900px;
            min-height: 380px;
            display: flex;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .content {
            width: 50%;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .content h1 {
            font-size: 14px;
            margin-bottom: 12px;
            color: #111827;
        }

        .content p {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .links {
            margin-top: 10px;
        }

        .links a {
            display: block;
            color: #4b5563;
            font-size: 12px;
            margin: 10px 0;
            text-decoration: none;
        }

        .links a:hover {
            color: #ff2d20;
        }

        .button {
            display: inline-block;
            width: fit-content;
            margin-top: 20px;
            padding: 10px 18px;
            background: #1f2937;
            color: white;
            text-decoration: none;
            font-size: 11px;
            border-radius: 4px;
        }

        .button:hover {
            background: #111827;
        }

        /* Laravel Illustration */
        .illustration {
            width: 50%;
            background: #fff0f0;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .laravel-text {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 62px;
            font-weight: bold;
            color: #ff2d20;
            letter-spacing: -5px;
        }

        .shape {
            width: 240px;
            height: 240px;
            border: 3px solid #ff2d20;
            transform: rotate(45deg);
            position: relative;
        }

        .shape::before {
            content: "";
            position: absolute;
            width: 170px;
            height: 170px;
            border: 3px solid #ff2d20;
            left: 32px;
            top: 32px;
        }

        .shape::after {
            content: "L";
            position: absolute;
            font-size: 180px;
            font-weight: bold;
            color: #ff2d20;
            transform: rotate(-45deg);
            left: 65px;
            top: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {

            .card {
                width: 95%;
                flex-direction: column;
            }

            .content,
            .illustration {
                width: 100%;
            }

            .illustration {
                min-height: 280px;
            }

            .navbar {
                padding: 0 15px;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="container">

        <nav class="navbar">

            <div class="logo">
                Laravel
            </div>

            <div class="nav-right">

                @if (Route::has('login'))

                    @auth
                        <a href="{{ url('/dashboard') }}">
                            Dashboard
                        </a>
                    @else

                            <a href="{{ route('login') }}" class="login">
                                login
                            </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="register">
                                Register
                            </a>
                        @endif

                    @endauth

                @endif

            </div>

        </nav>

    </div>


    <!-- Main -->
    <main class="main">

        <div class="card">

            <!-- Left Content -->
            <div class="content">

                <h1>
                    Let's get started
                </h1>

                <p>
                    Laravel has an incredibly rich ecosystem.
                </p>

                <p>
                    We suggest starting with the following.
                </p>

                <div class="links">

                    <a href="https://laravel.com/docs">
                        ◉ &nbsp; Read the Documentation
                    </a>

                    <a href="https://laracasts.com">
                        ◉ &nbsp; Watch video tutorials at Laracasts
                    </a>

                </div>

                <a href="https://cloud.laravel.com" class="button">
                    Deploy now
                </a>

            </div>


            <!-- Right Illustration -->
            <div class="illustration">

                <div class="laravel-text">
                    Laravel
                </div>

                <div class="shape"></div>

            </div>

        </div>

    </main>

</body>

</html>