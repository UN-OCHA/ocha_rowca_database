<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class plan_location extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('plan_rowid', 'location_other_id');
    public $incrementing = false;
}
