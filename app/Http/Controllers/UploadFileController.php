<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;
use App\Imports\Import;
use Illuminate\Support\Facades\DB;
use App\kf_indicator as kf_indicator;
use App\location as location;
use App\header as header;
use App\file as file;
use App\location_type as location_type;
use App\kf_report as kf_report;
use App\kf_disag as kf_disag;
use App\type_header as type_header;

class UploadFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
       
        return view('uploadfile');
    }


    public function importData(Request $request){
        $importBrut = $request['import'];
        $import = json_encode($importBrut);

        // $import = '[{"sheetInfo":[{"feuille":"manyPcodes"},{"importInfo":[{"headerInfo":[{"headerName":"Admin1"},{"headerIndex":"1"},{"headerType":"location"},{"HeaderIndicator":"labelAdmin1"},{"disaggregationTarget":null}]},{"headerInfo":[{"headerName":"PIN"},{"headerIndex":"2"},{"headerType":"keyFigure"},{"HeaderIndicator":"9"},{"disaggregationTarget":null}]},{"headerInfo":[{"headerName":"Homme"},{"headerIndex":"4"},{"headerType":"disaggregation"},{"HeaderIndicator":"20181213160602939i"},{"disaggregationTarget":"PIN"}]},{"headerInfo":[{"headerName":"Femme"},{"headerIndex":"5"},{"headerType":"disaggregation"},{"HeaderIndicator":"201812131606023072i"},{"disaggregationTarget":"PIN"}]}]},{"sheetData":[["Country","Admin1","PIN","PT","Homme","Femme","Year"],["Burkina Faso","Centre","939148","939148","350000","589148","2015"],["Camerou","Burkina Faso","2070000","1600000","1530000","540000","2015"],["Mauritania","kyar","3000000","2500000","1524000","1476000","2015"]]}]}]';
        $jsonnn = json_decode($import);
        ////print_r( $jsonnn);
        
        //CREATE THE FILE
        $IdFile=date('YmdHis').rand (0, 9999)."i";
        $file = new file;
        $file->file_id = $IdFile;
        $file->file_name = "Test";
        $file->file_type = "Excel";
        $file->save();
        $importState = false;
        $importLog = "";
        $reportNumber=0;

        foreach ($jsonnn as $sheetInfo){
            //LECTURE DES DONNEES
            $sheetName = $sheetInfo->sheetInfo[0]->feuille;
            $importInfo = $sheetInfo->sheetInfo[1]->importInfo;
            $sheetData = $sheetInfo->sheetInfo[2]->sheetData;

            //TRAITEMENT DES DONNEES
            $line = 0;
            $LastCodeNiveauAdmin = 0;
            $idSelectedLocation = "<samp>";
            $date = "";
            
            foreach ($sheetData as $data){
                if($line!=0){
                    $importLog .="</br></br></br><h5 class='titreBlocLog'>Traitement ligne ".$line."</h5><p>";
                    
                    $numColone = 0;
                    $locations = array();
                    $keyfigures = array();
                    $disaggregations = array();
                    $date = null;
                    $Admin0 = null;
                    $Admin1 = null;
                    $Admin2 = null;
                    $Admin3 = null;
                    $Admin4 = null;
                    $Admin5 = null;

                    $Admin0new = false;
                    $Admin1new = false;
                    $Admin2new = false;
                    $Admin3new = false;
                    $Admin4new = false;
                    $Admin5new = false;

                    $parentAdmin0Id = "";
                    $parentAdmin1Id = "";
                    $parentAdmin2Id = "";
                    $parentAdmin3Id = "";
                    $parentAdmin4Id = "";

                    $nbLocation = 0;
                    $adminLevel = 0;
                    

                    foreach ($data as $colonne){
                       
                        $locationFound = false;
                        

                        foreach ($importInfo as $header){
                            if($numColone == $header->headerInfo[1]->headerIndex){



                                //RECHECHE DE LA LOCALISATION
                                if($header->headerInfo[2]->headerType == "location"){
                                    
                                    $pcodeIso3 = $colonne;
                                    $niveauAdmin = substr($header->headerInfo[3]->HeaderIndicator,5,6);
                                    $typeAdminSearch = substr($header->headerInfo[3]->HeaderIndicator,0,5);
                                    $locationToUpdate = false;
                                    $locationUpdateComment = "";

                                    

                                    if($typeAdminSearch=="pcode"){
                                        //RECHERCHE PAR PCODE
                                        $object = location::where('location_pcode_iso3', $colonne)->first();
                                        
                                    }else{
                                        //RECHERCHE PAR LABEL
                                        //select * from locations where upper(location_caption_en) = upper('centre')
                                        $pcodeIso3 = "";
                                        $locationToUpdate = true;
                                        $locationUpdateComment = "Without pcode";

                                        switch($niveauAdmin){
                                            case "Admin0":
                                                $adminLevel = 0;
                                            break;
                                            case "Admin1":
                                                $adminLevel = 1;
                                            break;
                                            case "Admin2":
                                                $adminLevel = 2;
                                            break;
                                            case "Admin3":
                                                $adminLevel = 3;
                                            break;
                                            case "Admin4":
                                                $adminLevel = 4;
                                            break;
                                            case "Admin5":
                                                $adminLevel = 5;
                                            break;

                                        }
                                        $valeur = strtoupper($colonne);
                                        $object = DB::table("locations")->whereRaw('upper(location_caption_en) = ?', $valeur)->where('location_admin_level', $adminLevel)->first();
                                    }

                                   
                                    $importLog .="Localisation trouvé : '".$colonne."' (".$typeAdminSearch." ".$niveauAdmin.") à la colonne (".$numColone.")</br>";
                                    
                                    if(!$object){
                                        $importLog .="DB : Localisation '".$colonne."' (".$typeAdminSearch." ".$niveauAdmin.") non trouvé dans la base de données elle sera crée</br>";
                                        $IdLocation=date('YmdHis').rand (0, 9999)."i";
                                       
                                        switch($niveauAdmin){
                                            case "Admin0":
                                                $codeNiveauAdmin = 0;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 2;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin0 = $location;
                                                $parentAdmin0Id = $IdLocation;
                                                $Admin0new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin0Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin1":
                                                $codeNiveauAdmin = 1;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 3;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin0Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin1 = $location;
                                                $parentAdmin1Id = $IdLocation;
                                                $Admin1new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin1Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin2":
                                                $codeNiveauAdmin = 2;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 4;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin1Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin2 = $location;
                                                $parentAdmin2Id = $IdLocation;
                                                $Admin2new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin2Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin3":
                                                $codeNiveauAdmin = 3;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 5;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin2Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin3 = $location;
                                                $parentAdmin3Id = $IdLocation;
                                                $Admin3new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin3Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin4":
                                                $codeNiveauAdmin = 4;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 6;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin3Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin4 = $location;
                                                $parentAdmin4Id = $IdLocation;
                                                $Admin4new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin4Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin5":
                                                $codeNiveauAdmin = 5;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 7;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin4Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin5 = $location;
                                                $parentAdmin5Id = $IdLocation;
                                                $Admin5new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                }
                                            break;
                                        }
                                    }else{
                                        $importLog .="DB : Localisation '".$colonne."' (".$typeAdminSearch." ".$niveauAdmin.") trouvé dans la base de données id='".$object->location_id."'</br>";
                                        array_push($locations, array("location_id" =>$object->location_id,"location_value" =>$colonne,"niveauAdmin" =>  $niveauAdmin));
                                        $IdLocation = $object->location_id;
                                        switch($niveauAdmin){
                                            case "Admin0":
                                                $codeNiveauAdmin = 0;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin0Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin1":
                                                $codeNiveauAdmin = 1;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin1Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin2":
                                                $codeNiveauAdmin = 2;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin2Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin3":
                                                $codeNiveauAdmin = 3;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin3Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin4":
                                                $codeNiveauAdmin = 4;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin4Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin5":
                                                $codeNiveauAdmin = 5;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                }
                                            break;
                                        }

                                    }

                                    $nbLocation++;
                                }



                                //RECHERCHE DE L'INDICATEUR
                                if($header->headerInfo[2]->headerType == "keyFigure"){
                                    $header_name = $header->headerInfo[0]->headerName;
                                    $importLog .="</br>keyFigure trouvé : '".$colonne."' (".$header_name.") à la colonne (".$numColone.")</br>";
                                   
                                    //RECHERCHE DU TYPE HEADER
                                    $object = header::where('header_name', $header_name)->first();
                                    $objectTypeHeader = type_header::where('type_header_code', 'keyFigure')->first();

                                    if(!$object){
                                        $importLog .="DB : Header '".$header_name."' non trouvé dans la table des synonymes, elle sera crée</br>";

                                        $IdHeader=date('YmdHis').rand (0, 9999)."i";
                                        $Header = new Header;
                                        $Header->header_id = $IdHeader;
                                        $Header->kfind_id = $header->headerInfo[3]->HeaderIndicator;
                                        $Header->type_header_id = $objectTypeHeader->type_header_id;
                                        $Header->header_name = $header_name;
                                        $Header->save();
                                        
                                        $importLog .="DB : Header '".$header_name."' créé dans la table des synonymes</br>";
                                        array_push($keyfigures, array("kfind_id" =>$header->headerInfo[3]->HeaderIndicator,"header_name" =>$header_name,"valeur" =>$colonne));
                                        
                                    }else{
                                        $importLog .="DB : Header '".$header_name."' = '".$object->header_name."' trouvé dans la table des synonymes</br>";
                                      
                                        array_push($keyfigures, array("kfind_id" =>$object->kfind_id,"header_name" =>$header_name,"valeur" =>$colonne));
                                    }
                                }
                                
                                

                                //RECHERCHE DE LA DATE
                                if($header->headerInfo[2]->headerType == "date"){
                                    
                                    $dateFormat = $header->headerInfo[3]->HeaderIndicator;
                                    $jour = "";
                                    $mois = "";
                                    $annee = "";
                                    switch($dateFormat){
                                        case "AAAA":
                                            $date = date_format(date_create($colonne."-12-31"),"Y/m/d");
                                        break;
                                        case "AAAA-MM-DD":
                                            if(strlen($colonne)==10){
                                                $jour = substr($colonne,8,2);
                                                $mois = substr($colonne,5,2);
                                                $annee = substr($colonne,0,4);
                                                $date = $annee."/".$mois."/".$jour;
                                            }else{
                                                if(strlen($colonne)==8){
                                                    $jour = substr($colonne,6,2);
                                                    $mois = substr($colonne,4,2);
                                                    $annee = substr($colonne,0,4);
                                                    $date = $annee."/".$mois."/".$jour;
                                                }else{
                                                    //ERREUR SUR LE FORMAT DE LA DATE
                                                }
                                            }
                                        break;
                                        case "DD-MM-AAAA":
                                            if(strlen($colonne)==10){
                                                $jour = substr($colonne,0,2);
                                                $mois = substr($colonne,3,2);
                                                $annee = substr($colonne,6,4);
                                                $date = $annee."/".$mois."/".$jour;
                                            }else{
                                                if(strlen($colonne)==8){
                                                    $jour = substr($colonne,0,2);
                                                    $mois = substr($colonne,2,2);
                                                    $annee = substr($colonne,4,4);
                                                    $date = $annee."/".$mois."/".$jour;
                                                }else{
                                                    //ERREUR SUR LE FORMAT DE LA DATE
                                                }
                                            }
                                            
                                        break;
                                        case "AAAA-DD-MM":
                                            if(strlen($colonne)==10){
                                                $jour = substr($colonne,5,2);
                                                $mois = substr($colonne,8,2);
                                                $annee = substr($colonne,0,4);
                                                $date = $annee."/".$mois."/".$jour;
                                            }else{
                                                if(strlen($colonne)==8){
                                                    $jour = substr($colonne,4,2);
                                                    $mois = substr($colonne,6,2);
                                                    $annee = substr($colonne,0,4);
                                                    $date = $annee."/".$mois."/".$jour;
                                                }else{
                                                    //ERREUR SUR LE FORMAT DE LA DATE
                                                }
                                            }
                                        break;
                                    }
                                    $importLog .="Date trouvé '".$header_name."' (".$dateFormat.") trouvé à la colonne (".$numColone.")</br>";
                                }



                                //RECHERCHE DE LA DISAGGREGATION
                                if($header->headerInfo[2]->headerType == "disaggregation"){
                                    $idDisaggregation = $header->headerInfo[3]->HeaderIndicator; 
                                    $disaggregationTargetIndicator = $header->headerInfo[4]->disaggregationTarget; 
                                    array_push($disaggregations, array("id_disaggregation" =>$idDisaggregation,"disaggregationTargetIndicator" =>$disaggregationTargetIndicator,"valeur" =>$colonne));
 
                                    $importLog .="Désaggregation trouvé trouvé à la colonne (".$numColone.")</br>";
                                }
                            }
                        }

                        

                        
                        $idIndicateur = 0000;


                        

                        $numColone++;
                    }

                    $importLog .="</br>Localisation : Gestion des parents et fils";
                    if($nbLocation>1){
                        if($Admin0!=null){
                            $Admin0->save();
                            $importLog .="DB : Admin0 '".$Admin0->location_caption_en."' (".$Admin0->location_pcode_iso2.") créé sans fils</br>";
                        }
                        
                        if($Admin1!=null){
                            $Admin1->location_parent_id=$parentAdmin0Id;
                            $Admin1->save();
                            $importLog .="DB : Admin1 '".$Admin1->location_caption_en."' (".$Admin1->location_pcode_iso2.") créé avec parent (".$parentAdmin0Id.")</br>";
                        }
                        
                        if($Admin2!=null){
                            $Admin2->location_parent_id=$parentAdmin1Id;
                            $Admin2->save();
                            $importLog .="DB : Admin2 '".$Admin2->location_caption_en."' (".$Admin2->location_pcode_iso2.") créé avec parent (".$parentAdmin1Id.")</br>";
                        }
                        
                        if($Admin3!=null){
                            $Admin3->location_parent_id=$parentAdmin2Id;
                            $Admin3->save();
                            $importLog .="DB : Admin3 '".$Admin3->location_caption_en."' (".$Admin3->location_pcode_iso2.") créé avec parent (".$parentAdmin2Id.")</br>";
                        }
                        
                        if($Admin4!=null){
                            $Admin4->location_parent_id=$parentAdmin3Id;
                            $Admin4->save();
                            $importLog .="DB : Admin4 '".$Admin4->location_caption_en."' (".$Admin4->location_pcode_iso2.") créé avec parent (".$parentAdmin3Id.")</br>";
                        }

                        if($Admin5!=null){
                            $Admin5->location_parent_id=$parentAdmin4Id;
                            $Admin5->save();
                            $importLog .="DB : Admin5 '".$Admin5->location_caption_en."' (".$Admin5->location_pcode_iso2.") créé avec parent (".$parentAdmin4Id.")</br>";
                        }
                    }else{
                        if($Admin0!=null){
                            $Admin0->save();
                            $importLog .="DB : Admin0 '".$Admin0->location_caption_en."' (".$Admin0->location_pcode_iso2.") créé sans parent</br>";
                        }
                        
                        if($Admin1!=null){
                            $Admin1->save();
                            $importLog .="DB : Admin1 '".$Admin1->location_caption_en."' (".$Admin1->location_pcode_iso2.") créé sans parent</br>";
                        }
                        
                        if($Admin2!=null){
                            $Admin2->save();
                            $importLog .="DB : Admin2 '".$Admin2->location_caption_en."' (".$Admin2->location_pcode_iso2.") créé sans parent</br>";
                        }
                        
                        if($Admin3!=null){
                            $Admin3->save();
                            $importLog .="DB : Admin3 '".$Admin3->location_caption_en."' (".$Admin3->location_pcode_iso2.") créé sans parent</br>";
                        }
                        
                        if($Admin4!=null){
                            $Admin4->save();
                            $importLog .="DB : Admin4 '".$Admin4->location_caption_en."' (".$Admin4->location_pcode_iso2.") créé sans parent</br>";
                        }

                        if($Admin5!=null){
                            $Admin5->save();
                            $importLog .="DB : Admin5 '".$Admin4->location_caption_en."' (".$Admin5->location_pcode_iso2.") créé sans parent</br>";
                        }
                    }

                    //VERIIFCATION DATE
                    if($date==""){
                        $date = date("Y/m/d");
                        $importLog .="</br>Vérification de la date</br>";
                        $importLog .="Aucune date présente sur le fichier, le 'report' sera créé avec une date par défaut (".$date.")</br>";
                    }


                    //ENREGISTREMENT DU KF REPORT
                    $importLog .="</br>Enregistrement du 'KF Report'</br>";
                    $importLog .="</br>Location id choisi : ".$idSelectedLocation."</br>";
                    //print_r($locations);
                    //$importLog .="</br>array Key figures </br>";
                    //print_r($keyfigures);
                    //$importLog .="</br>disaggregations </br>";
                    //print_r($disaggregations);
                    //$importLog .="</br>Report Date</br>";
                    //print_r($date);
                    
                    
                    foreach ($keyfigures as $keyfigure){
                        $reportNumber++;
                        //GESTION DES DERNIERES DONNEES
                        DB::table('kf_reports')
                        ->where('location_id', $idSelectedLocation)
                        ->where('kfind_id', $keyfigure['kfind_id'])
                        ->where('kfreport_latest', 'oui')
                        ->update(['kfreport_latest' => 'valid to '.date('Ymd')]);

                        //DB INSERTION DE REPORT
                        $IdReport=date('YmdHis').$reportNumber;
                        $kf_report = new kf_report;
                        $kf_report->kfreport_id = $IdReport;
                        $kf_report->location_id = $idSelectedLocation;
                        $kf_report->kfind_id = $keyfigure['kfind_id'];
                        $kf_report->file_id = $IdFile;
                        $kf_report->kfreport_date = $date;
                        $kf_report->kfreport_value = $keyfigure['valeur'];
                        $kf_report->kfreport_source = "test";
                        $kf_report->kfreport_comment = "test";
                        $kf_report->kfreport_latest = "oui";
                        $kf_report->save();
                       

                        //ENREGISTREMENT DE LA DISAGGREGATION
                        foreach ($disaggregations as $disag){
                            if($disag['disaggregationTargetIndicator'] == $keyfigure['header_name']){
                                
                                $kf_disag = new kf_disag;
                                $kf_disag->kfreport_id = $IdReport;
                                $kf_disag->id_disaggregation = $disag['id_disaggregation'];
                                $kf_disag->disaggregated_value_label = "";
                                $kf_disag->disaggregated_value = $disag['valeur'];
                                $kf_disag->disaggregated_value_comment = "";
                                $kf_disag->save();
                            }
                        }

                        $importLog .="</br>DB : Report created id : ". $IdReport."</p>";
                        
                    }
                    
                }
                $line++;
            }
        }
        $importLog .="<samp>";
        $importState = true;
        $reusltImport = array(); 
        array_push($reusltImport, array("importState" =>$importState,"importLog" =>$importLog));
        return $reusltImport;
    }
