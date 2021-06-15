<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class emergency_category extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('emergency_id', 'emerg_categ_id');
    public $incrementing = false;
}
