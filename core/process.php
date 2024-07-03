<?php
// Подключение файла инициализации
include '../init.php';

// Проверка, авторизован ли пользователь
if ($users->signed_in()) {
    // Определение действия на основе значения 'func' из POST-запроса
    switch ($_POST['func']) {
        // Ответ на тикет
        case 'reply':
            $tickets->reply($_COOKIE['user'], $_POST['ticket'], $_POST['text'], $_FILES);
            break;
        // Получение ответов на тикет
        case 'replies':
            $tickets->ticket_replies($_POST['ticket']);
            break;
        // Создание нового тикета
        case 'create_ticket':
            $tickets->create($_POST['subject'], $_POST['theme'], $_POST['message'], $_FILES);
            break;
        // Отметка тикета как не требующего больше помощи
        case 'no_longer_help':
            $tickets->ticket_resolved($_POST['ticket']);
            break;
        // Закрытие тикета
        case 'close_ticket':
            $tickets->close_ticket($_POST['ticket']);
            break;
        // Обновление имени темы
        case 'update_themes_name':
            $tickets->update_themes_name($_POST['id'], $_POST['name']);
            break;
        // Обновление информации о теме
        case 'update_themes_info':
            $tickets->update_themes_info($_POST['id'], $_POST['info']);
            break;
        // Добавление новой темы
        case 'add_theme':
            $tickets->add_theme();
            break;
        // Загрузка файла тикета
        case 'file-download':
            $tickets->send_ticket_files($_POST['name'], $_POST['file']);
            break;
        // Изменение темы тикета
        case 'change_theme':
            $tickets->change_theme($_POST['id'], $_POST['theme']);
            break;
        // Удаление темы
        case 'delete_themes':
            $tickets->delete_themes($_POST['id']);
            break;
        // Добавление пользователя к теме
        case 'add_user_to_theme':
            $tickets->add_user_to_theme($_POST['id'], $_POST['username']);
            break;
        // Удаление пользователя из темы
        case 'delete_user_from_theme':
            $tickets->delete_user_from_theme($_POST['iduser'], $_POST['idtheme']);
            break;
    }
} else {
    // Если пользователь не авторизован, проверка авторизации
    if ($_POST['func'] == 'auth') {
        $users->auth($_POST['name'], $_POST['password']);
    }
}