/*
    public function test(){
        $import = '[{"sheetInfo":[{"feuille":"manyPcodes"},{"importInfo":[{"headerInfo":[{"headerName":"Admin1"},{"headerIndex":"1"},{"headerType":"location"},{"HeaderIndicator":"labelAdmin1"},{"disaggregationTarget":null}]},{"headerInfo":[{"headerName":"PIN"},{"headerIndex":"2"},{"headerType":"keyFigure"},{"HeaderIndicator":"9"},{"disaggregationTarget":null}]},{"headerInfo":[{"headerName":"Homme"},{"headerIndex":"4"},{"headerType":"disaggregation"},{"HeaderIndicator":"20181213160602939i"},{"disaggregationTarget":"PIN"}]},{"headerInfo":[{"headerName":"Femme"},{"headerIndex":"5"},{"headerType":"disaggregation"},{"HeaderIndicator":"201812131606023072i"},{"disaggregationTarget":"PIN"}]}]},{"sheetData":[["Country","Admin1","PIN","PT","Homme","Femme","Year"],["Burkina Faso","Centre","939148","939148","350000","589148","2015"],["Camerou","Burkina Faso","2070000","1600000","1530000","540000","2015"],["Mauritania","kyar","3000000","2500000","1524000","1476000","2015"]]}]}]';
        $jsonnn = json_decode($import);
        ////print_r( $jsonnn);
        
        //CREATE THE FILE
        $IdFile=date('YmdHis').rand (0, 9999)."i";
        $file = new file;
        $file->file_id = $IdFile;
        $file->file_name = "Test";
        $file->file_type = "Excel";
        $file->save();


        foreach ($jsonnn as $sheetInfo){
            //LECTURE DES DONNEES
            $sheetName = $sheetInfo->sheetInfo[0]->feuille;
            $importInfo = $sheetInfo->sheetInfo[1]->importInfo;
            $sheetData = $sheetInfo->sheetInfo[2]->sheetData;

            //TRAITEMENT DES DONNEES
            $line = 0;
            $LastCodeNiveauAdmin = 0;
            $idSelectedLocation = "";
            $date = "";

            foreach ($sheetData as $data){
                if($line!=0){
                    $importLog .="</br></br></br><h3>TRAITEMENT LIGNE  ".$line."</h3></br>");
                    
                    $numColone = 0;
                    $locations = array();
                    $keyfigures = array();
                    $disaggregations = array();
                    $date = null;
                    $Admin0 = null;
                    $Admin1 = null;
                    $Admin2 = null;
                    $Admin3 = null;
                    $Admin4 = null;
                    $Admin5 = null;

                    $Admin0new = false;
                    $Admin1new = false;
                    $Admin2new = false;
                    $Admin3new = false;
                    $Admin4new = false;
                    $Admin5new = false;

                    $parentAdmin0Id = "";
                    $parentAdmin1Id = "";
                    $parentAdmin2Id = "";
                    $parentAdmin3Id = "";
                    $parentAdmin4Id = "";

                    $nbLocation = 0;
                    $adminLevel = 0;
                    

                    foreach ($data as $colonne){
                       
                        $locationFound = false;
                        

                        foreach ($importInfo as $header){
                            if($numColone == $header->headerInfo[1]->headerIndex){



                                //RECHECHE DE LA LOCALISATION
                                if($header->headerInfo[2]->headerType == "location"){
                                    
                                    $pcodeIso3 = $colonne;
                                    $niveauAdmin = substr($header->headerInfo[3]->HeaderIndicator,5,6);
                                    $typeAdminSearch = substr($header->headerInfo[3]->HeaderIndicator,0,5);
                                    $locationToUpdate = false;
                                    $locationUpdateComment = "";

                                    if($typeAdminSearch=="pcode"){
                                        //RECHERCHE PAR PCODE
                                        $object = location::where('location_pcode_iso3', $colonne)->first();
                                        
                                    }else{
                                        //RECHERCHE PAR LABEL
                                        //select * from locations where upper(location_caption_en) = upper('centre')
                                        $pcodeIso3 = "";
                                        $locationToUpdate = true;
                                        $locationUpdateComment = "Without pcode";

                                        switch($niveauAdmin){
                                            case "Admin0":
                                                $adminLevel = 0;
                                            break;
                                            case "Admin1":
                                                $adminLevel = 1;
                                            break;
                                            case "Admin2":
                                                $adminLevel = 2;
                                            break;
                                            case "Admin3":
                                                $adminLevel = 3;
                                            break;
                                            case "Admin4":
                                                $adminLevel = 4;
                                            break;
                                            case "Admin5":
                                                $adminLevel = 5;
                                            break;

                                        }
                                        $valeur = strtoupper($colonne);
                                        $object = DB::table("locations")->whereRaw('upper(location_caption_en) = ?', $valeur)->where('location_admin_level', $adminLevel)->first();
                                    }

                                   
                                    $importLog .="Localisation ".$typeAdminSearch." trouvé : colonne ".$numColone." - ".$colonne." - ".$niveauAdmin."</br>");
                                    
                                    if(!$object){
                                        $importLog .="DB : Localisation non trouvé ".$colonne." : ".$niveauAdmin."</br>");
                                        $IdLocation=date('YmdHis').rand (0, 9999)."i";
                                       
                                        switch($niveauAdmin){
                                            case "Admin0":
                                                $codeNiveauAdmin = 0;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 2;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin0 = $location;
                                                $parentAdmin0Id = $IdLocation;
                                                $Admin0new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin0Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin1":
                                                $codeNiveauAdmin = 1;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 3;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin0Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin1 = $location;
                                                $parentAdmin1Id = $IdLocation;
                                                $Admin1new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin1Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin2":
                                                $codeNiveauAdmin = 2;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 4;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin1Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin2 = $location;
                                                $parentAdmin2Id = $IdLocation;
                                                $Admin2new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin2Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin3":
                                                $codeNiveauAdmin = 3;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 5;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin2Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin3 = $location;
                                                $parentAdmin3Id = $IdLocation;
                                                $Admin3new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin3Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin4":
                                                $codeNiveauAdmin = 4;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 6;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin3Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin4 = $location;
                                                $parentAdmin4Id = $IdLocation;
                                                $Admin4new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin4Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin5":
                                                $codeNiveauAdmin = 5;
                                                $location = new location;
                                                $location->location_id = $IdLocation;
                                                $location->locationtype_id = 7;
                                                $location->location_caption_fr = $colonne;
                                                $location->location_caption_en = $colonne;
                                                $location->location_pcode_iso2 = $pcodeIso3;
                                                $location->location_pcode_iso3 = $pcodeIso3;
                                                $location->location_admin_level = $codeNiveauAdmin;
                                                $location->location_parent_id = $parentAdmin4Id;
                                                $location->location_to_update = $locationToUpdate;
                                                $location->location_upd_comment = $locationUpdateComment;
                                                $Admin5 = $location;
                                                $parentAdmin5Id = $IdLocation;
                                                $Admin5new = true;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                }
                                            break;
                                        }
                                    }else{

                                        $importLog .="DB : Localisation trouvé ". $colonne." - ".$object->location_caption_en." - ".$niveauAdmin."</br>");
                                        array_push($locations, array("location_id" =>$object->location_id,"location_value" =>$colonne,"niveauAdmin" =>  $niveauAdmin));
                                        $IdLocation = $object->location_id;
                                        switch($niveauAdmin){
                                            case "Admin0":
                                                $codeNiveauAdmin = 0;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin0Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin1":
                                                $codeNiveauAdmin = 1;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin1Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin2":
                                                $codeNiveauAdmin = 2;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin2Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin3":
                                                $codeNiveauAdmin = 3;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin3Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin4":
                                                $codeNiveauAdmin = 4;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                    $parentAdmin4Id = $IdLocation;
                                                }
                                            break;
                                            case "Admin5":
                                                $codeNiveauAdmin = 5;

                                                if($codeNiveauAdmin>=$LastCodeNiveauAdmin){
                                                    $idSelectedLocation = $IdLocation;
                                                }
                                            break;
                                        }

                                    }

                                    $nbLocation++;
                                }



                                //RECHERCHE DE L'INDICATEUR
                                if($header->headerInfo[2]->headerType == "keyFigure"){
                                    $header_name = $header->headerInfo[0]->headerName;
                                    $importLog .="</br></br>keyFigure trouvé : colonne ".$numColone." - ".$header_name."</br>");
                                   
                                    $object = header::where('header_name', $header_name)->first();

                                    if(!$object){
                                        $importLog .="DB : Header non trouvé ".$header_name."</br>");

                                        
                                        $IdHeader=date('YmdHis').rand (0, 9999)."i";
                                        $Header = new Header;
                                        $Header->header_id = $IdHeader;
                                        $Header->kfind_id = $header->headerInfo[3]->HeaderIndicator;
                                        $Header->type_header_id = 0;
                                        $Header->header_name = $header_name;
                                        $Header->save();
                                        $importLog .="DB : Header créé ".$header_name."</br>");

                                        array_push($keyfigures, array("kfind_id" =>$header->headerInfo[3]->HeaderIndicator,"header_name" =>$header_name,"valeur" =>$colonne));
                                        
                                    }else{
                                        $importLog .="DB : Header trouvé ". $header_name." - ".$object->header_name."</br>");
                                        array_push($keyfigures, array("kfind_id" =>$object->kfind_id,"header_name" =>$header_name,"valeur" =>$colonne));
                                    }
                                }
                                
                                

                                //RECHERCHE DE LA DATE
                                if($header->headerInfo[2]->headerType == "date"){
                                    
                                    $dateFormat = $header->headerInfo[3]->HeaderIndicator;
                                    switch($dateFormat){
                                        case "AAAA":
                                            $date = date_format(date_create($colonne."-12-31"),"Y/m/d");
                                        break;
                                        case "AAAA-MM-DD":

                                        break;
                                        case "DD-MM-AAAA":

                                        break;
                                    }
                                    $importLog .="</br></br>Date trouvé : colonne ".$numColone." - ".$header_name." - format: ".$dateFormat."</br>");
                                    
                                }



                                //RECHERCHE DE LA DISAGGREGATION
                                if($header->headerInfo[2]->headerType == "disaggregation"){
                                    $idDisaggregation = $header->headerInfo[3]->HeaderIndicator; 
                                    $disaggregationTargetIndicator = $header->headerInfo[4]->disaggregationTarget; 
                                    array_push($disaggregations, array("id_disaggregation" =>$idDisaggregation,"disaggregationTargetIndicator" =>$disaggregationTargetIndicator,"valeur" =>$colonne));
                                    $importLog .="DISAGGREGATION trouvé ". $colonne." </br>");
                                }
                            }



                           
                           // //print_r($header->headerInfo[2]->headerType);
                            ////print_r("</br>");
                        }

                        

                        
                        $idIndicateur = 0000;


                        

                        $numColone++;
                    }

                    $importLog .="</br></br>GESTION DES PARENT FILS");
                    if($nbLocation>1){
                        if($Admin0!=null){
                            $Admin0->save();
                            $importLog .="</br>Admin0 : ".$Admin0->location_pcode_iso2." créé sans fils");
                        }
                        
                        if($Admin1!=null){
                            $Admin1->location_parent_id=$parentAdmin0Id;
                            $Admin1->save();
                            $importLog .="</br>Admin1 : ".$Admin1->location_pcode_iso2." avec parent : ".$parentAdmin0Id);
                        }
                        
                        if($Admin2!=null){
                            $Admin2->location_parent_id=$parentAdmin1Id;
                            $Admin2->save();
                            $importLog .="</br>Admin2 : ".$Admin2->location_pcode_iso2." avec parent : ".$parentAdmin1Id);
                        }
                        
                        if($Admin3!=null){
                            $Admin3->location_parent_id=$parentAdmin2Id;
                            $Admin3->save();
                            $importLog .="</br>Admin3 : ".$Admin3->location_pcode_iso2." avec parent : ".$parentAdmin2Id);
                        }
                        
                        if($Admin4!=null){
                            $Admin4->location_parent_id=$parentAdmin3Id;
                            $Admin4->save();
                            $importLog .="</br>Admin4 : ".$Admin4->location_pcode_iso2." avec parent : ".$parentAdmin3Id);
                        }

                        if($Admin5!=null){
                            $Admin5->location_parent_id=$parentAdmin4Id;
                            $Admin5->save();
                            $importLog .="</br>Admin5 : ".$Admin5->location_pcode_iso2." avec parent : ".$parentAdmin4Id);
                        }
                    }else{
                        if($Admin0!=null){
                            $Admin0->save();
                            $importLog .="</br>Admin0 : ".$Admin0->location_pcode_iso2." sans parent");
                        }
                        
                        if($Admin1!=null){
                            $Admin1->save();
                            $importLog .="</br>Admin1 : ".$Admin1->location_pcode_iso2." sans parent");
                        }
                        
                        if($Admin2!=null){
                            $Admin2->save();
                            $importLog .="</br>Admin2 : ".$Admin2->location_pcode_iso2." sans parent");
                        }
                        
                        if($Admin3!=null){
                            $Admin3->save();
                            $importLog .="</br>Admin3 : ".$Admin3->location_pcode_iso2." sans parent");
                        }
                        
                        if($Admin4!=null){
                            $Admin4->save();
                            $importLog .="</br>Admin4 : ".$Admin4->location_pcode_iso2." sans parent");
                        }

                        if($Admin5!=null){
                            $Admin5->save();
                            $importLog .="</br>Admin5 : ".$Admin5->location_pcode_iso2." sans parent");
                        }
                    }

                    //VERIIFCATION DATE
                    if($date==""){
                        $date = date("Y/m/d");
                    }


                    //ENREGISTREMENT DU KF REPORT
                    $importLog .="</br></br>ENREGISTREMENT DU KF REPORT");
                    $importLog .="</br>array locations </br>");
                    $importLog .="</br>Location detaillé : ".$idSelectedLocation."</br>");
                    //print_r($locations);
                    $importLog .="</br>array Key figures </br>");
                    //print_r($keyfigures);
                    $importLog .="</br>disaggregations </br>");
                    //print_r($disaggregations);
                    $importLog .="</br>Report Date</br>");
                    //print_r($date);
                    
                    
                    foreach ($keyfigures as $keyfigure){
                        
                        //GESTION DES DERNIERES DONNEES
                        DB::table('kf_reports')
                        ->where('location_id', $idSelectedLocation)
                        ->where('kfind_id', $keyfigure['kfind_id'])
                        ->where('kfreport_latest', 'oui')
                        ->update(['kfreport_latest' => 'valid to '.date('Ymd')]);

                        //DB INSERTION DE REPORT
                        $IdReport=date('YmdHis').rand (0, 9999)."i";
                        $kf_report = new kf_report;
                        $kf_report->kfreport_id = $IdReport;
                        $kf_report->location_id = $idSelectedLocation;
                        $kf_report->kfind_id = $keyfigure['kfind_id'];
                        $kf_report->file_id = $IdFile;
                        $kf_report->kfreport_date = $date;
                        $kf_report->kfreport_value = $keyfigure['valeur'];
                        $kf_report->kfreport_source = "test";
                        $kf_report->kfreport_comment = "test";
                        $kf_report->kfreport_latest = "oui";
                        $kf_report->save();


                        //ENREGISTREMENT DE LA DISAGGREGATION
                        foreach ($disaggregations as $disag){
                            if($disag['disaggregationTargetIndicator'] == $keyfigure['header_name']){
                                
                                $kf_disag = new kf_disag;
                                $kf_disag->kfreport_id = $IdReport;
                                $kf_disag->id_disaggregation = $disag['id_disaggregation'];
                                $kf_disag->disaggregated_value_label = "";
                                $kf_disag->disaggregated_value = $disag['valeur'];
                                $kf_disag->disaggregated_value_comment = "";
                                $kf_disag->save();
                            }
                        }

                        $importLog .="</br>Report created id : ". $IdReport."</br>");
                    }
                    
                }
                $line++;
            }
        }

    }
    */
    public function showUploadFile(Request $request){
        /*
        $file = $request->file('image');
     
        //Display File Name
        echo 'File Name: '.$file->getClientOriginalName();
        echo '<br>';
     
        //Display File Extension
        echo 'File Extension: '.$file->getClientOriginalExtension();
        echo '<br>';
     
        //Display File Real Path
        echo 'File Real Path: '.$file->getRealPath();
        echo '<br>';
     
        //Display File Size
        echo 'File Size: '.$file->getSize();
        echo '<br>';
     
        //Display File Mime Type
        echo 'File v Mime Type: '.$file->getMimeType();
     
        //Move Uploaded File
        $destinationPath = 'uploads';
        //echo 'file xxx:'.$file->getClientOriginalName();
        $newFileName = date('YmdHis').".".$file->getClientOriginalExtension();
        $file->move($destinationPath,$newFileName);

        */
        $file = $request->file('image');
        if (file_exists(public_path() . '/uploads/20181128154924.xlsx')) {
            Excel::import(new Import, $file);
        } else {
            echo "no";
        }

       //echo asset('storage/20181128154924.xlsx');
     }


}
