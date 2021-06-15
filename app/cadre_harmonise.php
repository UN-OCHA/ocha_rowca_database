<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cadre_harmonise extends Model
{
    protected $primaryKey = 'ch_id';
    protected $fillable = ['ch_id'];
    public $incrementing = false;
}
