<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class zone_avoir_localite extends Model
{
    //
    protected $primaryKey = array('zone_id', 'local_id');
    public $incrementing = false;
}
