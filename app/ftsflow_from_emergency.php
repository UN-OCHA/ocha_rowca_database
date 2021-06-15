<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class ftsflow_from_emergency extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('flow_rowid', 'emergency_id');
    public $incrementing = false;
}
