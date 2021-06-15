<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class plan_usageyear extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('usageyear_id', 'plan_rowid');
    public $incrementing = false;
}
