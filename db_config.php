<?php
// db_config.php

// Параметры подключения к базе данных
define('DB_HOST', 'database_server'); // Имя сервиса MySQL из docker-compose.yml
define('DB_USERNAME', 'app_user');    // Имя пользователя БД, созданного в docker-compose
define('DB_PASSWORD', 'UserPasswordForApp321'); // Пароль пользователя БД
define('DB_NAME', 'shchapov_webapp_db');    // Имя базы данных

// Имя для Cookie сессии
define('AUTH_COOKIE_NAME', 'ActiveUserSession');

// Функция для установки соединения с БД
function establish_db_connection() {
    $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD); // Сначала подключаемся без указания БД

    if (!$conn) {
        // Не выводим детали ошибки пользователю в продакшене, но для отладки можно:
        // die("Ошибка подключения к серверу MySQL: " . mysqli_connect_error());
        error_log("DB Connection Error: " . mysqli_connect_error()); // Логируем ошибку
        die("Сервис временно недоступен. Попробуйте позже. [Code: DB_CONN_FAIL]");
    }

    // Пытаемся создать БД, если она не существует (это может делать root при инициализации,
    // но здесь для примера, что app_user может потребоваться CREATE DATABASE privilege,
    // что не всегда хорошо. Лучше, чтобы БД создавалась при старте контейнера MySQL)
    // Для нашего случая, docker-compose создаст БД. Поэтому этот блок можно убрать или закомментировать.
    /*
    $db_creation_query = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if (!mysqli_query($conn, $db_creation_query)) {
        error_log("DB Creation Error: " . mysqli_error($conn));
        // die("Не удалось создать базу данных: " . mysqli_error($conn));
    }
    */
    
    // Выбираем базу данных
    if (!mysqli_select_db($conn, DB_NAME)) {
        error_log("DB Selection Error: " . mysqli_error($conn));
        mysqli_close($conn); // Закрываем соединение, если БД не выбрана
        die("Сервис временно недоступен. Ошибка выбора БД. [Code: DB_SEL_FAIL]");
    }
    
    mysqli_set_charset($conn, "utf8mb4"); // Устанавливаем кодировку для соединения
    return $conn;
}

// Функция для инициализации таблицы (можно вызывать один раз или проверять)
function initialize_user_table($connection) {
    $create_table_sql = "
    CREATE TABLE IF NOT EXISTS client_accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_email VARCHAR(255) NOT NULL UNIQUE,
        client_login VARCHAR(50) NOT NULL UNIQUE,
        client_secret VARCHAR(255) NOT NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if (!mysqli_query($connection, $create_table_sql)) {
        error_log("Table Creation Error: " . mysqli_error($connection));
        // die("Ошибка создания таблицы client_accounts: " . mysqli_error($connection));
        return false;
    }
    return true;
}

?>