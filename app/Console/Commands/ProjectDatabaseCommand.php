<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PDO;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
        $this->pdo = DB::getPdo(); // set PDO connection

        switch ($this->argument('operation')) {
            case 'create':
                if ($this->argument('password')) {
                    if ($this->createDatabase($this->argument('database'), $this->argument('user'), $this->argument('password'))) {
                        $this->info('Operation Successfull');
                        return true;
                    } else {
                        $this->info('Operation Failed');
                        return false;
                    }
                } else {
                    $this->error('Please provide password');
                    return false;
                }
                break;

            case 'drop':

                if ($this->dropDatabase($this->argument('database'), $this->argument('user'))) {
                    Artisan::call("config:sync");
                    $this->info('Operation Successfull');
                    return true;
                } else {
                    $this->info('Operation Failed');
                    return false;
                }
                break;

            default:
                $this->error("choose either 'create' or 'drop' as operation");
                return Command::FAILURE;
                break;
        }
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
                    'utf8mb4',
                    'utf8mb4_unicode_ci'
                ));

                $this->pdo->exec("CREATE USER '$user'@'%' IDENTIFIED BY '$password'");
                $this->pdo->exec("GRANT ALL PRIVILEGES ON `$database`.* TO '$user'@'%'");
                $this->pdo->exec("GRANT ALL PRIVILEGES ON information_schema.tables TO '$user'@'%'");
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

                $this->pdo->exec("FLUSH PRIVILEGES");

                return true;
            } catch (\Throwable $th) {
                return $th;
            }
        } else {
            return "Database Not Found";
        }
    }
}
