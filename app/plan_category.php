<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class plan_category extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('plan_rowid', 'plan_categorie_id');
    public $incrementing = false;
}
