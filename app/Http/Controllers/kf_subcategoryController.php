<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\kf_subcategory as kf_subcategory;
use App\methodedecalcul as methodedecalcul;
use App\kf_categ as kf_categ;
use App\fiche_subcategory as fiche_subcategory;


class kf_subcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function liste()
    {
        DB::connection('pgsql');
        $datas = kf_subcategory::all();
        return view('kf_subcategory.liste',['datas'=>$datas]);
    }

    public function show_view_consulter($id)
    {
        DB::connection('pgsql');
        $datas = fiche_subcategory::where('kfsubcategory_id', $id)->first();
        return view('kf_subcategory.consulter',['datas'=>$datas]);
    }

    public function show_view_ajouter()
    {
        DB::connection('pgsql');
        $kf_categs = kf_categ::all();
        return view('kf_subcategory.ajouter',['kf_categs'=>$kf_categs]);
    }

    public function show_view_modifier($id)
    {
        DB::connection('pgsql');
        $datas = fiche_subcategory::where('kfsubcategory_id', $id)->first();
        $kf_categs = kf_categ::all();
        return view('kf_subcategory.modifier',['datas'=>$datas,'kf_categs'=>$kf_categs]);
    }

    public function show_view_delete($id)
    {
        DB::connection('pgsql');
        $datas = fiche_subcategory::where('kfsubcategory_id', $id)->first();
        $kf_categs = kf_categ::all();
        return view('kf_subcategory.delete',['datas'=>$datas,'kf_categs'=>$kf_categs]);
    }

    public function update()
    {
        DB::connection('pgsql');
        $object = kf_subcategory::where('kfsubcategory_id', $_POST['kfsubcategory_id'])->first();
        $object->kfsubcategory_caption_en = $_POST['kfsubcategory_caption_en'];
        $object->kfsubcategory_caption_fr = $_POST['kfsubcategory_caption_fr'];
        $object->kfcateg_id = $_POST['kfcateg_id'];
        $object->save();
        $objectUpdated = fiche_subcategory::where('kfsubcategory_id', $_POST['kfsubcategory_id'])->first();
        return view('kf_subcategory.consulter',['datas'=>$objectUpdated]);
    }
    public function delete()
    {
        if($_POST['delete']=="DELETE"){
            DB::connection('pgsql');
            $indicateur = kf_subcategory::where('kfsubcategory_id', $_POST['kfsubcategory_id'])->first();
            $indicateur->forceDelete();
            return redirect('subcategories');
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
                    $indicateur = kf_subcategory::where('kfsubcategory_id', $id)->first();
                    $indicateur->forceDelete();
                }
            }
            return redirect('subcategories');
        }else{
            return back()->with('msg', 'Please type exactly <strong>DELETE multiples</strong> !');
        }


       
    }
    public function add()
    {
        DB::connection('pgsql');
        $object = new kf_subcategory;
        $object->kfsubcategory_id = self::generateID();
        $object->kfsubcategory_caption_fr = $_POST['kfsubcategory_caption_fr'];
        $object->kfsubcategory_caption_en = $_POST['kfsubcategory_caption_en'];
        $object->kfcateg_id = $_POST['kfcateg_id'];
        $object->save();
        return redirect('subcategories');
    }

    private function generateID(){
        $id=date('YmdHis').rand (0, 9999);
        return $id;
    }
}
