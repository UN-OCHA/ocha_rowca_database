<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class displacement extends Model
{
    //
    protected $primaryKey = 'dis_id';
    protected $fillable = ['dis_id'];
    public $incrementing = false;
}
