<?php

class tickets extends db
{

    public function __construct()
    {
    }

    public function my_tickets()
    {

        $_link = $this->getDBH();
        $time = new time;
        $users = new users;

        $query = $_link->prepare('SELECT * FROM `tickets` WHERE `user` = :user');
        $query->bindParam(':user', $_COOKIE['user'], PDO::PARAM_INT);
        $query->execute();

        while ($result = $query->fetch(PDO::FETCH_ASSOC)):
            echo '<tr>';
            echo '<td><a href="ticket/?id=' . $result['uniqid'] . '">' . $result['title'] . '</a></td>';
            if ($result['resolved'] == 1) {
                echo '<td><span class="text-success">Выполнена</span></td>';
            } else if ($result['resolved'] == 0) {
                echo '<td><i>В работе</i></td>';
            }
            if ($result['last_reply'] == $_COOKIE['user']) {
                echo '<td>-</td>';
            } else {
                echo '<td>' . $users->id_to_column($result['last_reply'], 'firstname') . ' ' . $users->id_to_column($result['last_reply'], 'lastname') . '</td>';
            }
            echo '<td>' . $time->ago($result['date']) . '</td>';
            echo '</tr>';
        endwhile;
    }
    public function my_tickets_manager()
    {

        $_link = $this->getDBH();
        $time = new time;
        $users = new users;
        $user_id = $users->get_user_info("id");
        $query = $_link->query('SELECT `id`, `NAME` FROM `themes`');
        $query->execute();
        $themes_names = array();
        while ($result = $query->fetch(PDO::FETCH_ASSOC)):
            $themes_names[$result['id']] = $result['NAME'];
        endwhile;
        $query = $_link->prepare('SELECT t.* FROM tickets t JOIN user_rights ur ON t.theme = ur.themes_id WHERE ur.user_id = :user AND t.resolved = 0;');
        $query->bindParam(':user', $user_id, PDO::PARAM_INT);
        $query->execute();

        while ($result = $query->fetch(PDO::FETCH_ASSOC)):
            echo '<tr style="word-wrap:break-word;">';
            echo '<td><a href="ticket/?id=' . $result['uniqid'] . '">' . $result['title'] . '</a></td><td>' . $themes_names[$result['theme']] . '</td>';
            if ($result['last_reply'] == $_COOKIE['user']) {
                echo '<td>Вы</td>';
            } else {
                echo '<td>' . $users->id_to_column($result['last_reply'], 'firstname') . ' ' . $users->id_to_column($result['last_reply'], 'lastname') . '</td>';
            }
            echo '<td>' . $time->ago($result['date']) . '</td>';
            echo '</tr>';
        endwhile;

    }
    public function close_ticket($ticket)
    {
        $users = new users;
        $_link = $this->getDBH();
        if ($this->is_ticket($ticket) && $users->get_user_group($_COOKIE['user']) != 'Студент') {
            $query = $_link->prepare('UPDATE `tickets` SET `resolved` = 1 WHERE `uniqid` = :ticket');
            $query->bindParam(':ticket', $ticket, PDO::PARAM_STR);
            $query->execute();
            echo 'success';
        }
    }
    public function change_theme($ticket, $theme)
    {
        $users = new users;
        $_link = $this->getDBH();
        $query = $_link->prepare('SELECT * FROM `themes` WHERE `id` = :uniqid');
        $old_theme_id = $this->ticket_info($ticket, 'theme');
        $query->bindParam(':uniqid', $old_theme_id, PDO::PARAM_INT);
        $query->execute();
        $old_theme_name = $query->fetch(PDO::FETCH_ASSOC)['name'];
        $query = $_link->prepare('SELECT * FROM `themes` WHERE `id` = :uniqid');
        $query->bindParam(':uniqid', $theme, PDO::PARAM_INT);
        $query->execute();
        $theme_name = $query->fetch(PDO::FETCH_ASSOC)['name'];
        $text = 'Пользователем: ' . $users->get_user_info("firstname") . ' ' . $users->get_user_info("lastname") . ' (' . $users->get_user_info("department") . ') ';
        $text .= 'изменена тема заявки с "' . $old_theme_name . '" на "' . $theme_name . '"';
        $this->reply($users->get_user_info("id"), $ticket, $text, false);
        $query = $_link->prepare('UPDATE `tickets` SET `theme` = :theme WHERE `uniqid` = :uniqid');
        $query->bindParam(':uniqid', $ticket, PDO::PARAM_STR);
        $query->bindParam(':theme', $theme, PDO::PARAM_STR);
        $query->execute();
    }
    public function ticket_info($ticket, $field)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('SELECT * FROM `tickets` WHERE `uniqid` = :uniqid');
        $query->bindParam(':uniqid', $ticket, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result[$field];
    }
    public function ticket_file_info($ticket)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('SELECT * FROM `tickets` WHERE `uniqid` = :uniqid');
        $query->bindParam(':uniqid', $ticket, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $files = json_decode($result['files']);
        if (!is_array($files)) {
            return false;
        }
        $ret = '';
        foreach ($files as $v) {
            $ret .= '<a href="' . WEBSITE_URL . 'core/getfile.php?id=' . $v[0] . '&file=' . $v[1] . '" class="link-file-download">' . $v[1] . '</a><br>';
        }
        return $ret;
    }
    public function reply_file_info($reply)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('SELECT * FROM `ticket_replies` WHERE `id` = :uniqid');
        $query->bindParam(':uniqid', $reply, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $files = json_decode($result['files']);
        if (!is_array($files)) {
            return false;
        }
        $ret = '';
        foreach ($files as $v) {
            $ret .= '<a href="' . WEBSITE_URL . 'core/getfile.php?id=' . $v[0] . '&file=' . $v[1] . '" class="link-file-download">' . $v[1] . '</a><br>';
        }
        return $ret;
    }

