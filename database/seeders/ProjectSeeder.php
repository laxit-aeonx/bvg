<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::transaction(function () {

            $project = Project::create([
                'slug' => 'demo',
                'name' => 'Pantheon LLP Infra',
                'description' => 'Multi-story Arcade park'
            ]);

            $user = User::where('is_admin', 0)->inRandomOrder()->first();

            ProjectUser::create([
                'project_id' => $project->id,
                'user_id' => $user->id
            ]);
        });
    }
}
