<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Winter\LaravelConfigWriter\ArrayFile;
use Illuminate\Support\Facades\Log;

class CreateProjectDatabase implements ShouldQueue
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
        try {
            $project = $this->project;
            if (Artisan::call("project:db create {$project->db_name} {$project->db_user} {$project->db_pass}")) {
                $this->configBackup(config('database.connections'));

                $config = ArrayFile::open(base_path('config/database.php'));
                $config->set('connections.' . strtolower($project->slug), [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => $project->db_name,
                    'username' => $project->db_user,
                    'password' => $project->db_pass,
                ]);
                $config->write();

                MigrateProjectDatabase::dispatch($project)->delay(now()->addSeconds(5));
            } else {
                return response([
                    'message' => 'Could Not Create Database'
                ], 500);
            }
        } catch (\Throwable $th) {
            return response([
                'message' => 'Could Not Create Database',
                'exception' => $th
            ], 500);
        }
    }

    public function configBackup($old_config)
    {
        $configOld = ArrayFile::open(base_path('config/database_prev.php'));
        $configOld->set('connections', $old_config); // dump stable connection
        $configOld->write(); // write in backup file
    }
}