    public function is_ticket($ticket)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('SELECT * FROM `tickets` WHERE `uniqid` = :uniqid');
        $query->bindParam(':uniqid', $ticket, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function my_ticket($ticket)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('SELECT * FROM `tickets` WHERE `uniqid` = :uniqid');
        $query->bindParam(':uniqid', $ticket, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result['user'] == $_COOKIE['user']) {
            return true;
        } else {
            return false;
        }
    }
    public function get_ticket_field($ticket, $field)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('SELECT * FROM `tickets` WHERE `uniqid` = :uniqid');
        $query->bindParam(':uniqid', $ticket, PDO::PARAM_STR);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)[$field];
    }

    public function reply($user, $ticket, $text, $files)
    {
        $_link = $this->getDBH();
        $deny = array(
            'phtml',
            'php',
            'php3',
            'php4',
            'php5',
            'php6',
            'php7',
            'phps',
            'cgi',
            'pl',
            'asp',
            'aspx',
            'shtml',
            'shtm',
            'htaccess',
            'htpasswd',
            'ini',
            'log',
            'sh',
            'js',
            'html',
            'htm',
            'css',
            'sql',
            'spl',
            'scgi',
            'fcgi',
            'exe'
        );
        $path = '../uploads/';
        $input_name = 'file';
        $file_str_db = '';
        $files_names = array();
        if (!isset($_FILES[$input_name])) {
            $error = 'Файлы не загружены.';
        } else {
            // Преобразуем массив $_FILES в удобный вид для перебора в foreach.
            $files = array();
            $diff = count($_FILES[$input_name]) - count($_FILES[$input_name], COUNT_RECURSIVE);
            if ($diff == 0) {
                $files = array($_FILES[$input_name]);
            } else {
                foreach ($_FILES[$input_name] as $k => $l) {
                    foreach ($l as $i => $v) {
                        $files[$i][$k] = $v;
                    }
                }
            }

            foreach ($files as $file) {
                // Проверим на ошибки загрузки.
                if (!empty($file['error']) || empty($file['tmp_name'])) {
                    $res = 'Не удалось загрузить файл.';
                } elseif ($file['tmp_name'] == 'none' || !is_uploaded_file($file['tmp_name'])) {
                    $res = 'Не удалось загрузить файл.';
                } else {
                    // Оставляем в имени файла только буквы, цифры и некоторые символы.
                    $pattern = "[^a-zа-яё0-9,~!@#%^-_\$\?\(\)\{\}\[\]\.]";
                    $name = mb_eregi_replace($pattern, '-', $file['name']);
                    $name = mb_ereg_replace('[-]+', '-', $name);
                    $parts = pathinfo($name);

                    if (empty($name) || empty($parts['extension'])) {
                        $res = 'Недопустимый тип файла';
                    } elseif (!empty($allow) && !in_array(strtolower($parts['extension']), $allow)) {
                        $res = 'Недопустимый тип файла';
                    } elseif (!empty($deny) && in_array(strtolower($parts['extension']), $deny)) {
                        $res = 'Недопустимый тип файла';
                    } else {
                        // Перемещаем файл в директорию.
                        $filename = date("Y-m-d H-i-s") . '_' . rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999);
                        if (move_uploaded_file($file['tmp_name'], FILES_PATH . $filename)) {
                            // Далее можно сохранить название файла в БД и т.п.
                            $files_names[] = array($filename, $name);
                        } else {
                            $res = 'Не удалось загрузить файл.';
                        }
                    }
                }

            }
        }
        if (isset($res)) {
            echo '<div class="alert error">Ошибка загрузки файла</div>';
            die();
        }
        $text = strip_tags($text);
        if (trim($text) != '') {
            $date = new DateTime();
            $date = $date->getTimestamp();
            $file_str_db = json_encode($files_names);
            $query = $_link->prepare('INSERT INTO `ticket_replies`() VALUES(NULL, :user, :text, :ticket, :date, :file)');
            $query->bindParam(':user', $_COOKIE['user'], PDO::PARAM_INT);
            $query->bindParam(':text', $text, PDO::PARAM_STR);
            $query->bindParam(':ticket', $ticket, PDO::PARAM_STR);
            $query->bindParam(':date', $date, PDO::PARAM_STR);
            $query->bindParam(':file', $file_str_db, PDO::PARAM_STR);
            $query->execute();

            $query = $_link->prepare('UPDATE `tickets` SET `last_reply` = :id, `resolved` = 0 WHERE `uniqid` = :uniqid');
            $query->bindParam(':id', $_COOKIE['user'], PDO::PARAM_INT);
            $query->bindParam(':uniqid', $ticket, PDO::PARAM_STR);
            $query->execute();

            echo 'success 1';
        } else {
            echo '<div class="alert error">Please enter a comment.</div>';
        }

    }

    public function ticket_replies($ticket)
    {

        $_link = $this->getDBH();

        $query = $_link->prepare('SELECT * FROM `ticket_replies` WHERE `ticket_id` = :uniqid');
        $query->bindParam(':uniqid', $ticket, PDO::PARAM_STR);
        $query->execute();
        $tickets = array();
        while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $tickets[] = $result;
        }
        return $tickets;
    }

    public function get_themes()
    {

        $_link = $this->getDBH();

        $query = $_link->query('SELECT * FROM `themes`');
        $query->execute();

        while ($result = $query->fetch(PDO::FETCH_ASSOC)):

            echo '<option value="' . $result['id'] . '">' . $result['name'] . '</option>';

        endwhile;
    }
    public function get_themes_table()
    {
        $_link = $this->getDBH();
        $query = $_link->query('SELECT * FROM `themes`');
        $query->execute();
        while ($result = $query->fetch(PDO::FETCH_ASSOC)):
            echo '<tr><th scope="row">' . $result['id'] . '<br><button type="button" data-id="' . $result['id'] . '" class="btn btn-outline-danger themes-edit"><i class="fa fa-trash" aria-hidden="true"></i></button></th>'
                . '<td><input data-col="name" data-id="' . $result['id'] . '" class="form-control themes-edit" value="' . $result['name'] . '"></input></td>'
                . '<td><textarea data-col="info" data-id="' . $result['id'] . '" class="form-control themes-edit" rows="3">' . $result['info'] . '</textarea></td></tr>';
        endwhile;
    }
    public function delete_themes($id)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('DELETE FROM `themes` WHERE  `id`= :uniqid');
        $query->bindParam(':uniqid', $id, PDO::PARAM_STR);
        $query->execute();
    }
    public function add_theme()
    {
        $this->getDBH()->query("INSERT INTO `themes` (`name`) VALUES ('Новая тема')");
    }
    public function send_ticket_files($name, $file_name)
    {
        $file = FILES_PATH . $file_name;
        if (file_exists($file)) {
            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            // заставляем браузер показать окно сохранения файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $name);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // читаем файл и отправляем его пользователю
            readfile($file);
            exit;
        }
    }

    public function update_themes_name($id, $name)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('UPDATE `themes` SET `name`= :name WHERE `id`=:id');
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->execute();
    }
    public function add_user_to_theme($id, $username)
    {
        $users = new users;
        $user_id = $users->get_id_from_username($username);
        if ($user_id) {
            $_link = $this->getDBH();
            $query = $_link->prepare('SELECT COUNT(*) FROM `user_rights`  WHERE `themes_id` = :id AND `user_id` = :user_id');
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->execute();
            if ($query->fetch(PDO::FETCH_ASSOC)['COUNT(*)'] == 0) {
                $query = $_link->prepare("INSERT INTO `user_rights` (`themes_id`, `user_id`) VALUES (:id, :user_id)");
                $query->bindParam(':id', $id, PDO::PARAM_INT);
                $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $query->execute();
                echo 'success';
            } else {
                echo 'Такой пользователь уже состоит в данной теме.';
            }
        } else {
            echo 'Такой пользователь не существует.';
        }
    }
    public function delete_user_from_theme($id_user, $id_theme)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('DELETE FROM `user_rights` WHERE  `themes_id` = :id_theme AND `user_id` = :id_user');
        $query->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $query->bindParam(':id_theme', $id_theme, PDO::PARAM_INT);
        $query->execute();
    }
    public function update_themes_info($id, $info)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('UPDATE `themes` SET `info`= :info WHERE `id`=:id');
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->bindParam(':info', $info, PDO::PARAM_STR);
        $query->execute();
    }
    public function get_themes_name($ticket)
    {
        $_link = $this->getDBH();
        $query = $_link->prepare('SELECT the.name FROM tickets tik JOIN themes the ON the.id = tik.theme WHERE tik.uniqid = :uniqid');
        $query->bindParam(':uniqid', $ticket, PDO::PARAM_STR);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['name'];
    }
    public function get_themes_info()
    {

        $_link = $this->getDBH();

        $query = $_link->query('SELECT * FROM `themes`');
        $query->execute();

        while ($result = $query->fetch(PDO::FETCH_ASSOC)):

            echo '<span class="hidden" id="theme-info-' . $result['id'] . '">' . $result['info'] . '</span>';

        endwhile;
    }
    public function create($subject, $theme, $message, $files)
    {

        $_link = $this->getDBH();
        $deny = array(
            'phtml',
            'php',
            'php3',
            'php4',
            'php5',
            'php6',
            'php7',
            'phps',
            'cgi',
            'pl',
            'asp',
            'aspx',
            'shtml',
            'shtm',
            'htaccess',
            'htpasswd',
            'ini',
            'log',
            'sh',
            'js',
            'html',
            'htm',
            'css',
            'sql',
            'spl',
            'scgi',
            'fcgi',
            'exe'
        );
        $path = '../uploads/';
        $input_name = 'file';
        $file_str_db = '';
        $files_names = array();
        if (!isset($_FILES[$input_name])) {
            $error = 'Файлы не загружены.';
        } else {
            // Преобразуем массив $_FILES в удобный вид для перебора в foreach.
            $files = array();
            $diff = count($_FILES[$input_name]) - count($_FILES[$input_name], COUNT_RECURSIVE);
            if ($diff == 0) {
                $files = array($_FILES[$input_name]);
            } else {
                foreach ($_FILES[$input_name] as $k => $l) {
                    foreach ($l as $i => $v) {
                        $files[$i][$k] = $v;
                    }
                }
            }

            foreach ($files as $file) {
                // Проверим на ошибки загрузки.
                if (!empty($file['error']) || empty($file['tmp_name'])) {
                    $res = 'Не удалось загрузить файл.';
                } elseif ($file['tmp_name'] == 'none' || !is_uploaded_file($file['tmp_name'])) {
                    $res = 'Не удалось загрузить файл.';
                } else {
                    // Оставляем в имени файла только буквы, цифры и некоторые символы.
                    $pattern = "[^a-zа-яё0-9,~!@#%^-_\$\?\(\)\{\}\[\]\.]";
                    $name = mb_eregi_replace($pattern, '-', $file['name']);
                    $name = mb_ereg_replace('[-]+', '-', $name);
                    $parts = pathinfo($name);

                    if (empty($name) || empty($parts['extension'])) {
                        $res = 'Недопустимый тип файла';
                    } elseif (!empty($allow) && !in_array(strtolower($parts['extension']), $allow)) {
                        $res = 'Недопустимый тип файла';
                    } elseif (!empty($deny) && in_array(strtolower($parts['extension']), $deny)) {
                        $res = 'Недопустимый тип файла';
                    } else {
                        // Перемещаем файл в директорию.
                        $filename = date("Y-m-d H-i-s") . '_' . rand(10000000, 99999999) . rand(10000000, 99999999) . rand(10000000, 99999999);
                        if (move_uploaded_file($file['tmp_name'], FILES_PATH . $filename)) {
                            // Далее можно сохранить название файла в БД и т.п.
                            $files_names[] = array($filename, $name);
                        } else {
                            $res = 'Не удалось загрузить файл.';
                        }
                    }
                }

            }
        }
        if (isset($res)) {
            echo '<div class="alert error">Ошибка загрузки файла</div>';
            die();
        }
        if ($subject != '' && $message != '') {

            $date = new DateTime();
            $date = $date->getTimestamp();
            $uniqid = uniqid();
            $file_str_db = json_encode($files_names);
            $message = strip_tags($message);
            $query = $_link->prepare('INSERT INTO `tickets`() VALUES(NULL, :uniqid, :user, :title, :init_msg, :theme, :date, :last_reply, 0, :file)');
            $query->bindParam(':uniqid', $uniqid, PDO::PARAM_STR);
            $query->bindParam(':user', $_COOKIE['user'], PDO::PARAM_INT);
            $query->bindParam(':title', $subject, PDO::PARAM_STR);
            $query->bindParam(':init_msg', $message, PDO::PARAM_STR);
            $query->bindParam(':theme', $theme, PDO::PARAM_STR);
            $query->bindParam(':date', $date, PDO::PARAM_STR);
            $query->bindParam(':last_reply', $_COOKIE['user'], PDO::PARAM_INT);
            $query->bindParam(':file', $file_str_db, PDO::PARAM_STR);
            $query->execute();

            echo 'success ' . $uniqid;
        } else {
            echo '<div class="alert error">Пожалуйста, заполните все поля</div>';
        }
    }

    public function ticket_resolved($ticket)
    {
        $_link = $this->getDBH();
        if ($this->is_ticket($ticket) && $this->ticket_info($ticket, 'user') == $_COOKIE['user']) {
            $query = $_link->prepare('UPDATE `tickets` SET `resolved` = 1 WHERE `uniqid` = :ticket');
            $query->bindParam(':ticket', $ticket, PDO::PARAM_STR);
            $query->execute();

            echo 'success';
        }
    }

} 