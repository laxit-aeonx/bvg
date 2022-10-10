<?php

namespace App\Helpers;

use PDO;
use Illuminate\Support\Facades\Config;

class CustomDatabase
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Raw database connection configuration.
     * See config/database.php for more info
     *
     * @var array
     */
    protected $config = [];

    public function __construct()
    {
        $this->config = [
            'host' => Config::get('database.connections.mysql.host'),
            'port' => Config::get('database.connections.mysql.port'),
            'username' => Config::get('database.connections.mysql.username'),
            'password' => Config::get('database.connections.mysql.password'),
            'charset' => Config::get('database.connections.mysql.charset'),
            'collation' => Config::get('database.connections.mysql.collation'),
        ];

        $this->pdo = $this->getPdo(
            Config::get('database.connections.mysql.host'),
            Config::get('database.connections.mysql.port'),
            Config::get('database.connections.mysql.username'),
            Config::get('database.connections.mysql.password'),
        );
    }

    public function databaseExists($database)
    {
        $stmt = $this->pdo->query("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$database'");

        return $stmt->fetch() !== false;
    }

    public function createDatabase($host, $database, $user, $password)
    {
        if (!$this->databaseExists($database)) {
            try {
                $this->pdo->exec(sprintf(
                    'CREATE DATABASE `%s` CHARACTER SET %s COLLATE %s;',
                    $database,
                    $this->config['charset'],
                    $this->config['collation']
                ));

                $this->pdo->exec("CREATE USER '$user'@'%' IDENTIFIED BY '$password'");
                $this->pdo->exec("GRANT ALL ON `$database`.* TO '$user'@'%'");
                $this->pdo->exec("FLUSH PRIVILEGES");

                return true;
            } catch (\Throwable $th) {
                return $th;
            }
        } else {
            return "Database Already Exists";
        }
    }

    public function dropDatabase($host, $database, $user)
    {
        if ($this->databaseExists($database)) {
            try {

                $this->pdo->exec("DROP DATABASE `$database`");
                $this->pdo->exec("DROP USER IF EXISTS '$user'@'%'");
                $this->pdo->exec("REVOKE ALL PRIVILEGES ON `$database`.* TO '$user'@'%'");

                $this->pdo->exec("FLUSH PRIVILEGES");

                return true;
            } catch (\Throwable $th) {
                return $th;
            }
        } else {
            return "Database Not Found";
        }
    }

    public function getColumns($database, $table)
    {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM `$database`.`$table`");

        $data = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $i => $field) {
            // `name`, `position`, `type`, `nullable`, `default_value
            $data[] = [
                'name' => $field['Field'],
                'position' => $i,
                'type' => $field['Type'],
                'nullable' => $field['Null'] == 'YES',
                'default_value' => $field['Default']
            ];
        }

        return $data;
    }

    /**
     * Get PDO connection in case we want to perform custom queries
     *
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     *
     * @return PDO
     */
    public function getPdo($host, $port, $username, $password)
    {
        if ($this->pdo === null) {
            $pdo = new PDO(sprintf('mysql:host=%s;port=%d;', $host, $port), $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->pdo = $pdo;
        }

        return $this->pdo;
    }
}
