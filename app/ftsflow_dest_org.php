<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class ftsflow_dest_org extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $fillable = ['flow_rowid','organization_id'];
    protected $primaryKey = array('flow_rowid', 'organization_id');
    public $incrementing = false;
}
