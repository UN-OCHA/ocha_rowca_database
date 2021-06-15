<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CaseloadsImport;
use App\Imports\informsahelrisksImport;
use App\Imports\internallydisplacedpersonsImport;
use App\Imports\nutritionsImport;
use App\Imports\foodsecuritiesImport;
use App\Imports\cadre_harmonisesImport;
use App\Imports\displacements_lcb_Import;
use App\Imports\displacements_sahel_Import;
use App\Imports\displacements_sahel_central_Import;
use App\Imports\displacements_wca_Import;
use App\Imports\caseloads_lcb_Import;
use App\Imports\caseloads_sahel_Import;
use App\Imports\caseloads_sahel_central_Import;
use App\Imports\caseloads_wca_Import;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;


class localDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show_view_access_import(Request $request) 
    {
        if ($request->session()->has('authenticated')) {
            return redirect("/import");
        }else{
            return view('localdata.accessimport');
        }
        
    }
    public function show_view_access_manage(Request $request) 
    {
        if ($request->session()->has('authenticated')) {
            return redirect("/managezones");
        }else{
            return view('localdata.accessmanage');
        }
    }
    public function show_view_import() 
    {
        $zones = DB::table('zones')->orderBy('zone_name', 'asc')->get();
        return view('localdata.import',['zones'=>$zones]);
    }
    public function show_view_database() 
    {
        $zones = DB::table('zones')->orderBy('zone_name', 'asc')->get();
        $pays = DB::table('liste_localites')->where('zone_code', '=', 'WCA')->orderBy('local_name', 'asc')->get();
        return view('localdata.database',['datas'=>$zones,'listepays'=>$pays]);
    }
    public function show_view_confirm_import($element) 
    {
        $elementName = "";
        switch ($element){
            case "caseloads":
                $elementName = "Caseloads";
            break;
            case "informSahel":
                $elementName = "Inform sahel";
            break;
            /*case "idps":
                $elementName = "Internally displaced persons";
            break;*/
            case "disp":
                $elementName = "Displacements";
            break;
            case "nutrition":
                $elementName = "Nutrition";
            break;
            case "ch":
                $elementName = "Cadre harmonisé";
            break;
        }
        
        $zones = DB::table('zones')->orderBy('zone_name', 'asc')->get();
        return view('localdata.confirmimport',['elementName'=>$elementName,'element'=>$element,'zones'=>$zones]);
    }
    public function import_caseloads() 
    {
        try {
            /*
            date_default_timezone_set('UTC');
            Storage::copy('input data/caseloads/lcb.xlsx', 'backup data/caseloads/lcb backup '.date("Y-m-d H i s").'.xlsx');
            Storage::copy('input data/caseloads/sahel.xlsx', 'backup data/caseloads/sahel backup '.date("Y-m-d H i s").'.xlsx');
            Storage::copy('input data/caseloads/sahel_central.xlsx', 'backup data/caseloads/sahel_central backup '.date("Y-m-d H i s").'.xlsx');
            Storage::copy('input data/caseloads/wca.xlsx', 'backup data/caseloads/wca backup '.date("Y-m-d H i s").'.xlsx');
*/
			/***************************/
			
            DB::statement('TRUNCATE caseloads ');

            DB::table('data_by_years')->where('t_category', '=', 'people_in_need')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'people_targeted')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'people_reached')->delete();
    
            Excel::import(new caseloads_lcb_Import, 'input data/caseloads/lcb.xlsx');
            Excel::import(new caseloads_sahel_Import, 'input data/caseloads/sahel.xlsx');
            Excel::import(new caseloads_sahel_central_Import, 'input data/caseloads/sahel_central.xlsx');
            Excel::import(new caseloads_wca_Import, 'input data/caseloads/wca.xlsx');
            return redirect('/import')->with('success', 'All good!');
			
			/***************************/
            return redirect('/import')->with('success', 'All good!');
        } catch (\Throwable $th) {
            return redirect('/import')->with('error', " , Import discontinued : ".$th->getMessage() );
        }
    }
    public function import_inform_sahel() 
    {
        try {
			/***************************/
			
			DB::statement('TRUNCATE inform_sahel_risks ');
            Excel::import(new informsahelrisksImport, 'input data/inform_sahel.xlsx');
            return redirect('/import')->with('success', 'All good!');
			
			/***************************/
            return redirect('/import')->with('success', 'All good!');
        } catch (\Throwable $th) {
            return redirect('/import')->with('error', " , Import discontinued : ".$th->getMessage() );
        }
        
    }
    public function import_nutrition() 
    {
        try {
            
			/***************************/
			
			DB::statement('TRUNCATE nutrition ');

            DB::table('data_by_years')->where('t_category', '=', 'severe_acute_malnutrition')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'global_acute_malnutrition')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'moderate_acute_malnutrition')->delete();

            Excel::import(new nutritionsImport, 'input data/nutrition.xlsx');
            return redirect('/import')->with('success', 'All good!');
			
			/***************************/
            return redirect('/import')->with('success', 'All good!');
        } catch (\Throwable $th) {
            return redirect('/import')->with('error', " , Import discontinued : ".$th->getMessage() );
        }
        
    }
    public function import_cadre_harmonise() 
    {
        try {
            
			/***************************/
			
			DB::statement('TRUNCATE cadre_harmonises ');

            DB::table('data_by_years')->where('t_category', '=', 'ch_Current_phase1')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Current_phase2')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Current_phase3')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Current_phase3_plus')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Current_phase4')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Current_phase5')->delete();

            DB::table('data_by_years')->where('t_category', '=', 'ch_Projected_phase1')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Projected_phase2')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Projected_phase3')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Projected_phase3_plus')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Projected_phase4')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'ch_Projected_phase5')->delete();


            Excel::import(new cadre_harmonisesImport, 'input data/cadre_harmonise.xlsx');
            return redirect('/import')->with('success', 'All good!');
			
			/***************************/
            return redirect('/import')->with('success', 'All good!');
        } catch (\Throwable $th) {
            return redirect('/import')->with('error', " , Import discontinued : ".$th->getMessage() );
        }
        
    }
    public function import_displacement() 
    {
        try {
            /*
            date_default_timezone_set('UTC');
            Storage::copy('input data/displacements/lcb.xlsx', 'backup data/displacements/lcb backup '.date("Y-m-d H i s").'.xlsx');
            Storage::copy('input data/displacements/sahel.xlsx', 'backup data/displacements/sahel backup '.date("Y-m-d H i s").'.xlsx');
            Storage::copy('input data/displacements/sahel_central.xlsx', 'backup data/displacements/sahel_central backup '.date("Y-m-d H i s").'.xlsx');
            Storage::copy('input data/displacements/wca.xlsx', 'backup data/displacements/wca backup '.date("Y-m-d H i s").'.xlsx');
            */
            DB::statement('TRUNCATE displacements');

            DB::table('data_by_years')->where('t_category', '=', 'Refugee')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'IDP')->delete();
            DB::table('data_by_years')->where('t_category', '=', 'Returnee')->delete();
    
            Excel::import(new displacements_lcb_Import, 'input data/displacements/lcb.xlsx');
            Excel::import(new displacements_sahel_Import, 'input data/displacements/sahel.xlsx');
            Excel::import(new displacements_sahel_central_Import, 'input data/displacements/sahel_central.xlsx');
            Excel::import(new displacements_wca_Import, 'input data/displacements/wca.xlsx');
    
            return redirect('/import')->with('success', 'All good!');
        } catch (\Throwable $th) {
            return redirect('/import')->with('error', " , Import discontinued : ".$th->getMessage() );
        }
    }

    public function guide_import(Request $request)
    {
            $url="/import"."/".$request->input('dataCategory');
            $fileCrisis = "";
            date_default_timezone_set('UTC');

            switch ($request->input('zoneCode')) {
                case "SAH":
                    $fileCrisis = "sahel.xlsx";
                    break;
                case "SAHC":
                    $fileCrisis = "sahel_central.xlsx";
                    break;
                case "LCB":
                    $fileCrisis = "lcb.xlsx";
                    break;
                case "WCA":
                    $fileCrisis = "wca.xlsx";
                    break;
            }

        

            switch ($request->input('dataCategory')) {
                case "caseloads":
                    Storage::copy("input data/caseloads/".$fileCrisis, "backup data/caseloads/".$fileCrisis." backup ".date("Y-m-d H i s").'.xlsx');
                    $path = $request->importedfile->storeAs('input data/caseloads',$fileCrisis);
                    break;
                case "informSahel":
                    Storage::copy('input data/inform_sahel.xlsx', 'backup data/inform_sahel backup '.date("Y-m-d H i s").'.xlsx');
                    $path = $request->importedfile->storeAs('input data','inform_sahel.xlsx');
                    break;
                case "disp":
                    Storage::copy("input data/displacements/".$fileCrisis, "backup data/displacements/".$fileCrisis." backup ".date("Y-m-d H i s").'.xlsx');
                    $path = $request->importedfile->storeAs('input data/displacements',$fileCrisis);
                    break;
                case "ch":
                    Storage::copy('input data/cadre_harmonise.xlsx', 'backup data/cadre_harmonise backup '.date("Y-m-d H i s").'.xlsx');
                    $path = $request->importedfile->storeAs('input data','cadre_harmonise.xlsx');
                    break;
                case "nutrition":
                    Storage::copy('input data/nutrition.xlsx', 'backup data/nutrition backup '.date("Y-m-d H i s").'.xlsx');
                    $path = $request->importedfile->storeAs('input data','nutrition.xlsx');
                    break;
            }
            return redirect($url);

    }

    public function verifyaccessimport(Request $request)
    {
        if($_POST['password']=="1two3"){
            $request->session()->put('authenticated', 'true');
            return redirect("/import");
        }else{
            return back()->with('msg', 'Wrong password please try again on contact the administrator');
        }
    }

    public function verifyaccessmanage(Request $request)
    {
        if($_POST['password']=="1two3"){
            $request->session()->put('authenticated', 'true');
            return redirect("/managezones");
        }else{
            return back()->with('msg', 'Wrong password please try again on contact the administrator');
        }
    }
}
