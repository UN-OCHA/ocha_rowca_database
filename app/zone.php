<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class zone extends Model
{
    //
    protected $primaryKey = 'zone_id';
    protected $fillable = ['zone_id'];
    public $incrementing = false;
}
