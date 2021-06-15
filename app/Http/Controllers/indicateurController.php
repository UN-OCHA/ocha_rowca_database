<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;
use App\Imports\Import;
use Illuminate\Support\Facades\DB;
use App\kf_indicator as kf_indicator;
use App\ficheindicateur as ficheindicateur;
use App\kf_subcategory as kf_subcategory;
use App\methodedecalcul as methodedecalcul;


class indicateurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function liste()
    {
        DB::connection('pgsql');
        $indicateurs = kf_indicator::all();
        return view('indicateur.listeIndicateurs',['indicateurs'=>$indicateurs]);
    }

    public function show_view_consulter($id)
    {
        DB::connection('pgsql');
        $indicateur = ficheindicateur::where('kfind_id', $id)->first();
        return view('indicateur.consulterIndicateur',['indicateur'=>$indicateur]);
    }

    public function show_view_ajouter()
    {
        DB::connection('pgsql');
        $methodecalcul = methodedecalcul::all();
        $subcategories = kf_subcategory::all();
        return view('indicateur.ajouterIndicateur',['methodecalculs'=>$methodecalcul,'subcategories'=>$subcategories]);
    }

    public function show_view_modifier($id)
    {
        DB::connection('pgsql');
        $indicateur = ficheindicateur::where('kfind_id', $id)->first();
        $methodecalcul = methodedecalcul::all();
        $subcategories = kf_subcategory::all();
        return view('indicateur.modifierIndicateur',['indicateur'=>$indicateur,'methodecalculs'=>$methodecalcul,'subcategories'=>$subcategories]);
    }

    public function show_view_delete($id)
    {
        DB::connection('pgsql');
        $indicateur = ficheindicateur::where('kfind_id', $id)->first();
        $methodecalcul = methodedecalcul::all();
        $subcategories = kf_subcategory::all();
        return view('indicateur.deleteIndicateur',['indicateur'=>$indicateur,'methodecalculs'=>$methodecalcul,'subcategories'=>$subcategories]);
    }

    public function update()
    {
        DB::connection('pgsql');
        $indicateur = kf_indicator::where('kfind_id', $_POST['kfind_id'])->first();
        $indicateur->kfindic_caption_fr = $_POST['kfindic_caption_fr'];
        $indicateur->kfindic_caption_en = $_POST['kfindic_caption_en'];
        $indicateur->kfindic_source = $_POST['kfindic_source'];
        $indicateur->id_method = $_POST['id_method'];
        $indicateur->kfsubcategory_id = $_POST['kfsubcategory_id'];
        $indicateur->save();
        $indicateurUpdated = ficheindicateur::where('kfind_id', $_POST['kfind_id'])->first();
        return view('indicateur.consulterIndicateur',['indicateur'=>$indicateurUpdated]);
    }
    public function delete()
    {
        if($_POST['delete']=="DELETE"){
            DB::connection('pgsql');
            $indicateur = kf_indicator::where('kfind_id', $_POST['kfind_id'])->first();
            $indicateur->forceDelete();
            return redirect('indicateurs');
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
                    echo($id)."</br>";
                    $indicateur = kf_indicator::where('kfind_id', $id)->first();
                    $indicateur->forceDelete();
                }
            }
            return redirect('indicateurs');
        }else{
            return back()->with('msg', 'Please type exactly <strong>DELETE multiples</strong> !');
        }


       
    }
    public function add()
    {
        DB::connection('pgsql');
        $indicateur = new kf_indicator;
        $indicateur->kfind_id = self::generateID();
        $indicateur->kfindic_caption_fr = $_POST['kfindic_caption_fr'];
        $indicateur->kfindic_caption_en = $_POST['kfindic_caption_en'];
        $indicateur->kfindic_source = $_POST['kfindic_source'];
        $indicateur->id_method = $_POST['id_method'];
        $indicateur->kfsubcategory_id = $_POST['kfsubcategory_id'];
        $indicateur->save();
        return redirect('indicateur/'.$indicateur->kfind_id);
    }

    private function generateID(){
        $id=date('YmdHis').rand (0, 9999);
        return $id;
    }
}
