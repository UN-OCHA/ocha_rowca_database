<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class ftsflow_dest_location extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('flow_rowid', 'location_other_id');
    public $incrementing = false;
}
