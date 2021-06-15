<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class kf_disag extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('kfreport_id', 'id_disaggregation');
    public $incrementing = false;

}
