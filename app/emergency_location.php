<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class emergency_location extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('emergency_id', 'location_other_id');
    public $incrementing = false;
}
