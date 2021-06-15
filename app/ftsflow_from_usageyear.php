<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class ftsflow_from_usageyear extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $fillable = ['flow_rowid','usageyear_id'];
    protected $primaryKey = array('flow_rowid', 'usageyear_id');
    public $incrementing = false;
}
