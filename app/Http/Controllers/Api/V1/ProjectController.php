<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Project\ProjectListResource;
use App\Http\Resources\V1\Project\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

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
}
