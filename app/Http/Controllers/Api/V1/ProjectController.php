<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CustomDatabase;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Project\{ProjectResource, ProjectListResource};
use App\Models\{Project, ProjectUser, User};
use App\Http\Requests\Project\ProjectCreateRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Winter\LaravelConfigWriter\ArrayFile;

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
        Log::info('Controller Fired');
        try {
            DB::transaction(function () use ($request) {

                $request['slug'] = strtolower($request->slug);
                $request['db_host'] = config('database.connections.mysql.host');
                $request['db_name'] = "bvg_" . $request->slug;
                $request['db_user'] = "bvg_" . $request->slug . "_user";
                $request['db_pass'] = Str::random(10);

                $project = Project::create($request->all());
            });

            return response([
                'message' => 'Project Created',
            ], 200);
        } catch (\Throwable $th) {

            return response([
                'message' => 'Could Not Create Project',
                'exception' => $th
            ], 500);
        }
    }

    public function delete($project)
    {
        if (Project::findOrFail($project)->delete()) {
            return response([
                'message' => 'Project Deleted',
            ], 200);
        } else {
            return response([
                'message' => 'Could not Delete Project'
            ], 500);
        }
    }
}
