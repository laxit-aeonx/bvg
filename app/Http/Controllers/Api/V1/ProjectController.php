<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Project\{ProjectResource, ProjectListResource};
use App\Models\Project;
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

            $request['slug'] = strtolower($request->slug);
            $request['db_name'] = "bvg_" . $request->slug;
            $request['db_user'] = "bvg_" . $request->slug . "_user";
            $request['db_pass'] = Str::random(10);

            $project = Project::create($request->all());

            return response([
                'message' => 'Project Creation Started . . .',
                'data' => new ProjectResource($project)
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
