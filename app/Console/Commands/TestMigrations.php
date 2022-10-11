<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TestMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $db = 'testing';
        // $user = 'testing';
        // $password = 'password';

        // $operation = Artisan::call("project:db create {$db} {$user} {$password}");
        // dd($operation);
        // if ($operation) {
        //     $this->info('DB Created');
        //     return Command::SUCCESS;
        // } else {
        //     $this->info('DB Not Created');
        //     return Command::SUCCESS;
        // }

        $project = Project::first();

        // reloading confing settings
        Artisan::call("config:clear");
        Artisan::call("config:cache");

        Artisan::call('migrate', [
            '--path' => "database/migrations/project/2022_10_09_101428_create_project_users.php",
            '--force' => true,
            '--database' => $project->slug
        ]);

        $this->info('Migration Completed');
        return Command::SUCCESS;
    }
}
