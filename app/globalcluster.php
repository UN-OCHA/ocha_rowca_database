<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class globalcluster extends Model
{
    //
    protected $fillable = ['globalclusters_id','globalclusters_name'];
    protected $primaryKey = 'globalclusters_id';
    public $incrementing = false;
}
