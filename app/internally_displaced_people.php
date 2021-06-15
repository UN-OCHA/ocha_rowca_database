<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class internally_displaced_people extends Model
{
    //
    protected $primaryKey = 'idp_id';
    protected $fillable = ['idp_id'];
    public $incrementing = false;
}
