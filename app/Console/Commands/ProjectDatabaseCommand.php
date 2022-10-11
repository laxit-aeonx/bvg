<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ProjectDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:db {operation} {database} {user} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates & drop database with users';

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('command fired');
        $this->setConfig(); // set PDO connection

        switch ($this->argument('operation')) {
            case 'create':
                Log::info('Case Create');
                if ($this->argument('password')) {
                    Log::info('case success');
                    if ($this->createDatabase($this->argument('database'), $this->argument('user'), $this->argument('password'))) {
                        Log::info('Successful Query');
                        $this->info('Operation Successfull');
                        return true;
                    } else {
                        Log::info('Failed Query');
                        $this->info('Operation Failed');
                        return false;
                    }
                } else {
                    Log::info('case error');
                    $this->error('Please provide password');
                    return false;
                }
                break;

            case 'drop':

                if ($this->dropDatabase($this->argument('database'), $this->argument('user'))) {
                    $this->info('Operation Successfull');
                    return Command::SUCCESS;
                } else {
                    $this->info('Operation Failed');
                    return Command::FAILURE;
                }
                break;

            default:
                $this->error("choose either 'create' or 'drop' as operation");
                return Command::FAILURE;
                break;
        }
    }

    public function setConfig()
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

    public function createDatabase($database, $user, $password)
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
                $this->pdo->exec("GRANT ALL PRIVILEGES ON `$database`.* TO '$user'@'%'");
                $this->pdo->exec("FLUSH PRIVILEGES");

                return true;
            } catch (\Throwable $th) {
                return $th;
            }
        } else {
            return "Database Already Exists";
        }
    }

    public function dropDatabase($database, $user)
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
