<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class outilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function deleteall()
    {
        //DB::table('kf_disags')->truncate();
        //DB::table('disaggregations')->delete();
        //DB::table('type_disaggregations')->delete();
        //DB::table('report_year_trends')->truncate();
        //DB::table('kf_indicators')->truncate();
        //DB::table('kf_reports')->truncate();
        //DB::table('kf_disags')->truncate();
        DB::statement('TRUNCATE kf_indicators CASCADE');
        DB::statement('TRUNCATE kf_reports CASCADE');
        DB::statement('TRUNCATE report_month_trends CASCADE');
        DB::statement('TRUNCATE report_year_trends CASCADE');
        DB::statement('TRUNCATE kf_disags CASCADE');
        DB::statement('TRUNCATE recalculated_reports CASCADE');
        //DB::table('headers')->delete();
        
        //DB::table('recalculated_reports')->truncate();
        //**DB::table('headers')->delete();
        //**DB::table('kf_indicators')->delete();
        //DB::table('kf_subcategories')->delete();
        //DB::table('kf_categs')->delete();
        //DB::table('emergency_locations')->delete();
        
        //DB::table('ftsflow_dest_locations')->truncate();
        //DB::table('')->truncate();
        //DB::table('plan_locations')->truncate();
        //DB::table('location_others')->truncate();
        //DB::table('locations')->truncate();
        DB::statement('TRUNCATE ftsflow_dest_locations CASCADE');
        DB::statement('TRUNCATE ftsflow_from_locations CASCADE');
        DB::statement('TRUNCATE plan_locations CASCADE');
        DB::statement('TRUNCATE location_others CASCADE');
        DB::statement('TRUNCATE locations CASCADE');
        echo 'finish';
        //DB::table('location_types')->delete();
    }
}
