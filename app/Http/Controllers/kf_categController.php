<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\kf_categ as kf_categ;


class kf_categController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function liste()
    {
        DB::connection('pgsql');
        $datas = kf_categ::all();
        return view('kf_categ.liste',['datas'=>$datas]);
    }

    public function show_view_consulter($id)
    {
        DB::connection('pgsql');
        $datas = kf_categ::where('kfcateg_id', $id)->first();
        return view('kf_categ.consulter',['datas'=>$datas]);
    }

    public function show_view_ajouter()
    {
        DB::connection('pgsql');
        $kf_categs = kf_categ::all();
        return view('kf_categ.ajouter');
    }

    public function show_view_modifier($id)
    {
        DB::connection('pgsql');
        $datas = kf_categ::where('kfcateg_id', $id)->first();
        return view('kf_categ.modifier',['datas'=>$datas]);
    }

    public function show_view_delete($id)
    {
        DB::connection('pgsql');
        $datas = kf_categ::where('kfcateg_id', $id)->first();
        return view('kf_categ.delete',['datas'=>$datas]);
    }

    public function update()
    {
        DB::connection('pgsql');
        $object = kf_categ::where('kfcateg_id', $_POST['kfcateg_id'])->first();
        $object->kfcateg_caption_en = $_POST['kfcateg_caption_en'];
        $object->kfcateg_caption_fr = $_POST['kfcateg_caption_fr'];
        $object->save();
        $objectUpdated = kf_categ::where('kfcateg_id', $_POST['kfcateg_id'])->first();
        return view('kf_categ.consulter',['datas'=>$objectUpdated]);
    }
    public function delete()
    {
        if($_POST['delete']=="DELETE"){
            DB::connection('pgsql');
            $indicateur = kf_categ::where('kfcateg_id', $_POST['kfcateg_id'])->first();
            $indicateur->forceDelete();
            return redirect('categories');
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
                    $object = kf_categ::where('kfcateg_id', $id)->first();
                    $object->forceDelete();
                }
            }
            return redirect('categories');
        }else{
            return back()->with('msg', 'Please type exactly <strong>DELETE multiples</strong> !');
        }


       
    }
    public function add()
    {
        DB::connection('pgsql');
        $object = new kf_categ;
        $object->kfcateg_id = self::generateID();
        $object->kfcateg_caption_fr = $_POST['kfcateg_caption_fr'];
        $object->kfcateg_caption_en = $_POST['kfcateg_caption_en'];
        $object->save();
        return redirect('category/'.$object->kfcateg_id);
    }

    private function generateID(){
        $id=date('YmdHis').rand (0, 9999);
        return $id;
    }
}
