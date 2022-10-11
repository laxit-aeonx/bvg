<?php

namespace App\Observers;

use App\Helpers\CustomDatabase;
use App\Jobs\CreateProjectDatabase;
use App\Models\Project;

use Winter\LaravelConfigWriter\ArrayFile;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function created(Project $project)
    {
        CreateProjectDatabase::dispatch($project);
    }

    /**
     * Handle the Project "updated" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function updated(Project $project)
    {
        //
    }

    /**
     * Handle the Project "deleted" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function deleted(Project $project)
    {
        // TODO push this in queue for config writing
        $conn = new CustomDatabase();
        try {
            if ($conn->dropDatabase($project->db_name, $project->db_user)) {

                $old_config = config('database.connections'); //get all config

                $this->configBackup($old_config);

                $config = ArrayFile::open(base_path('config/database.php'));
                unset($old_config[strtolower($project->slug)]); // remove current project slug
                $config->set('connections', $old_config); // load new connection
                $config->write();

                sleep(3); // take a break to make sure file is written
            } else {
                return response([
                    'message' => 'Could Not Delete Database'
                ], 500);
            }
        } catch (\Throwable $th) {
            return response([
                'message' => 'Could Not Delete Database',
                'exception' => $th
            ], 500);
        }
    }

    /**
     * Handle the Project "restored" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function restored(Project $project)
    {
        //
    }

    /**
     * Handle the Project "force deleted" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function forceDeleted(Project $project)
    {
        //
    }

    public function configBackup($old_config)
    {
        $configOld = ArrayFile::open(base_path('config/database_prev.php'));
        $configOld->set('connections', $old_config); // dump stable connection
        $configOld->write(); // write in backup file
    }
}
