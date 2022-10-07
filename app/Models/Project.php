<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, Uuids;

    protected $guarded = [];

    public function getUser()
    {
        return $this->belongsToMany('App\Models\User')->withPivot('project_id', 'user_id');
    }
}
