<?php

declare(strict_types=1);

namespace Tests\Support\Config;

use function getenv;

/**
 * Provides a basic registrar class for testing BaseConfig registration functions.
 */

class Registrar
{
    /**
     * DB config array for testing purposes.
     *
     * @var array
     */
    protected static $dbConfig = [
        'MySQLi'  => [
            'DSN'      => '',
            'hostname' => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'database' => 'test',
            'DBDriver' => 'MySQLi',
            'DBPrefix' => 'db_',
            'pConnect' => false,
            'DBDebug'  => (ENVIRONMENT !== 'production'),
            'charset'  => 'utf8',
            'DBCollat' => 'utf8_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 3306,
        ],
        'Postgre' => [
            'DSN'      => '',
            'hostname' => 'localhost',
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => 'test',
            'DBDriver' => 'Postgre',
            'DBPrefix' => 'db_',
            'pConnect' => false,
            'DBDebug'  => (ENVIRONMENT !== 'production'),
            'charset'  => 'utf8',
            'DBCollat' => 'utf8_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 5432,
        ],
        'SQLite3' => [
            'DSN'      => '',
            'hostname' => 'localhost',
            'username' => '',
            'password' => '',
            'database' => 'database.db',
            'DBDriver' => 'SQLite3',
            'DBPrefix' => 'db_',
            'pConnect' => false,
            'DBDebug'  => (ENVIRONMENT !== 'production'),
            'charset'  => 'utf8',
            'DBCollat' => 'utf8_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 3306,
        ],
        'SQLSRV'  => [
            'DSN'      => '',
            'hostname' => 'localhost',
            'username' => 'sa',
            'password' => '1Secure*Password1',
            'database' => 'test',
            'DBDriver' => 'SQLSRV',
            'DBPrefix' => 'db_',
            'pConnect' => false,
            'DBDebug'  => (ENVIRONMENT !== 'production'),
            'charset'  => 'utf8',
            'DBCollat' => 'utf8_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 1433,
        ],
    ];

    /**
     * Override database config
     *
     * @return array
     */
    public static function Database(): array
    {
        $config = [];

        // Under Github Actions, we can set an ENV var named 'DB'
        // so that we can test against multiple databases.
        if ($group = getenv('DB')) {
            if (! empty(self::$dbConfig[$group])) {
                // GitHub ActionsでのPHPUnit実行時の設定
                $config['tests'] = self::$dbConfig[$group];
            }
        } else {
            // ローカル開発環境でのPHPUnit実行時の設定
            $config['tests'] = [
                'DSN'      => '',
                'hostname' => 'localhost',
                'username' => 'username',
                'password' => 'password',
                'database' => 'codeigniter',
                'DBDriver' => 'MySQLi',
                'DBPrefix' => '',
                'pConnect' => false,
                'DBDebug'  => (ENVIRONMENT !== 'production'),
                'charset'  => 'utf8',
                'DBCollat' => 'utf8_general_ci',
                'swapPre'  => '',
                'encrypt'  => false,
                'compress' => false,
                'strictOn' => false,
                'failover' => [],
                'port'     => 3306,
            ];
        }

        return $config;
    }
}
