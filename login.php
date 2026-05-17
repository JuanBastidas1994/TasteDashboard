<?php
require_once "funciones.php";
require_once "clases/cl_usuarios.php";

$session = getSession();
$usuario = new cl_usuarios(NULL);

if(isLogin()){
    header("location:index.php");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Login | Taste</title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,400;0,600;0,700;0,800;0,900;1,900&display=swap" rel="stylesheet">
    <link href="plugins/sweetalerts/sweetalert2-v11.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/sweetalerts/sweetalert-customjc.css" rel="stylesheet" type="text/css" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            background-image: url('assets/img/bg-login.png');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 100vh;
            padding: 40px 60px;
        }

        /* Left slogan — hidden on mobile */
        .login-slogan {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-right: 40px;
        }

        .login-slogan .slogan-title {
            font-size: 48px;
            font-weight: 800;
            color: #0d1b2a;
            line-height: 1.1;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .login-slogan .slogan-title .todos {
            display: block;
            font-style: italic;
            color: #0d1b2a;
            font-size: 72px;
            line-height: 1;
        }

        .slogan-divider {
            width: 140px;
            height: 5px;
            background: linear-gradient(90deg, #e63c2f, #c0392b);
            border-radius: 3px;
            margin: 12px 0 20px 0;
        }

        .login-slogan .slogan-sub {
            font-size: 16px;
            font-weight: 700;
            color: #0d1b2a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.5;
        }

        .login-slogan .slogan-sub .highlight {
            color: #e63c2f;
        }

        /* Card */
        .login-card-wrap {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #fffaf3;
            border-radius: 20px;
            padding: 40px 44px 36px;
            width: 380px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        }

        .login-card .card-subtitle {
            font-size: 15px;
            font-weight: 600;
            color: #3b3f5c;
            margin-bottom: 10px;
        }

        .login-card .brand-logo {
            max-height: 52px;
            width: auto;
            margin-bottom: 28px;
        }

        /* Inputs */
        .input-field {
            position: relative;
            margin-bottom: 14px;
            text-align: left;
        }

        .input-field .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b6e76;
            width: 18px;
            height: 18px;
            pointer-events: none;
        }

        .input-field .eye-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b6e76;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .input-field input {
            width: 100%;
            padding: 8px 42px 8px 44px;
            border: 3px solid #6b6e76;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Nunito', sans-serif;
            font-weight: 600;
            color: #3b3f5c;
            background: #fff;
            outline: none;
            transition: border-color 0.2s;
        }

        .input-field input::placeholder {
            color: #b0b8d0;
            font-weight: 500;
        }

        .input-field input:focus {
            border-color: #e63c2f;
        }

        /* Button */
        .btnLogin {
            width: 100%;
            padding: 8px;
            background: #e63c2f;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Nunito', sans-serif;
            letter-spacing: 1px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.2s;
        }

        .btnLogin:hover {
            background: #c0392b;
        }

        /* Forgot */
        .forgot-link {
            margin-top: 20px;
            font-size: 13px;
            color: #3b3f5c;
            font-weight: 600;
        }

        .forgot-link a {
            color: #e63c2f;
            font-weight: 700;
            text-decoration: none;
        }

        .forgot-link a:hover {
            text-decoration: underline;
        }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            body {
                background-image: url('assets/img/bg-login-mobile.png');
                background-position: center center;
                align-items: center;
                justify-content: center;
            }

            .login-wrapper {
                padding: 20px;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 0;
            }

            .login-slogan {
                display: none;
            }

            .login-card-wrap {
                width: 100%;
            }

            .login-card {
                width: 80%;
                max-width: 340px;
                padding: 32px 28px 28px;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">

        <!-- <div class="login-slogan">
            <div class="slogan-title">
                EL MUNDIAL<br>LO JUGAMOS<br>
                <span class="todos">TODOS</span>
            </div>
            <div class="slogan-divider"></div>
            <div class="slogan-sub">
                PASIÓN EN LA CANCHA,<br>
                RESULTADOS EN <span class="highlight">TU NEGOCIO</span>
            </div>
        </div> -->

        <div class="login-card-wrap">
            <div class="login-card">
                <p class="card-subtitle">Iniciar sesión</p>
                <img src="./assets/img/logo.png" class="brand-logo" alt="tasté">

                <form method="POST" action="#">

                    <div class="input-field">
                        <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input id="username" name="username" type="text" placeholder="Correo electrónico" autocomplete="username">
                    </div>

                    <div class="input-field">
                        <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input id="password" name="password" type="password" placeholder="Contraseña" autocomplete="current-password">
                        <svg id="toggle-password" class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>

                    <button type="submit" name="btnLogin" class="btnLogin">Ingresar</button>

                    <!-- <p class="forgot-link">¿Olvidaste tu contraseña ? <a href="recuperar_password.php">Haz click aquí</a></p> -->
                </form>
            </div>
        </div>

    </div>

    <?php js_mandatory(); ?>
    <script src="assets/js/pages/login.js?v=5"></script>
    <script>
        document.getElementById('toggle-password').addEventListener('click', function () {
            var input = document.getElementById('password');
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.style.color = isPassword ? '#e63c2f' : '#aab0c3';
        });
    </script>

</body>
</html>
