<?php

namespace System\Database\DBConnection;

use PDO;
use PDOException;

/*
* DBConnection class - only one connection allowed
*/

class DBConnection
{
    private static $dbConnectionInstance = null; // The static variable to store Singleton's instance

    private function __construct()
    {
    } // Close access outside the class

    public static function getDbConnectionInstance()
    {

        if (is_null(self::$dbConnectionInstance)) { // If no instance then make one
            $DBConnectionInstance = new DBConnection();
            self::$dbConnectionInstance = $DBConnectionInstance->dbConnection();
        }
        return self::$dbConnectionInstance;
    }

    private function dbConnection(): PDO
    {
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
        try {
            return new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD, $options);
        } catch (PDOException $exception) {
            echo "error in database connection" . $exception->getMessage();
            exit();
        }
    }

    public static function getNewInsertId()
    {
        return self::getDbConnectionInstance()->lastInsertId();
    }

}