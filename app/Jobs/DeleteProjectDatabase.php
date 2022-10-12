<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class DeleteProjectDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, IsMonitored;

    protected $project;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($project)
    {
        Log::info("delete CONSTRUCTOR");
        Log::info($project);
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $project = $this->project;
            Log::info("delete jobs fired with" . $project['slug']);
            if (Artisan::call("project:db drop {$project['db_name']} {$project['db_user']} ")) {
                Artisan::call("config:sync");
            } else {
                return response([
                    'message' => 'Could Not Delete Database'
                ], 500);
            }
        } catch (\Throwable $th) {
            return response([
                'message' => 'Could Not Create Database',
                'exception' => $th
            ], 500);
        }
    }
}
