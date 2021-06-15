<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class food_security extends Model
{
    protected $primaryKey = 'fs_id';
    protected $fillable = ['fs_id'];
    public $incrementing = false;
}
