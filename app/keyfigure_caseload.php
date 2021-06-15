<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class keyfigure_caseload extends Model
{
    //
    protected $primaryKey = 'id_caseload';
    protected $fillable = ['id_caseload'];
    public $incrementing = false;
}
