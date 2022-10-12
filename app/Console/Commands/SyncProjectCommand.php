<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Winter\LaravelConfigWriter\ArrayFile;

class SyncProjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command syncs all projects with database configuration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('config:clear');
        Artisan::call('config:cache');

        $projects = Project::all();
        $configs = Config::get('database.connections');
        $defaultConfig = Config::get('database.default');

        unset($configs[$defaultConfig]); // removes default connection

        foreach ($projects as $key => $project) {

            if (array_key_exists($project->slug, $configs)) {
                $this->comment('Config found for ' . $project->slug);
            } else {
                $this->warn('no config found for ' . $project->slug);
                $this->configCreate($project);
                $this->info('config generated for ' . $project->slug);
            }
        }
    }

    public function configCreate($project)
    {
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
    }
}
