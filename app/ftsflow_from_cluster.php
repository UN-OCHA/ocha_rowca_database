<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class ftsflow_from_cluster extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('flow_rowid', 'cluster_id');
    public $incrementing = false;
}
