<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class usageyear extends Model
{
    //
    protected $fillable = ['usageyear_id','usageyear_name'];
    protected $primaryKey = 'usageyear_id';
    public $incrementing = false;
}
