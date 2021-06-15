<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;
use App\Imports\Import;
use Illuminate\Support\Facades\DB;
use App\localite as localite;
use App\zone_avoir_localite as zone_avoir_localite;



class localiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function liste()
    {
        
        DB::connection('pgsql');
        $localites = localite::all();
        return view('localite.liste',['datas'=>$localites]);
    }
    public function show_view_consulter($id)
    {
        DB::connection('pgsql');
        $localite = localite::where('local_id', $id)->first();
        $zoneAvoirLoc = DB::table('zone_avoir_localites')->where('local_id', '=',  $localite->local_id)->get();
        $zone = DB::table('zones')->where('zone_id', '=',  $zoneAvoirLoc[0]->ZONE_ID)->first();
        //$keyfigure_caseloads = keyfigure_caseload::where('local_id', $id)->get();
        $keyfigure_caseloads = DB::table('keyfigure_caseloads')->where('local_id', '=', $id)->get();
        $keyfigure_displacements = DB::table('keyfigure_displacements')->where('dis_crise', '=', $zone->zone_code)->where('local_id', '=', $localite->local_id)->get();
        $keyfigure_cadre_harmonises_projected = DB::table('keyfigure_cadre_harmonises')->where('local_id', '=', $id)->where('ch_situation', '=', 'Projected')->get();
        $keyfigure_cadre_harmonises_current = DB::table('keyfigure_cadre_harmonises')->where('local_id', '=', $id)->where('ch_situation', '=', 'Current')->get();
        $keyfigure_nutritions = DB::table('keyfigure_nutritions')->where('local_id', '=', $id)->get();
        
      
        return view('localite.consulter',[
            'datas'=>$localite,
            'zone'=>$zone,
            'keyfigure_caseloads'=>$keyfigure_caseloads,
            'keyfigure_displacements'=>$keyfigure_displacements,
            'keyfigure_cadre_harmonises_projected'=>$keyfigure_cadre_harmonises_projected,
            'keyfigure_cadre_harmonises_current'=>$keyfigure_cadre_harmonises_current,
            'keyfigure_nutritions'=>$keyfigure_nutritions,
            ]);
    }

    public function show_view_analyser($id)
    {
        DB::connection('pgsql');
        $localite = localite::where('local_id', $id)->first();
        $zoneAvoirLoc = DB::table('zone_avoir_localites')->where('local_id', '=',  $localite->local_id)->get();
        $zone = DB::table('zones')->where('zone_id', '=',  $zoneAvoirLoc[0]->ZONE_ID)->first();
        //$keyfigure_caseloads = keyfigure_caseload::where('local_id', $id)->get();
        $keyfigure_caseloads = DB::table('keyfigure_caseloads')->where('local_id', '=', $id)->get();
        $keyfigure_displacements = DB::table('keyfigure_displacements')->where('dis_crise', '=', $zone->zone_code)->where('local_id', '=', $localite->local_id)->get();
        $keyfigure_cadre_harmonises_projected = DB::table('keyfigure_cadre_harmonises')->where('local_id', '=', $id)->where('ch_situation', '=', 'Projected')->get();
        $keyfigure_cadre_harmonises_current = DB::table('keyfigure_cadre_harmonises')->where('local_id', '=', $id)->where('ch_situation', '=', 'Current')->get();
        $keyfigure_nutritions = DB::table('keyfigure_nutritions')->where('local_id', '=', $id)->get();

        return view('localite.analyser',[
            'datas'=>$localite,
            'zone'=>$zone,
            'keyfigure_caseloads'=>$keyfigure_caseloads,
            'keyfigure_displacements'=>$keyfigure_displacements,
            'keyfigure_cadre_harmonises_projected'=>$keyfigure_cadre_harmonises_projected,
            'keyfigure_cadre_harmonises_current'=>$keyfigure_cadre_harmonises_current,
            'keyfigure_nutritions'=>$keyfigure_nutritions,
            ]);
    }
    public function show_view_charts($id)
    {
        DB::connection('pgsql');
        $localite = localite::where('local_id', $id)->first();
        $zoneAvoirLoc = DB::table('zone_avoir_localites')->where('local_id', '=',  $localite->local_id)->get();
        $zone = DB::table('zones')->where('zone_id', '=',  $zoneAvoirLoc[0]->ZONE_ID)->first();
        $trends_by_years = DB::table('trends_by_years')
        ->select(DB::raw('local_pcode, local_name, local_id,t_category,t_year,SUM(t_value) as t_value'))
        ->where('local_id', '=', $id)->orderBy('t_year', 'asc')->groupBy("local_pcode","local_name","local_id","t_category","t_year")->get();

        return view('localite.charts',[
            'datas'=>$localite,
            'zone'=>$zone,
            'trends_by_years'=>$trends_by_years,
            ]);
    }


    public function show_view_manage_consulter($id)
    {
        DB::connection('pgsql');
        $localite = localite::where('local_id', $id)->first();
        $zoneAvoirLoc = DB::table('zone_avoir_localites')->where('local_id', '=',  $localite->local_id)->get();

        $zone = DB::table('zones')->where('zone_id', '=',  $zoneAvoirLoc[0]->ZONE_ID)->get();
        return view('localite.manageconsulter',['datas'=>$localite,'zone'=>$zone]);
    }

    public function show_view_ajouter($zone_id)
    {
        DB::connection('pgsql');
        return view('localite.ajouter',['zone_id'=>$zone_id]);
    }

    public function show_view_modifier($id)
    {
        DB::connection('pgsql');
        $localite = localite::where('local_id', $id)->first();
        $zoneAvoirLoc = DB::table('zone_avoir_localites')->where('local_id', '=',  $localite->local_id)->get();
        $zone = DB::table('zones')->where('zone_id', '=',  $zoneAvoirLoc[0]->ZONE_ID)->get();

        return view('localite.modifier',['datas'=>$localite,'zone'=>$zone]);
    }

    public function show_view_delete($id)
    {
        DB::connection('pgsql');
        $localite = localite::where('local_id', $id)->first();
        $zoneAvoirLoc = DB::table('zone_avoir_localites')->where('local_id', '=',  $localite->local_id)->get();
        $zone = DB::table('zones')->where('zone_id', '=',  $zoneAvoirLoc[0]->ZONE_ID)->get();
        return view('localite.delete',['datas'=>$localite,'zone'=>$zone]);
    }

    public function update()
    {
        DB::connection('pgsql');
        $localite = localite::where('local_id', $_POST['local_id'])->first();
        $localite->local_name = $_POST['local_name'];
        $localite->local_pcode = $_POST['local_pcode'];
        $localite->local_admin_level = $_POST['local_admin_level'];
        $localite->local_country = $_POST['local_country'];
        $localite->save();
        $localiteUpdated = localite::where('local_id', $_POST['local_id'])->first();

        $zoneAvoirLoc = DB::table('zone_avoir_localites')->where('local_id', '=',  $localite->local_id)->get();
        $zone = DB::table('zones')->where('zone_id', '=',  $zoneAvoirLoc[0]->ZONE_ID)->get();
        return view('localite.manageconsulter',['datas'=>$localiteUpdated,'zone'=>$zone]);
    }
    
    public function delete()
    {
        if($_POST['delete']=="DELETE"){
            DB::connection('pgsql');
            $zoneAvoirLoc = DB::table('zone_avoir_localites')->where('local_id', '=', $_POST['local_id'])->get();
            $zoneID = $zoneAvoirLoc[0]->ZONE_ID;
            DB::table('zone_avoir_localites')->where('local_id', '=', $_POST['local_id'])->delete();
            DB::table('localites')->where('local_id', '=', $_POST['local_id'])->delete();
            return redirect('managezone/'.$zoneID);
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
                    $localite = localite::where('local_id', $id)->first();
                    $localite->forceDelete();
                }
            }
            return redirect('localites');
        }else{
            return back()->with('msg', 'Please type exactly <strong>DELETE multiples</strong> !');
        }
    }

    public function add()
    {
        DB::connection('pgsql');
        $localite = new localite;
        $id = self::generateID();
        $localite->local_id =$id;
        $localite->local_name = $_POST['local_name'];
        $localite->local_pcode = $_POST['local_pcode'];
        $localite->local_admin_level = $_POST['local_admin_level'];
        $localite->save();

        $zone_avoir_localite = new zone_avoir_localite;
        $zone_avoir_localite->ZONE_ID = $_POST['zone_id'];
        $zone_avoir_localite->local_id = $id;
        $zone_avoir_localite->save();

        return redirect('managezone/'.$_POST['zone_id']);
    }

    private function generateID(){
        $id=date('YmdHis').rand (0, 9999);
        return $id;
    }
}
