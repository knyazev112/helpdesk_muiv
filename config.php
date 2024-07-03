<?php
//
// Настройки используемой базы данных для заявок.
//

$host = "127.0.0.1"; 
$port ="3306"; 
$username = "root";
$password = "";

//
// Настройки базы данных пользователей
//
$students_db = new stdClass();
$students_db->host = "127.0.0.1";
$students_db->port = "3306";
$students_db->username = "root";
$students_db->password = "";
$students_db->db_name = "moodle";
$students_db->table_prefix = "mdl_"; // Префикс таблиц в базе данных пользователей.

$files_path = '../uploads/';    // Путь к директории для загрузки файлов.

// База данных и необходимые таблицы будут созданы автоматически при первом открытии index.php

$page_title = "Центр поддержки ЧОУ ВО «Московский университет им. С.Ю. Витте»"; // Название страниц сайта в заголовке браузера.
$website_url = "http://knyazev.tech/"; // Ссылка главную страницу сервиса. Нужно начинать с http:// или https:// и использовать слеш (/) в конце.

