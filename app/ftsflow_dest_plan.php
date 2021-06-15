<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class ftsflow_dest_plan extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('flow_rowid', 'plan_rowid');
    public $incrementing = false;
}
