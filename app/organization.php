<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class organization extends Model
{
    //
    protected $primaryKey = 'organization_id';
    protected $fillable = ['organization_id','organization_name'];
}
