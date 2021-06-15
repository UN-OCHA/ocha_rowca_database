<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\header as header;
use App\fiche_header as fiche_header;
use App\kf_indicator as kf_indicator;
use App\type_header as type_header;


class headerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function liste()
    {
        DB::connection('pgsql');
        $datas = header::all();
        return view('header.liste',['datas'=>$datas]);
    }

    public function show_view_consulter($id)
    {
        DB::connection('pgsql');
        $datas = fiche_header::where('header_id', $id)->first();
        return view('header.consulter',['datas'=>$datas]);
    }

    /*
    public function show_view_ajouter()
    {
        DB::connection('pgsql');
        $headers = header::all();
        return view('header.ajouter');
    }*/
/*
    public function show_view_modifier($id)
    {
        DB::connection('pgsql');
        $datas = header::where('header_id', $id)->first();
        $type_headers = type_header::all();
        $subcategories = kf_indicator::all();
        return view('header.modifier',['datas'=>$datas]);
    }*/

    public function show_view_delete($id)
    {
        DB::connection('pgsql');
        $datas = header::where('header_id', $id)->first();
        return view('header.delete',['datas'=>$datas]);
    }

    /*
    public function update()
    {
        DB::connection('pgsql');
        $object = header::where('header_id', $_POST['header_id'])->first();
        $object->kfcateg_caption_en = $_POST['kfcateg_caption_en'];
        $object->kfcateg_caption_fr = $_POST['kfcateg_caption_fr'];
        $object->save();
        $objectUpdated = header::where('header_id', $_POST['header_id'])->first();
        return view('header.consulter',['datas'=>$objectUpdated]);
    }*/

    public function delete()
    {
        if($_POST['delete']=="DELETE"){
            DB::connection('pgsql');
            $indicateur = header::where('header_id', $_POST['header_id'])->first();
            $indicateur->forceDelete();
            return redirect('headers');
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
                    $object = header::where('header_id', $id)->first();
                    $object->forceDelete();
                }
            }
            return redirect('headers');
        }else{
            return back()->with('msg', 'Please type exactly <strong>DELETE multiples</strong> !');
        }
    }

    /*
    public function add()
    {
        DB::connection('pgsql');
        $object = new header;
        $object->header_id = self::generateID();
        $object->kfcateg_caption_fr = $_POST['kfcateg_caption_fr'];
        $object->kfcateg_caption_en = $_POST['kfcateg_caption_en'];
        $object->save();
        return redirect('category/'.$object->header_id);
    }*/

    private function generateID(){
        $id=date('YmdHis').rand (0, 9999);
        return $id;
    }
}
