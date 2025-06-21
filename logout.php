<?php
// perform_logout.php
require_once 'db_config.php'; // Для доступа к AUTH_COOKIE_NAME
session_start();

// Удаляем cookie, устанавливая время жизни в прошлом
setcookie(AUTH_COOKIE_NAME, '', time() - (3600 * 24), "/"); // На сутки назад

// Очищаем переменные сессии (если использовались)
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

$_SESSION['flash_message_logout'] = "Вы успешно вышли из системы."; // Можно использовать на странице логина
header("Location: user_login.php"); // Перенаправляем на страницу входа
exit;
?>