<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class ftsflow_usageyear extends Model
{
    //
   

use HasCompositePrimaryKey;
    protected $primaryKey = array('flow_rowid', 'usageyear_id');
    public $incrementing = false;
}
