<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;

class DeleteProjectDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $project;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($project)
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
        Artisan::call("project:db drop {$this->project['db_name']} {$this->project['db_user']} ");
    }
}
