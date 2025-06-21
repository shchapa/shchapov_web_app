<?php
require_once 'db_config.php';
session_start();

$db_conn = establish_db_connection();
// initialize_user_table($db_conn); // Таблица должна быть уже создана при регистрации

if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    header("Location: profile.php");
    exit;
}

$login_error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_action'])) {
    $entered_login = $_POST['auth_login']; // Имена полей формы изменены
    $entered_pass = $_POST['auth_password'];

    if (empty($entered_login) || empty($entered_pass)) {
        $login_error_message = 'Необходимо указать логин и пароль.';
    } else {
        // !!! УЯЗВИМОСТЬ SQL Injection ЗДЕСЬ !!!
        // Это сделано намеренно для задания. В реальных приложениях ИСПОЛЬЗУЙТЕ ПОДГОТОВЛЕННЫЕ ВЫРАЖЕНИЯ!
        $query_str = "SELECT * FROM client_accounts WHERE client_login='$entered_login' AND client_secret='$entered_pass'";
        
        // Для отладки инъекции:
        // echo "<!-- DEBUG SQL: " . htmlspecialchars($query_str) . " -->";

        $query_result = mysqli_query($db_conn, $query_str);

        if ($query_result) {
            if (mysqli_num_rows($query_result) == 1) { // Должен быть только один пользователь
                $user_data = mysqli_fetch_assoc($query_result);
                setcookie(AUTH_COOKIE_NAME, $user_data['client_login'], time() + (3600 * 2), "/");
                $_SESSION['flash_message'] = "С возвращением, " . htmlspecialchars($user_data['client_login']) . "!";
                header('Location: profile.php');
                exit;
            } else {
                $login_error_message = 'Введен неверный логин или пароль. Попробуйте снова.';
            }
        } else {
            // Ошибка в SQL запросе (может быть полезно для отладки SQLi, но скроем от пользователя)
            error_log("Login SQL Error: " . mysqli_error($db_conn) . " Query: " . $query_str);
            $login_error_message = 'Произошла системная ошибка. Пожалуйста, обратитесь в поддержку. [Code: AUTH_SQL_ERR]';
            // В задании требуется показать ошибку:
            // $login_error_message = "Ошибка в SQL запросе: " . mysqli_error($db_conn);
        }
    }
}
mysqli_close($db_conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        .login-panel { margin-top: 80px; max-width: 400px; background: #fff; padding: 25px 30px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .login-panel h2 { text-align: center; margin-bottom: 20px; color: #1c1e21; font-weight: 600; }
        .form-control-custom { border-radius: 6px; padding: 10px 15px; font-size: 1rem; }
        .form-control-custom:focus { border-color: #1877f2; box-shadow: 0 0 0 0.2rem rgba(24,119,242,.25); }
        .btn-login { background-color: #1877f2; border-color: #1877f2; color: white; padding: 10px; font-weight: bold; font-size: 1.05rem; }
        .btn-login:hover { background-color: #166fe5; border-color: #166fe5; }
        .alert-login-error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; padding: 10px 15px; border-radius: 6px; }
        .signup-link-container { text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #dddfe2; }
        .btn-signup-new { background-color: #42b72a; border-color: #42b72a; color: white; padding: 10px 15px; font-weight: bold; text-decoration: none; display: inline-block; border-radius: 6px; }
        .btn-signup-new:hover { background-color: #36a420; border-color: #36a420; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-panel mx-auto">
            <h2>Авторизация</h2>

            <?php if (!empty($login_error_message)): ?>
                <div class="alert alert-login-error" role="alert">
                    <?= htmlspecialchars($login_error_message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="mb-3">
                    <label for="authLogin" class="form-label">Имя пользователя (логин):</label>
                    <input type="text" name="auth_login" id="authLogin" class="form-control form-control-custom" placeholder="Введите ваш логин" required>
                </div>
                <div class="mb-3">
                    <label for="authPassword" class="form-label">Пароль:</label>
                    <input type="password" name="auth_password" id="authPassword" class="form-control form-control-custom" placeholder="Введите ваш пароль" required>
                </div>
                <button type="submit" name="login_action" class="btn btn-login w-100">Войти</button>
            </form>
            <div class="signup-link-container">
                <a href="registration.php" class="btn btn-signup-new">Создать новый аккаунт</a>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
