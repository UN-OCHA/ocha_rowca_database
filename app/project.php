<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class project extends Model
{
    //
    protected $fillable = ['project_id','project_code','project_name'];
    protected $primaryKey = 'globalclusters_id';
    public $incrementing = false;
}
