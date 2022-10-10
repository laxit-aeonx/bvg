<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, Uuids;

    public $timestamps = true;

    protected $fillable = [
        'id',
        'slug',
        'name',
        'description',
        'db_host',
        'db_name',
        'db_user',
        'db_pass'
    ];

    public function getUser()
    {
        return $this->belongsToMany('App\Models\User')->withPivot('project_id', 'user_id');
    }
}
