<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class ftsflow_dest_globalcluster extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('flow_rowid', 'globalclusters_id');
    public $incrementing = false;
}
