<?php

// Начало буферизации вывода

ob_start();

// Отключение отображения ошибок

ini_set('display_errors', 0);

// Подключение файла конфигурации

include 'config.php';

// Определение констант

define('WEBSITE_URL', $website_url);

define('HOST', $host);
define('PORT', $port);
define('USER', $username);
define('PASSWORD', $password);

define('STUD_HOST', $students_db->host);
define('STUD_PORT', $students_db->port);
define('STUD_USER', $students_db->username);
define('STUD_PASSWORD', $students_db->password);
define('STUD_DB_NAME', $students_db->db_name);
define('STUD_TABLE_PREFIX', $students_db->table_prefix);

// Определение констант для пути загрузки файлов и URL сайта

define('FILES_PATH', $files_path);

// Подключение основных файлов системы

require 'core/db.php';
require 'core/users.php';
require 'core/time.php';
require 'core/tickets.php';

// Инициализация объектов классов для работы с базой данных, пользователями, временем и заявками

$database = new db;
$users = new users;
$time = new time;
$tickets = new tickets;

