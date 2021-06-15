<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class plan_emergency extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('plan_rowid', 'emergency_id');
    public $incrementing = false;
}
