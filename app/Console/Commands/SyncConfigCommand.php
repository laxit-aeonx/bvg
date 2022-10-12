<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Winter\LaravelConfigWriter\ArrayFile;

class SyncConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if all configs have database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('config:clear');
        Artisan::call('config:cache');

        $configs = Config::get('database.connections');

        foreach ($configs as $key => $config) {
            $project = Project::where('slug', $key)->first();
            if ($project) {
                $this->info('Config ' . $key . ' Verified');
            } else {

                if ($key != 'mysql') {
                    $this->warn('Stale Config ' . $key);
                    unset($configs[$key]); // remove current project slug
                    $this->info('Config ' . $key . ' Removed');
                }
            }
        }

        $configFile = ArrayFile::open(base_path('config/database.php'));
        $configFile->set('connections', $configs); // load new connection
        $configFile->write();

        $this->info('Configuration Synced');
    }
}
