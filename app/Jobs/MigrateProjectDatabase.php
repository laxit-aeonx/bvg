<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Illuminate\Support\Facades\Log;

class MigrateProjectDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

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
        $project = $this->project->slug;
        Log::info('Migration Started for '.$project);
        Artisan::call("config:clear");
        Artisan::call("config:cache");

        Artisan::call('migrate', [
            '--path' => "database/migrations/project/2022_10_09_101428_create_project_users.php",
            '--force' => true,
            '--database' => $project
        ]);
    }
}
