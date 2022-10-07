<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\AdminSeeder;
use Database\Seeders\PermissionSeeder;

class InitDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialized Database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm('This will Delete All Tables, Continue ?')) {

            $this->output->progressStart(3);
            $this->info(' Initializing...');

            Artisan::call('migrate:fresh');
            $this->output->progressAdvance();
            $this->info(' Database Dumped');

            $permissionSeeder = new PermissionSeeder();
            $permissionSeeder->run();
            $this->output->progressAdvance();
            $this->info(' Seeding: Permissions');

            $adminSeeder = new AdminSeeder();
            $adminSeeder->run();
            $this->output->progressAdvance();
            $this->info(' Seeding: Admin');

            $this->output->progressFinish();
        }
    }
}
