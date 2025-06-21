<?php
require_once 'db_config.php';
session_start(); // Для отображения флеш-сообщений

if (!isset($_COOKIE[AUTH_COOKIE_NAME])) {
    // Если cookie не установлен, перенаправляем на страницу входа
    $_SESSION['flash_message_error'] = "Доступ запрещен. Пожалуйста, авторизуйтесь.";
    header("Location: user_login.php");
    exit;
}

$current_user_login = htmlspecialchars($_COOKIE[AUTH_COOKIE_NAME]);

// Получаем флеш-сообщение, если есть
$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Удаляем сообщение, чтобы оно не показывалось снова
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - <?= $current_user_login ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .navbar-custom { background-color: #343a40; }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link { color: #f8f9fa; }
        .navbar-custom .nav-link:hover { color: #adb5bd; }
        .profile-container { margin-top: 30px; padding: 20px; background-color: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,.1); }
        .welcome-message { font-size: 1.8rem; color: #28a745; } /* Изменен цвет и размер */
        .user-info { margin-top: 15px; font-size: 1.1rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Мой Профиль</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e\");"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Вы вошли как: <strong><?= $current_user_login ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perform_logout.php">Выйти</a> <!-- Изменено имя файла выхода -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="profile-container text-center">
            <?php if ($flash_message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($flash_message) ?>
            </div>
            <?php endif; ?>

            <h1 class="welcome-message">Привет, <?= $current_user_login ?>!</h1> <!-- Заголовок для задания -->
            <p class="user-info">Это ваша личная страница. Здесь будет ваш контент.</p>
            <!-- Дополнительный контент для профиля -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php
require_once 'db_config.php';
session_start(); // Для отображения флеш-сообщений

if (!isset($_COOKIE[AUTH_COOKIE_NAME])) {
    // Если cookie не установлен, перенаправляем на страницу входа
    $_SESSION['flash_message_error'] = "Доступ запрещен. Пожалуйста, авторизуйтесь.";
    header("Location: user_login.php");
    exit;
}

$current_user_login = htmlspecialchars($_COOKIE[AUTH_COOKIE_NAME]);

// Получаем флеш-сообщение, если есть
$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Удаляем сообщение, чтобы оно не показывалось снова
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - <?= $current_user_login ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .navbar-custom { background-color: #343a40; }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link { color: #f8f9fa; }
        .navbar-custom .nav-link:hover { color: #adb5bd; }
        .profile-container { margin-top: 30px; padding: 20px; background-color: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,.1); }
        .welcome-message { font-size: 1.8rem; color: #28a745; } /* Изменен цвет и размер */
        .user-info { margin-top: 15px; font-size: 1.1rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Мой Профиль</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e\");"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Вы вошли как: <strong><?= $current_user_login ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perform_logout.php">Выйти</a> <!-- Изменено имя файла выхода -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="profile-container text-center">
            <?php if ($flash_message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($flash_message) ?>
            </div>
            <?php endif; ?>

            <h1 class="welcome-message">Привет, <?= $current_user_login ?>!</h1> <!-- Заголовок для задания -->
            <p class="user-info">Это ваша личная страница. Здесь будет ваш контент.</p>
            <!-- Дополнительный контент для профиля -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
