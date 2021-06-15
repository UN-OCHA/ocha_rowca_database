<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;

class organisation_category extends Model
{
    //
    use HasCompositePrimaryKey;
    protected $primaryKey = array('organization_id', 'organization_categories_id');
}
