<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MigrateProjectDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $project;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call("config:clear");
        Artisan::call("config:cache");

        DB::setDefaultConnection('mysql');

        Artisan::call('migrate', [
            '--path' => "database/migrations/project/2022_10_13_060614_create_project_user.php",
            '--force' => true,
            '--database' => $this->project->slug
        ]);

        Artisan::call('migrate', [
            '--path' => "database/migrations/project/2022_10_13_060638_create_project_permissions.php",
            '--force' => true,
            '--database' => $this->project->slug
        ]);

        Artisan::call('db:seed', [
            '--class' => "ProjectPermissionSeeder",
            '--force' => true,
            '--database' => $this->project->slug
        ]);

        Artisan::call('db:seed', [
            '--class' => "ProjectAdminSeeder",
            '--force' => true,
            '--database' => $this->project->slug
        ]);

        Artisan::call('db:seed', [
            '--class' => "ProjectUserSeeder",
            '--force' => true,
            '--database' => $this->project->slug
        ]);
    }
}
