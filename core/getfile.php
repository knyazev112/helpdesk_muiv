<?php

include '../config.php';

// Получение пути к файлу из параметров запроса
$file = $files_path . $_GET['id'];

// Проверка существования файла
if (file_exists($file)) {
  // Сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
  // Если этого не сделать, файл будет читаться в память полностью!
  if (ob_get_level()) {
    ob_end_clean();
  }

  // Заставляем браузер показать окно сохранения файла
  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename=' . $_GET['file']);
  header('Content-Transfer-Encoding: binary');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($file));

  // Читаем файл и отправляем его пользователю
  readfile($file);
  exit;
}
