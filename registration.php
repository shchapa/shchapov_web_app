<?php
require_once 'db_config.php'; // Подключаем конфигурацию БД

session_start(); // Используем сессии для флеш-сообщений (опционально, но делает код чище)

$db_conn = establish_db_connection();
initialize_user_table($db_conn); // Убедимся, что таблица существует

// Если пользователь уже авторизован, перенаправляем на страницу профиля
if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    header("Location: user_profile.php"); // Изменил имя файла профиля
    exit;
}

$registration_error = '';
$registration_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_action'])) {
    $submitted_email = trim($_POST['signup_email']);
    $submitted_login = trim($_POST['signup_login']);
    $submitted_pass = $_POST['signup_password']; // Пароль не тримим

    // Простая валидация на стороне сервера
    if (empty($submitted_email) || empty($submitted_login) || empty($submitted_pass)) {
        $registration_error = 'Все поля обязательны для заполнения!';
    } elseif (!filter_var($submitted_email, FILTER_VALIDATE_EMAIL)) {
        $registration_error = 'Некорректный формат email адреса.';
    } elseif (strlen($submitted_login) < 3) {
        $registration_error = 'Логин должен содержать не менее 3 символов.';
    } elseif (strlen($submitted_pass) < 6) {
        $registration_error = 'Пароль должен быть не менее 6 символов.';
    } else {
        // Проверка, существует ли уже пользователь с таким email или login
        // Для безопасности используем подготовленные выражения, НО для задания здесь оставим SQLi-подобную конструкцию
        // $check_sql = "SELECT id FROM client_accounts WHERE client_email = '$submitted_email' OR client_login = '$submitted_login'";
        // Для демонстрации уязвимости в регистрации (хотя основная в логине)
        
        // ВАЖНО: Хеширование паролей ОБЯЗАТЕЛЬНО в реальных приложениях!
        // $hashed_password = password_hash($submitted_pass, PASSWORD_DEFAULT);
        // Сейчас для простоты и уязвимости храним как есть.

        // УЯЗВИМАЯ ЧАСТЬ (как и в оригинале, но с новыми именами)
        $insert_query = "INSERT INTO client_accounts (client_email, client_login, client_secret) 
                         VALUES ('$submitted_email', '$submitted_login', '$submitted_pass')";

        if (mysqli_query($db_conn, $insert_query)) {
            // Успешная регистрация
            setcookie(AUTH_COOKIE_NAME, $submitted_login, time() + (3600 * 2), "/"); // Cookie на 2 часа
            $_SESSION['flash_message'] = "Регистрация прошла успешно! Добро пожаловать, $submitted_login!";
            header('Location: user_profile.php'); // Перенаправляем на профиль
            exit;
        } else {
            // Ошибка MySQL (например, дубликат email или login)
            if (mysqli_errno($db_conn) == 1062) { // Код ошибки для дублирующейся записи
                 $registration_error = "Пользователь с таким email или логином уже существует.";
            } else {
                 $registration_error = "Ошибка регистрации. Пожалуйста, попробуйте позже. Details: " . mysqli_error($db_conn);
            }
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
    <title>Создание аккаунта</title> <!-- Изменен title -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"> <!-- Другой CDN Bootstrap -->
    <style>
        body { background-color: #eef1f5; font-family: 'Arial', sans-serif; }
        .form-container { margin-top: 60px; max-width: 480px; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-container h2 { text-align: center; margin-bottom: 25px; color: #333; }
        .form-control { border-radius: 4px; padding: 12px; }
        .form-control:focus { border-color: #5892fc; box-shadow: 0 0 0 0.2rem rgba(88,146,252,.25); }
        .btn-custom-primary { background-color: #5892fc; border-color: #5892fc; color: white; padding: 12px; font-weight: bold; }
        .btn-custom-primary:hover { background-color: #4079d4; border-color: #3b6fc3; }
        .alert-custom { padding: 12px; border-radius: 4px; }
        .footer-link { text-align: center; margin-top: 20px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container mx-auto">
            <h2>Регистрация нового пользователя</h2>

            <?php if (!empty($registration_error)): ?>
                <div class="alert alert-danger alert-custom" role="alert">
                    <?= htmlspecialchars($registration_error) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($registration_success)): // Не используется, так как редирект ?>
                <div class="alert alert-success alert-custom" role="alert">
                    <?= htmlspecialchars($registration_success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="mb-3">
                    <label for="signupEmail" class="form-label">Ваш Email:</label> <!-- Изменены for и name -->
                    <input type="email" name="signup_email" id="signupEmail" class="form-control" placeholder="например, user@example.com" required>
                </div>
                <div class="mb-3">
                    <label for="signupLogin" class="form-label">Желаемый логин:</label>
                    <input type="text" name="signup_login" id="signupLogin" class="form-control" placeholder="Минимум 3 символа" required>
                </div>
                <div class="mb-3">
                    <label for="signupPassword" class="form-label">Придумайте пароль:</label>
                    <input type="password" name="signup_password" id="signupPassword" class="form-control" placeholder="Минимум 6 символов" required>
                </div>
                <button type="submit" name="register_action" class="btn btn-custom-primary w-100">Зарегистрироваться</button>
            </form>
            <div class="footer-link">
                <p>Уже зарегистрированы? <a href="user_login.php">Войти в систему</a></p> <!-- Изменено имя файла логина -->
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>