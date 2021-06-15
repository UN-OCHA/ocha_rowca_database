<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\kf_indicator as kf_indicator;
use App\type_header as type_header;
use App\header as header;
use App\disaggregation as disaggregation;

class QueryDatabaseController extends Controller
{
    public function getFKIndicatorList(){
        $indicators = kf_indicator::all();
        return $indicators->toJson();
        //return $indicators;
    }

    public function getTypeHeaders(){
        $typeheaders = type_header::all();
        return $typeheaders->toJson();
    }


    public function getHeaders(){
        $headers = DB::table('headers')
            ->join('type_headers', 'headers.type_header_id', '=', 'type_headers.type_header_id')
            ->select('headers.*', 'type_headers.type_header_name', 'type_headers.type_header_code')
            ->get();
        //$headers = header::all();
        return $headers->toJson();
    }


    public function getDisaggregations(){
        $disaggregations = disaggregation::all();
        return $disaggregations->toJson();
    }
}
