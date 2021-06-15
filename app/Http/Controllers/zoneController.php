<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;
use App\Imports\Import;
use Illuminate\Support\Facades\DB;
use App\zone as zone;
use App\liste_localite as liste_localite;
use App\keyfigure_caseload as keyfigure_caseload;
use Illuminate\Support\Facades\Storage;



class zoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function liste()
    {
        $zones = DB::table('zones')->orderBy('zone_name', 'asc')->get();
        $dataByZone = array();

        foreach ($zones as $zone) {
            
            
            $liste_localites = liste_localite::where('zone_id', $zone->zone_id)->orderBy('local_name', 'asc')->get();
            $keyfigure_caseloads = keyfigure_caseload::where('zone_id', $zone->zone_id)->get();
            $keyfigure_displacements = DB::table('keyfigure_displacements')->where('zone_id', '=', $zone->zone_id)->where('dis_crise', '=', $zone->zone_code)->get();
            $keyfigure_cadre_harmonises_projected = DB::table('keyfigure_cadre_harmonises')->where('zone_id', '=', $zone->zone_id)->where('ch_situation', '=', 'Projected')->get();
            $keyfigure_cadre_harmonises_current = DB::table('keyfigure_cadre_harmonises')->where('zone_id', '=', $zone->zone_id)->where('ch_situation', '=', 'Current')->get();
            $keyfigure_nutritions = DB::table('keyfigure_nutritions')->where('zone_id', '=', $zone->zone_id)->get();

            $dataZone=array("zone"=>$zone,"localites"=>$liste_localites,"caseloads"=>$keyfigure_caseloads,"displacements"=>$keyfigure_displacements,"cadre_harmonises_projected"=>$keyfigure_cadre_harmonises_projected,"cadre_harmonises_current"=>$keyfigure_cadre_harmonises_current,"nutrition"=>$keyfigure_nutritions);
            array_push($dataByZone,$dataZone);
        }

        //var_dump($dataByZone);

        return view('zone.liste',['datas'=>$dataByZone]);
    }
    public function manageliste()
    {
        DB::connection('pgsql');
        //$zones = zone::all();
        $zones = DB::table('zones')->orderBy('zone_name', 'asc')->get();
        return view('zone.manageliste',['datas'=>$zones]);
    }

    public function show_view_consulter($id)
    {
        $zone = zone::where('zone_id', $id)->first();
        $liste_localites = liste_localite::where('zone_id', $id)->orderBy('local_name', 'asc')->get();
        $keyfigure_caseloads = keyfigure_caseload::where('zone_id', $id)->get();
        $keyfigure_displacements = DB::table('keyfigure_displacements')->where('zone_id', '=', $id)->where('dis_crise', '=', $zone->zone_code)->get();
        $keyfigure_cadre_harmonises_projected = DB::table('keyfigure_cadre_harmonises')->where('zone_id', '=', $id)->where('ch_situation', '=', 'Projected')->get();
        $keyfigure_cadre_harmonises_current = DB::table('keyfigure_cadre_harmonises')->where('zone_id', '=', $id)->where('ch_situation', '=', 'Current')->get();
        $keyfigure_nutritions = DB::table('keyfigure_nutritions')->where('zone_id', '=', $id)->get();
       // $trend_crisis_caseload_by_years = DB::table('trend_crisis_caseload_by_years')->where('zone_id', '=', $id)->get();
        
        return view('zone.consulter',[
            'datas'=>$zone,
            'liste_localites'=>$liste_localites,
            'keyfigure_caseloads'=>$keyfigure_caseloads,
            'keyfigure_displacements'=>$keyfigure_displacements,
            'keyfigure_cadre_harmonises_projected'=>$keyfigure_cadre_harmonises_projected,
            'keyfigure_cadre_harmonises_current'=>$keyfigure_cadre_harmonises_current,
            'keyfigure_nutritions'=>$keyfigure_nutritions,
            //'trend_crisis_caseload_by_years'=>$trend_crisis_caseload_by_years,
            ]);
    }

    public function show_view_filter_old($category,$items)
    {
        $countriesList = array();
        $dataByZone = array();

        
        if($category=="crisis"){
            $crisisList = explode("_", $items);

        }else{
            $crisisList = ["WCA"];
            $countriesList = explode("_", $items);
        }
        
        $zones = DB::table('zones')->whereIn('zone_code', $crisisList)->orderBy('zone_name', 'asc')->get();


        if($category=="crisis"){
            foreach ($zones as $zone) {
                $liste_localites = liste_localite::where('zone_id', $zone->zone_id)->orderBy('local_name', 'asc')->get();
                $keyfigure_caseloads = keyfigure_caseload::where('zone_id', $zone->zone_id)->get();
                $keyfigure_displacements = DB::table('keyfigure_displacements')->where('zone_id', '=', $zone->zone_id)->get();
                $keyfigure_cadre_harmonises_projected = DB::table('keyfigure_cadre_harmonises')->select(DB::raw('zone_code, zone_name, local_name, local_pcode, local_admin_level, local_id, zone_id, ch_country, ch_adm0_pcode_iso3,  sum(ch_phase1) as ch_phase1, sum(ch_phase2) as ch_phase2, sum(ch_phase3) as ch_phase3, sum(ch_phase4) as ch_phase4, sum(ch_phase5) as ch_phase5, sum(ch_phase35) as ch_phase35, ch_exercise_month, ch_exercise_year, ch_situation, ch_date'))->where('zone_id', '=', $zone->zone_id)->where('ch_situation', '=', 'Projected')->groupBy('zone_code', 'zone_name', 'local_name', 'local_pcode', 'local_admin_level', 'local_id', 'zone_id', 'ch_country', 'ch_adm0_pcode_iso3',    'ch_exercise_month', 'ch_exercise_year', 'ch_situation', 'ch_date')->get();
                $keyfigure_cadre_harmonises_current = DB::table('keyfigure_cadre_harmonises')->select(DB::raw('zone_code, zone_name, local_name, local_pcode, local_admin_level, local_id, zone_id, ch_country, ch_adm0_pcode_iso3, sum(ch_phase1) as ch_phase1, sum(ch_phase2) as ch_phase2, sum(ch_phase3) as ch_phase3, sum(ch_phase4) as ch_phase4, sum(ch_phase5) as ch_phase5, sum(ch_phase35) as ch_phase35, ch_exercise_month, ch_exercise_year, ch_situation, ch_date'))->where('zone_id', '=', $zone->zone_id)->where('ch_situation', '=', 'Current')->groupBy('zone_code', 'zone_name', 'local_name', 'local_pcode', 'local_admin_level', 'local_id', 'zone_id', 'ch_country', 'ch_adm0_pcode_iso3',   'ch_exercise_month', 'ch_exercise_year', 'ch_situation', 'ch_date')->get();
                $keyfigure_nutritions = DB::table('keyfigure_nutritions')->where('zone_id', '=', $zone->zone_id)->get();
    
                $dataZone=array("zone"=>$zone,"localites"=>$liste_localites,"caseloads"=>$keyfigure_caseloads,"displacements"=>$keyfigure_displacements,"cadre_harmonises_projected"=>$keyfigure_cadre_harmonises_projected,"cadre_harmonises_current"=>$keyfigure_cadre_harmonises_current,"nutrition"=>$keyfigure_nutritions);
                array_push($dataByZone,$dataZone);
            }
        }else{
            foreach ($zones as $zone) {
                $liste_localites = liste_localite::where('zone_id', $zone->zone_id)->whereIn('local_pcode', $countriesList)->orderBy('local_name', 'asc')->get();
                $keyfigure_caseloads = keyfigure_caseload::where('zone_id', $zone->zone_id)->whereIn('local_pcode', $countriesList)->get();
                $keyfigure_displacements = DB::table('keyfigure_displacements')->where('zone_id', '=', $zone->zone_id)->whereIn('local_pcode', $countriesList)->get();
                $keyfigure_cadre_harmonises_projected = DB::table('keyfigure_cadre_harmonises')->where('zone_id', '=', $zone->zone_id)->whereIn('local_pcode', $countriesList)->where('ch_situation', '=', 'Projected')->get();
                $keyfigure_cadre_harmonises_current = DB::table('keyfigure_cadre_harmonises')->where('zone_id', '=', $zone->zone_id)->whereIn('local_pcode', $countriesList)->where('ch_situation', '=', 'Current')->get();
                $keyfigure_nutritions = DB::table('keyfigure_nutritions')->whereIn('local_pcode', $countriesList)->where('zone_id', '=', $zone->zone_id)->get();
    
                $dataZone=array("zone"=>$zone,"localites"=>$liste_localites,"caseloads"=>$keyfigure_caseloads,"displacements"=>$keyfigure_displacements,"cadre_harmonises_projected"=>$keyfigure_cadre_harmonises_projected,"cadre_harmonises_current"=>$keyfigure_cadre_harmonises_current,"nutrition"=>$keyfigure_nutritions);
                array_push($dataByZone,$dataZone);
            }
        }
        
        //var_dump($dataByZone);

        return view('zone.filter',['datas'=>$dataByZone]);
    }

    public function show_view_filterV2($category,$items,$periodFrom,$periodTo,$adminLevel)
    {
        $countriesList = array();
        $dataByZone = array();
        $dateFrom = $periodFrom."-01-01";
        $dateTo = $periodTo."-12-31";
        
        if($category=="crisis"){
            $crisisList = explode("_", $items);
        }else{
            $crisisList = ["WCA"];
            $countriesList = explode("_", $items);
        }
        
        $zones = DB::table('zones')->whereIn('zone_code', $crisisList)->orderBy('zone_name', 'asc')->get();


        if($category=="crisis"){
            foreach ($zones as $zone) {
                $liste_localites = DB::table('liste_localites')->where('zone_id', '=', $zone->zone_id)->get();
                $keyfigure_caseloads =  DB::table('caseloads_by_regions')->where('zone_id', '=', $zone->zone_id)->whereBetween('caseload_date', [$dateFrom, $dateTo])->orderBy('caseload_date', 'asc')->get();
                $keyfigure_displacements = DB::table('displacements_by_regions')->where('zone_id', '=', $zone->zone_id)->whereBetween('dis_date', [$dateFrom, $dateTo])->orderBy('dis_date', 'asc')->get();
                $keyfigure_cadre_harmonises = DB::table('cadre_harmonises_by_regions')->where('zone_id', '=', $zone->zone_id)->whereBetween('ch_date', [$dateFrom, $dateTo])->orderBy('ch_date', 'asc')->get();
                $keyfigure_nutritions = DB::table('nutrition_by_regions')->where('zone_id', '=', $zone->zone_id)->whereBetween('nut_date', [$dateFrom, $dateTo])->orderBy('nut_date', 'asc')->get();
    
                $dataZone=array("zone"=>$zone,"adminLevel"=>$adminLevel,"localites"=>$liste_localites,"caseloads"=>$keyfigure_caseloads,"displacements"=>$keyfigure_displacements,"cadre_harmonises"=>$keyfigure_cadre_harmonises,"nutrition"=>$keyfigure_nutritions);
                array_push($dataByZone,$dataZone);
            }
        }else{
            foreach ($zones as $zone) {
                $liste_localites = DB::table('liste_localites')->where('zone_id', $zone->zone_id)->whereIn('local_pcode', $countriesList)->orderBy('local_name', 'asc')->get();
                $keyfigure_caseloads = DB::table('caseloads_by_regions')->where('zone_id', $zone->zone_id)->whereBetween('caseload_date', [$dateFrom, $dateTo])->whereIn('local_pcode', $countriesList)->orderBy('caseload_date', 'asc')->get();
                $keyfigure_displacements = DB::table('displacements_by_regions')->where('zone_id', '=', $zone->zone_id)->whereBetween('dis_date', [$dateFrom, $dateTo])->whereIn('local_pcode', $countriesList)->orderBy('dis_date', 'asc')->get();
                $keyfigure_cadre_harmonises = DB::table('cadre_harmonises_by_regions')->where('zone_id', '=', $zone->zone_id)->whereBetween('ch_date', [$dateFrom, $dateTo])->whereIn('local_pcode', $countriesList)->orderBy('ch_date', 'asc')->get();
                $keyfigure_nutritions = DB::table('nutrition_by_regions')->whereIn('local_pcode', $countriesList)->where('zone_id', '=', $zone->zone_id)->whereBetween('nut_date', [$dateFrom, $dateTo])->orderBy('nut_date', 'asc')->get();
    
                $dataZone=array("zone"=>$zone,"adminLevel"=>$adminLevel,"localites"=>$liste_localites,"caseloads"=>$keyfigure_caseloads,"displacements"=>$keyfigure_displacements,"cadre_harmonises"=>$keyfigure_cadre_harmonises,"nutrition"=>$keyfigure_nutritions,"adminLevel"=>$adminLevel);
                array_push($dataByZone,$dataZone);
            }
        }
        
        //var_dump($dataByZone);

        return view('zone.filterV2',['datas'=>$dataByZone,"category"=>$category,"items"=>$items,"adminLevel"=>$adminLevel,"periodFrom"=>$periodFrom,"periodTo"=>$periodTo,"countriesList"=>$countriesList]);
    }

    public function show_view_analyser($id)
    {
        DB::connection('pgsql');
        $zone = zone::where('zone_id', $id)->first();
        $keyfigure_caseloads = keyfigure_caseload::where('zone_id', $id)->get();
        $keyfigure_displacements = DB::table('keyfigure_displacements')->where('zone_id', '=', $id)->where('dis_crise', '=', $zone->zone_code)->get();
        $liste_localites = liste_localite::where('zone_id', $id)->orderBy('local_name', 'asc')->get();
        $keyfigure_cadre_harmonises_current = DB::table('keyfigure_cadre_harmonises')->where('zone_id', '=', $id)->where('ch_situation', '=', 'Current')->get();
        $keyfigure_cadre_harmonises_projected = DB::table('keyfigure_cadre_harmonises')->where('zone_id', '=', $id)->where('ch_situation', '=', 'Projected')->get();
        $keyfigure_nutritions = DB::table('keyfigure_nutritions')->where('zone_id', '=', $id)->get();

        return view('zone.analyser',[
            'datas'=>$zone,
            'liste_localites'=>$liste_localites,
            'keyfigure_caseloads'=>$keyfigure_caseloads,
            'keyfigure_displacements'=>$keyfigure_displacements,
            'keyfigure_cadre_harmonises_current'=>$keyfigure_cadre_harmonises_current,
            'keyfigure_cadre_harmonises_projected'=>$keyfigure_cadre_harmonises_projected,
            'keyfigure_nutritions'=>$keyfigure_nutritions,
            ]);
    }
    public function show_view_analyser_avance()
    {
        DB::connection('pgsql');
        $caseloads_by_regions = DB::table('caseloads_by_regions')->get();
        $displacements_by_regions = DB::table('displacements_by_regions')->get();
        $cadre_harmonises_by_regions = DB::table('cadre_harmonises_by_regions')->get();
        $nutrition_by_regions = DB::table('nutrition_by_regions')->get();

        return view('zone.analyseravance',[
            'caseloads_by_regions'=>$caseloads_by_regions,
            'displacements_by_regions'=>$displacements_by_regions,
            'cadre_harmonises_by_regions'=>$cadre_harmonises_by_regions,
            'nutrition_by_regions'=>$nutrition_by_regions,
            ]);
    }

    public function show_view_charts($id)
    {
        DB::connection('pgsql');
        $zone = zone::where('zone_id', $id)->first();
        $trends_by_years = DB::table('trends_by_years')
        ->select(DB::raw('zone_code,zone_name,zone_id,t_category,t_year,SUM(t_value) as t_value'))
        ->where('zone_id', '=', $id)->orderBy('t_year', 'asc')->groupBy("zone_code","zone_name","zone_id","t_category","t_year")->get();

        return view('zone.charts',[
            'datas'=>$zone,
            'trends_by_years'=>$trends_by_years,
            ]);
    }

    public function show_view_manage_consulter($id)
    {
        DB::connection('pgsql');
        $zone = zone::where('zone_id', $id)->first();
        $liste_localites = liste_localite::where('zone_id', $id)->get();
        
        return view('zone.manageconsulter',[
            'datas'=>$zone,
            'liste_localites'=>$liste_localites
            ]);
    }

    public function show_view_ajouter()
    {
        DB::connection('pgsql');
        return view('zone.ajouter');
    }

    public function show_view_modifier($id)
    {
        DB::connection('pgsql');
        $zone = zone::where('zone_id', $id)->first();
        return view('zone.modifier',['datas'=>$zone]);
    }

    public function show_view_delete($id)
    {
        DB::connection('pgsql');
        $zone = zone::where('zone_id', $id)->first();
        return view('zone.deletezone',['datas'=>$zone]);
    }

    public function update()
    {
        DB::connection('pgsql');
        $zone = zone::where('zone_id', $_POST['zone_id'])->first();
        $zone->zone_code = $_POST['zone_code'];
        $zone->zone_name = $_POST['zone_name'];
        $zone->save();
        $zoneUpdated = zone::where('zone_id', $_POST['zone_id'])->first();
        $liste_localites = liste_localite::where('zone_id', $_POST['zone_id'])->get();
        return view('zone.consulter',['datas'=>$zoneUpdated,'liste_localites'=>$liste_localites]);
    }
    
    public function delete()
    {
        if($_POST['delete']=="DELETE"){
            DB::connection('pgsql');
            $zone = zone::where('zone_id', $_POST['zone_id'])->first();
            $zone->forceDelete();
            return redirect('zones');
        }else{
            return back()->with('msg', 'Type DELETE in all caps !');
        }
    }

    public function massdelete()
    {
        if($_POST['delete']=="DELETE multiples"){
            $keys = array_keys($_POST);
            DB::connection('pgsql');
            foreach($keys as $key){
                $key = str_replace("_","",$key);
                if(substr($key,0,8)=="checkbox"){
                    $id = str_replace(' ','',substr($key,8));
                    $zone = zone::where('zone_id', $id)->first();
                    $zone->forceDelete();
                }
            }
            return redirect('zones');
        }else{
            return back()->with('msg', 'Please type exactly <strong>DELETE multiples</strong> !');
        }
    }

    public function add()
    {
        DB::connection('pgsql');
        $zone = new zone;
        $zone->zone_id = self::generateID();
        $zone->zone_code = $_POST['zone_code'];
        $zone->zone_name = $_POST['zone_name'];
        $zone->save();
        return redirect('zone/'.$zone->zone_id);
    }

    private function generateID(){
        $id=date('YmdHis').rand (0, 9999);
        return $id;
    }
}
