<?php
declare(strict_types=1);

function getAppDatabaseConnection(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $databasePath = getenv('STEAM_PROJECT_SQLITE_PATH');
    if (!is_string($databasePath) || trim($databasePath) === '') {
        $databasePath = dirname(__DIR__) . '/storage/app.sqlite';
    }
    if (!is_file($databasePath)) {
        throw new RuntimeException('SQLite database file not found: ' . $databasePath);
    }

    $connection = new PDO('sqlite:' . $databasePath);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $connection->exec('PRAGMA foreign_keys = ON');

    return $connection;
}