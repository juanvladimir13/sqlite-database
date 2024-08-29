<?php

/**
 * @author juanvladimir13 <juanvladimir13@gmail.com>
 * @see https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace SQLiteDatabase;

class Dotenv
{
    public static function load(): array
    {
        $config = [
            'DB_DATABASE' => 'database.sqlite',
            'DB_FLAGS' => SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE,
            'DB_ENCRYPTION_KEY' => 'developers'
        ];

        try {
            $parentEnv = file_exists('../.env');
            $brotherEnv = file_exists('./.env');
            if (!($parentEnv || $brotherEnv))
                throw new \Exception('File not found');

            $dataEnvironment = $parentEnv ? include '../.env' : include './.env';
            return array_merge($config, $dataEnvironment);
        } catch (\Exception $e) {
            return $config;
        }
    }
}
