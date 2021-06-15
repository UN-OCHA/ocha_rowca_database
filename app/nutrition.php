<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class nutrition extends Model
{
    //
    protected $primaryKey = 'nut_id';
    protected $fillable = ['nut_id'];
    public $incrementing = false;
}
