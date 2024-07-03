<?php

class db
{
    /**
     * Получить соединение с базой данных для основной системы.
     *
     * @return PDO Объект PDO для работы с базой данных.
     */
    public function getDBH()
    {
        // Статическая переменная для хранения экземпляра PDO.
        static $DBH = null;

        // Проверка, создано ли соединение.
        if (is_null($DBH)) {
            try {
                // Создание нового экземпляра PDO.
                $DBH = new PDO(
                    'mysql:host=' . HOST . ';port=' . PORT . ';',
                    USER,
                    PASSWORD,
                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                );

                // Выполнение SQL запросов для настройки базы данных.
                $DBH->query("CREATE DATABASE IF NOT EXISTS support");
                $DBH->query("use support");
                $DBH->query("/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
                             /*!40101 SET NAMES utf8 */;
                             /*!50503 SET NAMES utf8mb4 */;
                             /*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
                             /*!40103 SET TIME_ZONE='+00:00' */;
                             /*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
                             /*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
                             /*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

                             CREATE TABLE IF NOT EXISTS `themes` (
                                `id` int NOT NULL AUTO_INCREMENT,
                                `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                                `info` text NOT NULL,
                                PRIMARY KEY (`id`) USING BTREE,
                                KEY `Индекс 2` (`name`)
                             ) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

                             CREATE TABLE IF NOT EXISTS `tickets` (
                                `id` int NOT NULL AUTO_INCREMENT,
                                `uniqid` varchar(20) NOT NULL,
                                `user` int NOT NULL,
                                `title` varchar(250) NOT NULL,
                                `init_msg` text NOT NULL,
                                `theme` int NOT NULL,
                                `date` varchar(250) NOT NULL,
                                `last_reply` int NOT NULL,
                                `resolved` int NOT NULL,
                                `files` text NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `Индекс 2` (`uniqid`),
                                KEY `Индекс 3` (`user`),
                                KEY `Индекс 4` (`theme`)
                             ) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

                             CREATE TABLE IF NOT EXISTS `ticket_replies` (
                                `id` int NOT NULL AUTO_INCREMENT,
                                `user` int NOT NULL,
                                `text` text NOT NULL,
                                `ticket_id` text NOT NULL,
                                `date` varchar(20) NOT NULL,
                                `files` text CHARACTER SET utf8mb4 COLLATE=utf8mb4_0900_ai_ci NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `Индекс 2` (`ticket_id`(100)),
                                KEY `Индекс 3` (`user`)
                             ) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

                             CREATE TABLE IF NOT EXISTS `user_rights` (
                                `id` int NOT NULL AUTO_INCREMENT,
                                `themes_id` int NOT NULL,
                                `user_id` int NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `Индекс 2` (`themes_id`),
                                KEY `Индекс 3` (`user_id`)
                             ) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

                             /*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
                             /*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
                             /*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
                             /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
                             /*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */");
            } catch (PDOException $ex) {
                die(); // Обработка ошибки соединения с базой данных.
            }
        }
        return $DBH;
    }

    /**
     * Получить соединение с базой данных для пользователей.
     *
     * @return PDO Объект PDO для работы с базой данных пользователей.
     */
    public function getDBS()
    {
        // Статическая переменная для хранения экземпляра PDO.
        static $DBS = null;

        // Проверка, создано ли соединение.
        if (is_null($DBS)) {
            try {
                // Создание нового экземпляра PDO.
                $DBS = new PDO(
                    'mysql:host=' . STUD_HOST . ';port=' . STUD_PORT . ';',
                    STUD_USER,
                    STUD_PASSWORD,
                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                );

                // Указание используемой базы данных.
                $DBS->query("use " . STUD_DB_NAME);
            } catch (PDOException $ex) {
                die(); // Обработка ошибки соединения с базой данных.
            }
        }
        return $DBS;
    }
}
