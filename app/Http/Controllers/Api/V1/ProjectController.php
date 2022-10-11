<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Project\{ProjectResource, ProjectListResource};
use App\Models\{Project, ProjectUser, User};
use App\Http\Requests\Project\ProjectCreateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function list()
    {
        return ProjectListResource::collection(Project::all());
    }

    public function details($project)
    {
        return new ProjectResource(Project::findOrFail($project));
    }

    public function create(ProjectCreateRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $request['db_name'] = $request->slug . "_db";
                $request['db_user'] = $request->slug . "_user";
                $request['db_pass'] = Str::random(10);

                $project = Project::create($request->all());

                ProjectUser::create([
                    'project_id' => $project->id,
                    'user_id' => $request->user
                ]);

                DB::statement("CREATE DATABASE {$project->db_name}");
                DB::statement("CREATE USER '{$project->db_user}'@'localhost' IDENTIFIED BY '{$$project->db_pass}' ");
                DB::statement("GRANT ALL PRIVILEGES ON {$project->db_name}. * TO '{$project->db_user}'@'localhost'");
            });

            return response([
                'message' => 'Project Created',
            ], 200);
        } catch (\Throwable $th) {

            return response([
                'message' => 'Could Not Create Project',
                'error' => $th
            ], 500);
        }
    }
}
