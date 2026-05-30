<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {

    private static $instance = null;

    public static function connect() {

        if (self::$instance == null) {

            try {

                self::$instance = new PDO(
                    "mysql:host=localhost;dbname=bd_transito;charset=utf8",
                    "root",
                    ""
                );

                self::$instance->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

            } catch(PDOException $e) {

                die("Error BD: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}