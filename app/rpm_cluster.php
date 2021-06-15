<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class rpm_cluster extends Model
{
    //
    protected $fillable = ['cluster_id','cluster_description'];
    protected $primaryKey = 'cluster_id';
    public $incrementing = false;
}
