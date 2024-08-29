<?php

declare(strict_types=1);

namespace SQLiteDatabase;

class Sqlite
{
    private \SQLite3 $connection;
    private static $database;

    private function __construct()
    {
        $config = Dotenv::load();
        $filename = $config['DB_DATABASE'];
        $flags = $config['DB_FLAGS'];
        $encryptionKey = $config['DB_ENCRYPTION_KEY'];

        $this->connection = new \SQLite3($filename, $flags, $encryptionKey);
    }

    public static function getInstance(): Sqlite
    {
        if (!self::$database instanceof self) {
            self::$database = new self();
        }

        return self::$database;
    }

    private function getConnection(): \SQLite3
    {
        return $this->connection;
    }

    private static function isConnected(): bool
    {
        return self::getInstance()->getConnection() !== null;
    }

    public static function executeQuery(string $sql, array $dataColumns): bool
    {
        if (!self::getInstance()->isConnected()) return false;

        $statement = self::getInstance()->getConnection()->prepare($sql);
        if ($statement === false) return false;

        for ($i = 0; $i < count($dataColumns); $i++) {
            $statement->bindValue($i + 1, $dataColumns[$i]);
        }

        return $statement->execute() !== null;
    }

    /**
     * @return array|false
     */
    public static function fetchAll(string $sql, array $columns = [], int $mode = SQLITE3_ASSOC)
    {
        if (!self::isConnected()) return [];

        $statement = self::getInstance()->getConnection()->prepare($sql);
        if ($statement === false) return [];

        for ($i = 0; $i < count($columns); $i++) {
            $statement->bindValue($i + 1, $columns[$i]);
        }

        $rows = $statement->execute();
        if ($rows === false) return false;

        $models = [];
        while ($row = $rows->fetchArray($mode)) {
            array_push($models, $row);
        }

        $rows->finalize();
        return $models;
    }

    /**
     * @return array|false
     */
    /*public static function fetchAllQuery(string $sql, array $columns, int $mode = SQLITE3_ASSOC)
    {
        if (!self::isConnected()) return false;

        $statement = self::getInstance()->getConnection()->prepare($sql);
        if ($statement === false) return false;

        for ($i = 0; $i < count($columns); $i++) {
            $statement->bindValue($i + 1, $columns[$i]);
        }

        $rows = $statement->execute();
        if ($rows === false) return false;

        $models = [];
        while ($row = $rows->fetchArray($mode)) {
            array_push($models, $row);
        }

        $rows->finalize();
        return $models;
    }*/

    /**
     * @return array|false
     */
    public static function fetchOne(string $sql, array $columns = [], int $mode = SQLITE3_ASSOC)
    {
        $rows = self::fetchAll($sql, $columns, $mode);
        if ($rows === false) return false;
        return count($rows) > 0 ? $rows[0] : [];
    }

    /**
     * @return array|false
     */
    /*public static function fetchAllAssoc(string $sql, string $key, array $columnsConcat, string $charSeparator = '|', int $mode = SQLITE3_ASSOC)
    {
        if (!self::isConnected()) return false;

        $statement = self::getInstance()->getConnection()->prepare($sql);
        if ($statement === false) return false;

        $rows = $statement->execute();
        if ($rows === false) return false;

        $models = [];
        while ($row = $rows->fetchArray($mode)) {
            $fullname = [];
            for ($i = 0; $i < count($columnsConcat); $i++) {
                $fullname[$i] = $row[$columnsConcat[$i]];
            }
            $models[$row[$key]] = implode(' ' . $charSeparator, $fullname);
        }

        $rows->finalize();
        return $models;
    }*/
}
