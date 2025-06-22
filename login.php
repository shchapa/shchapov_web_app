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
