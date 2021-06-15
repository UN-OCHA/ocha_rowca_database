<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class localite extends Model
{
    //
    protected $primaryKey = 'local_id';
    protected $fillable = ['local_id'];
    public $incrementing = false;
}
