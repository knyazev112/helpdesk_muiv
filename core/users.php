<?php

class users extends db
{
    public static $user;

    public function auth($user, $password)
    {

        $_link = $this->getDBS();

        if ($user != '' && $password != '') {

            $query = $_link->prepare('SELECT * FROM `' . STUD_TABLE_PREFIX . 'user' . '` WHERE `username` = :user');
            $query->bindParam(':user', $user, PDO::PARAM_STR);
            $query->execute();

            $res = $query->fetch(PDO::FETCH_ASSOC);

            if ($query->rowCount() > 0) {

                if (password_verify($password, $res['password'])) {
                    if ($res['deleted'] != 0 and $res['suspended'] != 0) {
                        echo 'Доступ запрещен.';
                    } else {
                        setcookie('user', $res['id'], time() + 999999, '/');
                        setcookie('id', password_hash($res['password'], PASSWORD_DEFAULT), time() + 999999, '/');
                        echo 'success';
                    }
                } else {
                    echo 'Неверный пароль';
                }
            } else {
                echo 'Пользователь не найден';
            }
        } else {
            echo 'Заполните все поля.';
        }
    }

    public function signed_in()
    {
        $_link = $this->getDBS();
        if (isset($_COOKIE['user']) and isset($_COOKIE['id'])) {
            $query = $_link->prepare('SELECT * FROM `' . STUD_TABLE_PREFIX . 'user' . '` WHERE `id` = :user');
            $query->bindParam(':user', $_COOKIE['user'], PDO::PARAM_STR);
            $query->execute();
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            if (count($res) > 0) {
                if (password_verify($res[0]['password'], $_COOKIE['id'])) {
                    if ($res[0]['deleted'] != 0 and $res[0]['suspended'] != 0) {
                        return false;
                    } else {
                        setcookie('user', $_COOKIE['user'], time() + 999999, '/');
                        setcookie('id', password_hash($res[0]['password'], PASSWORD_DEFAULT), time() + 999999, '/');
                        self::$user = $res[0];
                        if ($res[0]['username'] == 'admin') {
                            self::$user['user_rights'] = "Администратор системы";
                        } else {
                            $query = $_link->prepare('SELECT rol.archetype FROM ' . STUD_TABLE_PREFIX . 'role rol JOIN '
                                . STUD_TABLE_PREFIX . 'role_assignments assi ON rol.id = assi.roleid WHERE userid = :userid');
                            $query->bindParam(':userid', self::$user['id'], PDO::PARAM_INT);
                            $query->execute();
                            $role = $query->fetch(PDO::FETCH_ASSOC)['archetype'];

                            if ($role == 'manager') {
                                self::$user['user_rights'] = "Администратор системы";
                            } elseif ($role == 'coursecreator' or $role == 'editingteacher' or $role == 'teacher') {
                                self::$user['user_rights'] = "Менеджер";
                            } elseif ($role == 'student') {
                                self::$user['user_rights'] = "Студент";
                            } else {
                                self::$user['user_rights'] = "Гость";
                            }
                        }
                        return true;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function get_user_info($field)
    {
        return self::$user[$field];

    }
    public function id_to_column($id, $column)
    {
        $_link = $this->getDBS();

        $query = $_link->prepare('SELECT * FROM `' . STUD_TABLE_PREFIX . 'user' . '` WHERE `id` = :user');
        $query->bindParam(':user', $id, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result[$column];
    }
    public function get_id_from_username($username)
    {
        $_link = $this->getDBS();
        if (isset($username)) {
            $query = $_link->prepare('SELECT * FROM `' . STUD_TABLE_PREFIX . 'user' . '` WHERE `username` = :user');
            $query->bindParam(':user', $username, PDO::PARAM_STR);
            $query->execute();
            $res = $query->fetch(PDO::FETCH_ASSOC);
            if ($query->rowCount() > 0) {
                if ($res['deleted'] == 0) {
                    return $res['id'];
                }
            }
        }
        return false;
    }
    public function get_user_group($id)
    {
        $_link = $this->getDBS();
        $query = $_link->prepare('SELECT * FROM `' . STUD_TABLE_PREFIX . 'user' . '` WHERE `id` = :user');
        $query->bindParam(':user', $id, PDO::PARAM_STR);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_ASSOC);
        if ($query->rowCount() > 0) {
            $res;
            if ($res['username'] == 'admin') {
                return "Администратор системы";
            } else {
                $query = $_link->prepare('SELECT rol.archetype FROM ' . STUD_TABLE_PREFIX . 'role rol JOIN '
                    . STUD_TABLE_PREFIX . 'role_assignments assi ON rol.id = assi.roleid WHERE userid = :userid');
                $query->bindParam(':userid', $res['id'], PDO::PARAM_INT);
                $query->execute();
                $role = $query->fetch(PDO::FETCH_ASSOC)['archetype'];
                if ($role == 'manager') {
                    return "Администратор системы";
                } elseif ($role == 'coursecreator' or $role == 'editingteacher' or $role == 'teacher') {
                    return "Менеджер";
                } elseif ($role == 'student') {
                    return "Студент";
                } else {
                    return "Гость";
                }
            }
        } else {
            return false;
        }
    }

    public function get_themes_table()
    {
        $_link = $this->getDBH();
        $query = $_link->query('SELECT DISTINCT user_id FROM `user_rights`');
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $users_id = array();
        $users = array();
        foreach ($result as $v) {
            $users_id[] = $v['user_id'];
        }
        $placeholders = implode(',', array_fill(0, count($users_id), '?'));
        $_link_users_db = $this->getDBS();
        $query = $_link_users_db->prepare("SELECT id, username, firstname, lastname, department FROM `" . STUD_TABLE_PREFIX . 'user' . "` WHERE id IN ($placeholders)");
        $query->execute($users_id);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $v) {
            $query = $_link_users_db->prepare('SELECT rol.archetype FROM ' . STUD_TABLE_PREFIX . 'role rol JOIN '
                . STUD_TABLE_PREFIX . 'role_assignments assi ON rol.id = assi.roleid WHERE userid = :userid');
            $query->bindParam(':userid', $v['id'], PDO::PARAM_INT);
            $query->execute();
            $role = $query->fetch(PDO::FETCH_ASSOC);
            if (is_array($role)) {
                $role = $role['archetype'];
            }
            $user = array('id' => $v['id'], 'username' => $v['username'], 'firstname' => $v['firstname'], 'lastname' => $v['lastname'], 'department' => $v['department']);
            if ($role == 'manager') {
                $user['role'] = "Администратор системы";
            } elseif ($role == 'coursecreator' or $role == 'editingteacher' or $role == 'teacher') {
                $user['role'] = "Менеджер";
            } elseif ($role == 'student') {
                $user['role'] = "Студент";
            } elseif ($role === false) {
                $user['role'] = "Роль не назначена";
            } else {
                $user['role'] = "Гость";
            }
            $users[$v['id']] = $user;
        }
        $query = $_link->query("SELECT t.id AS theme_id, t.name AS theme_name, GROUP_CONCAT(ur.user_id ORDER BY ur.user_id SEPARATOR ', ')"
            . " AS user_ids FROM `themes` t LEFT JOIN `user_rights` ur ON t.id = ur.themes_id GROUP BY t.id, t.name;");
        $query->execute();
        while ($result = $query->fetch(PDO::FETCH_ASSOC)):
            echo '<tr><td scope="row">ID: ' . $result['theme_id'] . '<br><p>' . $result['theme_name'] . '</p></td><td>';
            $user_id = explode(", ", $result['user_ids']);
            foreach ($user_id as $k => $v) {
                if (array_key_exists($v, $users)) {
                    echo '<div class="card my-2"><div class="card-body">';
                    echo '<p class="m-0">Логин: ' . $users[$v]['username'] . '</p>';
                    echo '<p class="m-0">Ф.И.О.: ' . $users[$v]['firstname'] . ' ' . $users[$v]['lastname'] . '</p>';
                    echo '<p class="m-0">Подразделение: ' . $users[$v]['department'] . '</p> ';
                    echo '<p class="m-0">Роль: ' . $users[$v]['role'] . '</p>';
                    echo '<p class="m-0"><a href="#" class="del-user-from-theme m-0" data-IdUser="' . $users[$v]['id'] . '" data-IdTheme="' . $result['theme_id'] . '">Удалить пользователя</a></p>';
                    echo '</div></div>';
                }
            }
            echo '<form class="row g-3"><div class="col-auto"><input type="text" class="form-control" id="input-user-to-theme-' . $result['theme_id'] . '" placeholder="Введите логин"> </div>';
            echo '<div class="col-auto"><button type="submit" class="btn btn-outline-primary mb-3 button-add-user-to-theme" data-IdTheme="' . $result['theme_id'] . '">Добавить пользователя</button></div></form>';
            echo '</td></tr>';
        endwhile;
    }
}