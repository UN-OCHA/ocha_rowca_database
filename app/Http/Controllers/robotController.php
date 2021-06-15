<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\kf_indicator as kf_indicator;
use App\location as location;
use App\location_type as location_type;
use App\kf_report as kf_report;
use App\organisation_category as organisation_category;
use App\organization as organization;
use App\category_organization as category_organization;
use App\location_other as location_other;
use App\emergency as emergency;
use App\emergency_category as emergency_category;
use App\category_emergency as category_emergency;
use App\emergency_location as emergency_location;
use App\globalcluster as globalcluster;
use App\plan as plan;
use App\plan_emergency as plan_emergency;
use App\plan_usageyear as plan_usageyear;
use App\categories_plan as categories_plan;
use App\usageyear as usageyear;
use App\plan_location as plan_location;
use App\plan_category as plan_category;
use App\fts_flow as fts_flow;
use App\ftsflow_from_org as ftsflow_from_org;
use App\ftsflow_dest_plan as ftsflow_dest_plan;
use App\ftsflow_from_plan as ftsflow_from_plan;
use App\ftsflow_from_usageyear as ftsflow_from_usageyear;
use App\ftsflow_dest_org as ftsflow_dest_org;
use App\ftsflow_dest_cluster as ftsflow_dest_cluster;
use App\ftsflow_dest_location as ftsflow_dest_location;
use App\ftsflow_dest_projet as ftsflow_dest_projet;
use App\ftsflow_dest_usageyear as ftsflow_dest_usageyear;
use App\ftsflow_dest_globalcluster as ftsflow_dest_globalcluster;
use App\rpm_cluster as rpm_cluster;
use App\project as project;
use App\ftsflow_from_location as ftsflow_from_location;
use App\ftsflow_dest_emergency as ftsflow_dest_emergency;
use App\ftsflow_from_globalcluster as ftsflow_from_globalcluster;
use App\ftsflow_from_cluster as ftsflow_from_cluster;
use App\ftsflow_from_emergency as ftsflow_from_emergency;
use App\ftsflow_from_projet as ftsflow_from_projet;




class robotController extends Controller
{

    public function getAPIDatas()
    {
        //REFUGEES DATA
        self::getRefugeesByCountry();
        self::getRefugeesByCountrySubLocation();
        
        //FTS
        self::getFTSOrganisations();
        self::getFTSLocations();
        self::getFTSGlobalClusers();
        self::getRPMPlansBasic();
        self::getFTSFlows();

    }

    //https://data2.unhcr.org
    public function getRefugeesByCountry()
    {
        //Burkina Faso = 594
        //Angola = 578
        //Benin = 589
        //Cameroon = 349
        //Chad = 410
        //Cote d\u0027Ivoire = 509;
        //Gambia = 633
        //Ghana = 637
        //Guinea = 643
        //Guinea-Bissau = 639
        //Liberia = 535
        //Mali = 684
        //Mauritania = 677
        //Niger = 697
        //Nigeria = 699
        //Senegal = 723
        //Sierra Leone = 726
        //Togo = 745
        //Uganda = 220

        $countriesID = array();
        $countriesLabel = array();
        //AJOUT DES PAYS
        array_push($countriesID, "594");
        array_push($countriesLabel, "Burkina Faso");
        array_push($countriesID, "578");
        array_push($countriesLabel, "Angola");
        array_push($countriesID, "589");
        array_push($countriesLabel, "Benin");
        array_push($countriesID, "349");
        array_push($countriesLabel, "Cameroon");
        array_push($countriesID, "410");
        array_push($countriesLabel, "Chad");
        array_push($countriesID, "509");
        array_push($countriesLabel, "Cote d'Ivoire");
        array_push($countriesID, "633");
        array_push($countriesLabel, "Gambia");
        array_push($countriesID, "637");
        array_push($countriesLabel, "Ghana");
        array_push($countriesID, "639");
        array_push($countriesLabel, "Guinea-Bissau");
        array_push($countriesID, "535");
        array_push($countriesLabel, "Liberia");
        array_push($countriesID, "684");
        array_push($countriesLabel, "Mali");
        array_push($countriesID, "677");
        array_push($countriesLabel, "Mauritania");
        array_push($countriesID, "697");
        array_push($countriesLabel, "Niger");
        array_push($countriesID, "699");
        array_push($countriesLabel, "Nigeria");
        array_push($countriesID, "723");
        array_push($countriesLabel, "Senegal");
        array_push($countriesID, "726");
        array_push($countriesLabel, "Sierra Leone");
        array_push($countriesID, "745");
        array_push($countriesLabel, "Togo");
        array_push($countriesID, "220");
        array_push($countriesLabel, "Uganda");


        //RECHECHE ET CREATION DE L'INDICATEUR
        $indicator = DB::table("kf_indicators")->whereRaw('kfindic_caption_fr = ?', "Réfugiés")->first();
        if(!$indicator){
           echo "DB : indicator 'Réfugiés' non trouvé dans la base de données elle sera crée</br>";
            $kfind_id=date('YmdHis').rand (0, 9999)."i";

            $codeNiveauAdmin = 0;
            $indicator = new kf_indicator;
            $indicator->kfind_id = $kfind_id;
            $indicator->kfsubcategory_id = 9;
            $indicator->kfindic_caption_fr = "Réfugiés";
            $indicator->kfindic_caption_en = "Refugees";
            $indicator->save();
        }

        for ($x = 0; $x <count($countriesID); $x++) {
            $cSession = curl_init(); 
            //step2
            curl_setopt($cSession, CURLOPT_CAINFO,app_path()."/certificates/cacert-2017-09-20.pem");
            curl_setopt($cSession,CURLOPT_URL,"https://data2.unhcr.org/api/population/get?geo_id=".$countriesID[$x]."&population_collection=4");
            curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($cSession,CURLOPT_HEADER, false); 
            //step3
            $reponse = curl_exec($cSession);
            $results=json_decode($reponse);


            if($results === false)
            {
                echo 'Erreur Curl : ' . curl_error($cSession);
                $results = null;
            }
            else
            {
                //RECHECHE DE LA LOCATION
                $valeur = strtoupper($countriesLabel[$x]);
                $location = DB::table("locations")->whereRaw('upper(location_caption_en) = ?',$valeur )->where('location_admin_level', 0)->first();
                if(!$location){
                    echo "DB : Localisation '".$countriesLabel[$x]."' non trouvé dans la base de données elle sera crée</br>";
                    $IdLocation=date('YmdHis').rand (0, 9999)."i";

                    $codeNiveauAdmin = 0;
                    $location = new location;
                    $location->location_id = $IdLocation;
                    $location->locationtype_id = 2;
                    $location->location_caption_fr = $countriesLabel[$x];
                    $location->location_caption_en = $countriesLabel[$x];
                    $location->location_pcode_iso2 = "";
                    $location->location_pcode_iso3 = "";
                    $location->location_admin_level = 0;
                    $location->location_to_update = true;
                    $location->location_upd_comment = "Location witouht pcode";
                    $location->save();
                }

                foreach ($results->data as $result){
                    echo "geomaster_id : ".$result->geomaster_id."</br>";
                    echo "geomaster_name : ".$result->geomaster_name."</br>";
                    echo "admin_level : ".$result->admin_level."</br>";
                    echo "source : ".$result->source."</br>";
                    echo "date : ".$result->date."</br>";
                    echo "individuals : ".$result->individuals."</br>";
                    echo "households : ".$result->households."</br>";
                    echo "--------</br>";


                    //GESTION DES DERNIERES DONNEES
                    DB::table('kf_reports')
                    ->where('location_id', $location->location_id)
                    ->where('kfind_id', $indicator->kfind_id)
                    ->where('kfreport_latest', 'oui')
                    ->update(['kfreport_latest' => 'valid to '.date('Ymd H:i')]);


                    //DB INSERTION DE REPORT
                    $IdReport=date('YmdHis').rand (0, 9999)."i";
                    $kf_report = new kf_report;
                    $kf_report->kfreport_id = $IdReport;
                    $kf_report->location_id = $location->location_id;
                    $kf_report->kfind_id = $indicator->kfind_id;
                    $kf_report->kfreport_date = $result->date;
                    $kf_report->kfreport_value = $result->individuals;
                    $kf_report->kfreport_source = $result->source;
                    $kf_report->kfreport_comment = $result->population_groups_concat;
                    $kf_report->kfreport_latest = "oui";
                    $kf_report->save();
                }
            }
            curl_close($cSession);
        }
    }
    public function getRefugeesByCountrySubLocation()
    {
        //Burkina Faso = 594
        //Angola = 578
        //Benin = 589
        //Cameroon = 349
        //Chad = 410
        //Cote d\u0027Ivoire = 509;
        //Gambia = 633
        //Ghana = 637
        //Guinea = 643
        //Guinea-Bissau = 639
        //Liberia = 535
        //Mali = 684
        //Mauritania = 677
        //Niger = 697
        //Nigeria = 699
        //Senegal = 723
        //Sierra Leone = 726
        //Togo = 745
        //Uganda = 220

        $countriesID = array();
        $countriesLabel = array();
        //AJOUT DES PAYS
        array_push($countriesID, "594");
        array_push($countriesLabel, "Burkina Faso");
        array_push($countriesID, "578");
        array_push($countriesLabel, "Angola");
        array_push($countriesID, "589");
        array_push($countriesLabel, "Benin");
        array_push($countriesID, "349");
        array_push($countriesLabel, "Cameroon");
        array_push($countriesID, "410");
        array_push($countriesLabel, "Chad");
        array_push($countriesID, "509");
        array_push($countriesLabel, "Cote d'Ivoire");
        array_push($countriesID, "633");
        array_push($countriesLabel, "Gambia");
        array_push($countriesID, "637");
        array_push($countriesLabel, "Ghana");
        array_push($countriesID, "639");
        array_push($countriesLabel, "Guinea-Bissau");
        array_push($countriesID, "535");
        array_push($countriesLabel, "Liberia");
        array_push($countriesID, "684");
        array_push($countriesLabel, "Mali");
        array_push($countriesID, "677");
        array_push($countriesLabel, "Mauritania");
        array_push($countriesID, "697");
        array_push($countriesLabel, "Niger");
        array_push($countriesID, "699");
        array_push($countriesLabel, "Nigeria");
        array_push($countriesID, "723");
        array_push($countriesLabel, "Senegal");
        array_push($countriesID, "726");
        array_push($countriesLabel, "Sierra Leone");
        array_push($countriesID, "745");
        array_push($countriesLabel, "Togo");
        array_push($countriesID, "220");
        array_push($countriesLabel, "Uganda");


        //RECHECHE ET CREATION DE L'INDICATEUR
        $indicator = DB::table("kf_indicators")->whereRaw('kfindic_caption_fr = ?', "Réfugiés")->first();
        if(!$indicator){
           echo "DB : indicator 'Réfugiés' non trouvé dans la base de données elle sera crée</br>";
            $kfind_id=date('YmdHis').rand (0, 9999)."i";

            $codeNiveauAdmin = 0;
            $indicator = new kf_indicator;
            $indicator->kfind_id = $kfind_id;
            $indicator->kfsubcategory_id = 9;
            $indicator->kfindic_caption_fr = "Réfugiés";
            $indicator->kfindic_caption_en = "Refugees";
            $indicator->save();
        }

        for ($x = 0; $x <count($countriesID); $x++) {
            $cSession = curl_init(); 
            //step2
            curl_setopt($cSession, CURLOPT_CAINFO,app_path()."/certificates/cacert-2017-09-20.pem");
            curl_setopt($cSession,CURLOPT_URL,"https://data2.unhcr.org/api/population/get/sublocation?geo_id=".$countriesID[$x]."&population_collection=4&forcesublocation=true&fromDate=1900-01-01");
            curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($cSession,CURLOPT_HEADER, false); 
            //step3
            $reponse = curl_exec($cSession);
            $results=json_decode($reponse);


            if($results === false)
            {
                echo 'Erreur Curl : ' . curl_error($cSession);
                $results = null;
            }
            else
            {
                //RECHECHE DE LA LOCATION PARENT
                $valeur = strtoupper($countriesLabel[$x]);
                $Parentlocation = DB::table("locations")->whereRaw('upper(location_caption_en) = ?',$valeur )->where('location_admin_level', 0)->first();
                if(!$Parentlocation){
                    echo "DB : Localisation parent '".$countriesLabel[$x]."' non trouvé dans la base de données elle sera crée</br>";
                    $IdLocation=date('YmdHis').rand (0, 9999)."i";

                    $codeNiveauAdmin = 0;
                    $Parentlocation = new location;
                    $Parentlocation->location_id = $IdLocation;
                    $Parentlocation->locationtype_id = 2;
                    $Parentlocation->location_caption_fr = $countriesLabel[$x];
                    $Parentlocation->location_caption_en = $countriesLabel[$x];
                    $Parentlocation->location_pcode_iso2 = "";
                    $Parentlocation->location_pcode_iso3 = "";
                    $Parentlocation->location_admin_level = 0;
                    $Parentlocation->location_to_update = true;
                    $Parentlocation->location_upd_comment = "Location witouht pcode";
                    $Parentlocation->save();
                }

                foreach ($results->data as $result){
                    echo "geomaster_id : ".$result->geomaster_id."</br>";
                    echo "geomaster_name : ".$result->geomaster_name."</br>";
                    echo "admin_level : ".$result->admin_level."</br>";
                    echo "source : ".$result->source."</br>";
                    echo "date : ".$result->date."</br>";
                    echo "individuals : ".$result->individuals."</br>";
                    echo "households : ".$result->households."</br>";
                    echo "--------</br>";

                    $adminLevel = 1;
                    $locationType = 1;
                    $labelLocation = strtoupper($result->geomaster_name);
                    $locationComment = "Location witouht pcode";

                    switch($result->admin_level){
                        case "Province":
                            $adminLevel = 1;
                            $locationType = 3;
                        break;
                        case "Region":
                            $adminLevel = 1;
                            $locationType = 3;
                        break;
                        case "Settlement":
                            $adminLevel = 4;
                            $locationType = 6;
                        break;
                        default:
                            $locationComment.=": level = ".$result->admin_level;
                            $adminLevel = 1;
                            $locationType = 3;
                        break;
                    }

                    $Childlocation = DB::table("locations")->whereRaw('upper(location_caption_en) = ?',$labelLocation )->where('location_admin_level', $adminLevel)->first();
                    if(!$Childlocation){
                        echo "DB : Localisation enfant '".$labelLocation."' non trouvé dans la base de données elle sera crée</br>";
                        $IdLocation=date('YmdHis').rand (0, 9999)."i";

                        $Childlocation = new location;
                        $Childlocation->location_id = $IdLocation;
                        $Childlocation->locationtype_id = $locationType;
                        $Childlocation->location_caption_fr = $labelLocation;
                        $Childlocation->location_caption_en = $labelLocation;
                        $Childlocation->location_pcode_iso2 = "";
                        $Childlocation->location_pcode_iso3 = "";
                        $Childlocation->location_admin_level = $adminLevel;
                        $Childlocation->location_parent_id = $Parentlocation->location_id;
                        $Childlocation->location_to_update = true;
                        $Childlocation->location_upd_comment = $locationComment;
                        $Childlocation->save();
                    }


                    //GESTION DES DERNIERES DONNEES
                    DB::table('kf_reports')
                    ->where('location_id', $Childlocation->location_id)
                    ->where('kfind_id', $indicator->kfind_id)
                    ->where('kfreport_latest', 'oui')
                    ->update(['kfreport_latest' => 'valid to '.date('Ymd H:i')]);


                    //DB INSERTION DE REPORT
                    $IdReport=date('YmdHis').rand (0, 9999)."i";
                    $kf_report = new kf_report;
                    $kf_report->kfreport_id = $IdReport;
                    $kf_report->location_id = $Childlocation->location_id;
                    $kf_report->kfind_id = $indicator->kfind_id;
                    $kf_report->kfreport_date = $result->date;
                    $kf_report->kfreport_value = $result->individuals;
                    $kf_report->kfreport_source = $result->source;
                    $kf_report->kfreport_comment = $result->population_groups_concat;
                    $kf_report->kfreport_latest = "oui";
                    $kf_report->save();
                }
            }
            curl_close($cSession);
        }
    }


    //https://fts.unocha.org/
    public function getFTSdata($endpoint) 
    {
		// $fts_username = "ocha_dakar";
		// $fts_password = "thnm456Ujm";
		$fts_username = "ocha_viu";
		$fts_password = "nbgt876Sxc";
	
		// curl init	
		$ch = curl_init();
		$uri = "https://api.hpc.tools/v1/public/".$endpoint;
		//curl opts
		curl_setopt($ch, CURLOPT_CAINFO,app_path()."/certificates/cacert-2017-09-20.pem");
		curl_setopt($ch, CURLOPT_URL,$uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$fts_username:$fts_password");
		
		//process results
		$result = curl_exec($ch);

		if($result === false)
		{
			echo 'Erreur Curl : ' . curl_error($ch);
			$result = null;
		}
		else
		{
			$result;
		}
		curl_close($ch);

		return $result;
    }
    public function getflows()
    {
        $json = self::getFTSdata("organization");
        var_dump(json_decode($json));
    }

    public function getHPCCycleData()
    {
        //self::clearTables();
        
        
        //self::getRPMPlansEntities();
        //self::getFTSFlowsTest();
        //self::Synchronise();
    }

    public function getFTSOrganisations()
    {
        echo "<strong>Getting FTS ORGANISATIONS :</strong><br/>";
        $jsonData = self::getFTSdata("organization");
        $listData = json_decode($jsonData)->data;
        $numberNewRecords = 0;
        
        for($i = 0; $i < count($listData); ++$i) {

            //ORGANISATION
            $elements = DB::table('organizations')->where([
                ['organization_id', '=', $listData[$i]->id],
                ['organization_name', '=', $listData[$i]->name],
            ])->get();
            
            if($elements->isEmpty()){
                $organization = new organization;
                $organization->organization_id = $listData[$i]->id;
                $organization->organization_name = $listData[$i]->name;
                $organization->organization_abbreviation = $listData[$i]->abbreviation;
                $organization->save();
                $numberNewRecords++;
            }

            //category_organization
            $organisations_categories = $listData[$i]->categories;
            foreach ($organisations_categories as $organisations_category) {
                $elements = DB::table('category_organizations')->where([
                    ['organization_categories_id', '=', $organisations_category->id],
                    ['organization_categories_name', '=', $organisations_category->name],
                ])->get();
                
                if($elements->isEmpty()){
                    $category_organization = new category_organization;
                    $category_organization->organization_categories_id = $organisations_category->id;
                    $category_organization->organization_categories_name = $organisations_category->name;
                    $category_organization->organization_categories_group = $organisations_category->group;
                    $category_organization->save();
                }

                //organisation_category
                $elements = DB::table('organisation_categories')->where([
                    ['organization_id', '=',  $listData[$i]->id],
                    ['organization_categories_id', '=', $organisations_category->id],
                ])->get();
                
                if($elements->isEmpty()){
                    $organisation_category = new organisation_category;
                    $organisation_category->organization_id = $listData[$i]->id;
                    $organisation_category->organization_categories_id = $organisations_category->id;
                    $organisation_category->save();
                }
            }
        }
        echo "Finished : ".$numberNewRecords." organisations created<br/>";
    }

    public function getFTSLocations()
    {
        echo "<strong>Getting FTS LOCATIONS : </strong><br/>";
        $jsonData = self::getFTSdata("location");
        $listDataLoc = json_decode($jsonData)->data;
        $c = 0;
        $e = 0;

        for($i = 0; $i < count($listDataLoc); $i++) {
            $elements = DB::table('location_others')->where([
                ['location_other_id', '=', $listDataLoc[$i]->id],
            ])->get();
            
            
            if($elements->isEmpty()){
                $locationType = 2;
                switch($listDataLoc[$i]->adminLevel){
                    case 0:
                        $locationType = 2;
                    break;
                    case 1:
                        $locationType = 3;
                    break;
                    case 2:
                        $locationType = 4;
                    break;
                    case 3:
                        $locationType = 5;
                    break;
                    case 4:
                        $locationType = 6;
                    break;
                    break;
                    case 5:
                        $locationType = 7;
                    break;
                }

                $IdLocation=date('YmdHis').rand (0, 9999)."i";
                $location = new location;
                $location->location_id = $IdLocation;
                $location->locationtype_id = $locationType;
                $location->location_caption_fr =$listDataLoc[$i]->name;
                $location->location_caption_en = $listDataLoc[$i]->name;
                $location->location_pcode_iso2 = "";
                $location->location_pcode_iso3 = $listDataLoc[$i]->iso3;
                $location->location_admin_level = $listDataLoc[$i]->adminLevel;
                $location->location_to_update = true;
                $location->location_upd_comment = "FTS : Location without pcode";
                $location->location_source = "FTS";
                $location->save();

                $location_other = new location_other;
                $location_other->location_other_id = $listDataLoc[$i]->id;
                $location_other->location_id = $IdLocation;
                $location_other->location_other_source = "fts";
                $location_other->save();

                $c++;
            }

            //EMERGENCIES
            $jsonData2 = self::getFTSdata("emergency/country/".$listDataLoc[$i]->iso3);
            $request_data2 = json_decode($jsonData2);

            if(isset($request_data2->data)){
                $listDataEmer = $request_data2->data;
        
                for($j = 0; $j < count($listDataEmer); $j++) {
                    //EMERGENCY
                    $elements = DB::table('emergencies')->where([
                        ['emergency_id', '=', $listDataEmer[$j]->id],
                    ])->get();
                    
                    if($elements->isEmpty()){
                        $emergency = new emergency;
                        $emergency->emergency_id = $listDataEmer[$j]->id;
                        $emergency->emergency_name = $listDataEmer[$j]->name;
                        $emergency->emergency_glideid = $listDataEmer[$j]->glideId;
                        $emergency->emergency_date = self::convertDate($listDataEmer[$j]->date);
                        $emergency->save();
                        $e++;
                    }

                    //EMERGENCY CATEGORY
                    $emergency_categories = $listDataEmer[$j]->categories;
                    foreach ($emergency_categories as $emergency_category_) {
                        $elements = DB::table('category_emergencies')->where([
                            ['emerg_categ_id', '=', $emergency_category_->id],
                            ['emerg_categ_name', '=', $emergency_category_->name],
                        ])->get();
                        
                        if($elements->isEmpty()){
                            $category_emergency = new category_emergency;
                            $category_emergency->emerg_categ_id = $emergency_category_->id;
                            $category_emergency->emerg_categ_name = $emergency_category_->name;
                            $category_emergency->save();
                        }

                        //emergency_category
                        $elements = DB::table('emergency_categories')->where([
                            ['emergency_id', '=',  $listDataEmer[$j]->id],
                            ['emerg_categ_id', '=', $emergency_category_->id],
                        ])->get();
                        
                        if($elements->isEmpty()){
                            $emergency_category = new emergency_category;
                            $emergency_category->emergency_id = $listDataEmer[$j]->id;
                            $emergency_category->emerg_categ_id = $emergency_category_->id;
                            $emergency_category->save();
                        }
                    }

                    //EMERGENCY LOCATION
                    
                    $emergency_locations = $listDataEmer[$j]->locations;
                    foreach ($emergency_locations as $emergency_location_) {
                        $elements = DB::table('emergency_locations')->where([
                            ['emergency_id', '=',  $listDataEmer[$j]->id],
                            ['location_other_id', '=', $emergency_location_->id],
                        ])->get();
                        
                        if($elements->isEmpty()){
                            $emergency_location = new emergency_location;
                            $emergency_location->emergency_id = $listDataEmer[$j]->id;
                            $emergency_location->location_other_id = $emergency_location_->id;
                            $emergency_location->save();
                        }
                    }
                }
                
            }
            
        }
        echo "Finished : ".$e." emergencies created<br/>";
        echo "Finished : ".$c." locations created<br/>";
    }

    public function getFTSGlobalClusers()
    {
        $jsonData = self::getFTSdata("global-cluster");
        $listData = json_decode($jsonData)->data;
        $numberNewRecords = 0;
        
        for($i = 0; $i < count($listData); $i++) {
            $elements = DB::table('globalclusters')->where([
                ['globalclusters_id', '=', $listData[$i]->id],
                ['globalclusters_name', '=', $listData[$i]->name],
            ])->get();
            
            if($elements->isEmpty()){
                $globalcluster = new globalcluster;
                $globalcluster->globalclusters_id = $listData[$i]->id;
                $globalcluster->globalclusters_name = $listData[$i]->name;
                $globalcluster->globalclusters_code = $listData[$i]->code;
                $globalcluster->globalclusters_type = $listData[$i]->type;
                $globalcluster->globalclusters_parentid = $listData[$i]->parentId;
                $globalcluster->save();
                $numberNewRecords++;
            }
            
        }
        echo "Finished : ".$numberNewRecords." global clusters created<br/>";
    }

    public function getRPMPlansBasic()
    {
        //RPM BASIC PLANS
        echo "<strong>Getting RPM BASIC PLANS : </strong><br/>";

        $years = array(2018,2017,2016,2015,2015,2014,2013,2012,2011,2010,2009,2008,2007,2006,2005,2004,2003,2002,2001);
        //$years = array(2018);
        $c= 0;

        foreach ($years as $year){
            
            $jsonData = self::getFTSdata("rpm/plan/year/".$year."?content=basic");
            $request_data = json_decode($jsonData);
            
            if(isset($request_data->data)){
                $listData = $request_data->data;

                for($i = 0; $i < count($listData); $i++) {
                    //PLAN INFORMATION
                    $planId = $listData[$i]->id;
                    $origRequirements = 0;
                    $revisedRequirements = 0;
                    
                    if(isset($listData[$i]->origRequirements))
                    $origRequirements = $listData[$i]->origRequirements;

                    if(isset($listData[$i]->revisedRequirements))
                    $revisedRequirements = $listData[$i]->revisedRequirements;

                    $plans = DB::table('plans')->where([
                        ['plan_id', '=', $listData[$i]->id]
                    ])->get();

                    if($plans->isEmpty()){
                        $plan = new plan;
                        $plan->plan_id = $planId;
                        $plan->plan_name = $listData[$i]->name;
                        $plan->plan_code = $listData[$i]->code;
                        $plan->plan_startdate = self::convertDate($listData[$i]->startDate);
                        $plan->plan_enddate = self::convertDate($listData[$i]->endDate);
                        $plan->plan_origrequirements = $origRequirements;
                        $plan->plan_origrequirements = $revisedRequirements;
                        $plan->save();
                        $c++;
                    }

                    //PLAN EMERGENCY
                    $plan_emergencies = $listData[$i]->emergencies;
                    foreach ($plan_emergencies as $plan_emergency_) {
                        $elements = DB::table('plan_emergencies')->where([
                            ['plan_id', '=', $planId],
                            ['emergency_id', '=', $plan_emergency_->id],
                        ])->get();
                        
                        if($elements->isEmpty()){
                            //CREATE EMERGENCY
                            $objectLink = DB::table('emergencies')->where([
                                ['emergency_id', '=', $plan_emergency_->id],
                            ])->get();
                            if($objectLink->isEmpty()){
                                $emergency = new emergency;
                                $emergency->emergency_id = $plan_emergency_->id;
                                $emergency->emergency_name = $plan_emergency_->name;
                                $emergency->save();
                            }

                            //CREATE PLAN EMERGENCY
                            $plan_emergency = new plan_emergency;
                            $plan_emergency->plan_id = $planId;
                            $plan_emergency->emergency_id = $plan_emergency_->id;
                            $plan_emergency->save();
                        }
                    }

                    //PLAN YEAR
                    $plan_years = $listData[$i]->years;
                    foreach ($plan_years as $plan_year) {
                        $elements = DB::table('plan_usageyears')->where([
                            ['plan_id', '=', $planId],
                            ['usageyear_id', '=', $plan_year->id],
                        ])->get();
                        
                       
                        if($elements->isEmpty()){
                            //CREATE USAGE YEAR
                            $objectLink = DB::table('usageyears')->where([
                                ['usageyear_id', '=', $plan_year->id],
                            ])->get();
                            if($objectLink->isEmpty()){
                                $usageyear = new usageyear;
                                $usageyear->usageyear_id = $plan_year->id;
                                $usageyear->usageyear_name = $plan_year->year;
                                $usageyear->save();
                            }

                            //CREATE PLAN USAGE YEAR
                            $plan_usageyear = new plan_usageyear;
                            $plan_usageyear->plan_id = $planId;
                            $plan_usageyear->usageyear_id = $plan_year->id;
                            $plan_usageyear->save();
                        }
                    }
                    
                    //PLAN LOCATIONS
                    $plan_locations = $listData[$i]->locations;
                    foreach ($plan_locations as $plan_location_) {
                        $elements = DB::table('plan_locations')->where([
                            ['plan_id', '=', $planId],
                            ['location_other_id', '=', $plan_location_->id],
                        ])->get();
                        
                        if($elements->isEmpty()){
                            //CREATE LOCATION
                            $objectLink = DB::table('location_others')->where([
                                ['location_other_id', '=', $plan_location_->id],
                            ])->get();
                            if($objectLink->isEmpty()){
                                $locationType = 2;
                                switch($plan_location_->adminLevel){
                                    case 0:
                                        $locationType = 2;
                                    break;
                                    case 1:
                                        $locationType = 3;
                                    break;
                                    case 2:
                                        $locationType = 4;
                                    break;
                                    case 3:
                                        $locationType = 5;
                                    break;
                                    case 4:
                                        $locationType = 6;
                                    break;
                                    break;
                                    case 5:
                                        $locationType = 7;
                                    break;
                                }
                
                                $IdLocation=date('YmdHis').rand (0, 9999)."i";
                                $location = new location;
                                $location->location_id = $IdLocation;
                                $location->locationtype_id = $locationType;
                                $location->location_caption_fr = $plan_location_->name;
                                $location->location_caption_en = $plan_location_->name;
                                $location->location_pcode_iso2 = "";
                                $location->location_pcode_iso3 = $plan_location_->iso3;
                                $location->location_admin_level = $plan_location_->adminLevel;
                                $location->location_to_update = true;
                                $location->location_upd_comment = "FTS : Location without pcode";
                                $location->location_source = "FTS";
                                $location->save();
                
                                //CREATE LOCATION OTHER
                                $location_other = new location_other;
                                $location_other->location_other_id = $plan_location_->id;
                                $location_other->location_id = $IdLocation;
                                $location_other->location_other_source = "fts";
                                $location_other->save();
                            }

                            //CREATE PLAN LOCATION
                            $plan_location = new plan_location;
                            $plan_location->plan_id = $planId;
                            $plan_location->location_other_id = $plan_location_->id;
                            $plan_location->save();
                        }
                        // var_dump($plan_location);
                    }

                    //PLAN CATEGORY
                    $plan_categories = $listData[$i]->categories;
                    foreach ($plan_categories as $plan_category_) {
                        $elements = DB::table('plan_categories')->where([
                            ['plan_id', '=', $planId],
                            ['plan_categorie_id', '=', $plan_category_->id],
                        ])->get();
                        
                        if($elements->isEmpty()){
                            //CREATE CATEGORY PLAN
                            $objectLink = DB::table('categories_plans')->where([
                                ['plan_categorie_id', '=', $plan_category_->id],
                            ])->get();
                            if($objectLink->isEmpty()){
                                $categories_plan = new categories_plan;
                                $categories_plan->plan_categorie_id = $plan_category_->id;
                                $categories_plan->plan_categorie_name = $plan_category_->name;
                                $categories_plan->save();
                            }

                            //CREATE PLAN CATEGORY
                            $plan_category = new plan_category;
                            $plan_category->plan_id = $planId;
                            $plan_category->plan_categorie_id = $plan_category_->id;
                            $plan_category->save();
                        }
                    }
                }
            }
        }
        echo "Finished : ".$c." new plans created<br/>";
    }

    public function getRPMPlansEntities()
    {
        //RPM ENTITIES PLANS
        echo "<strong>Getting RPM ENTITIES PLANS : </strong><br/>";

        //$years = array(2018,2017,2016,2015,2015,2014,2013,2012,2011,2010,2009,2008,2007,2006,2005,2004,2003,2002,2001);
        $years = array(2018);
        $c= 0;
        $planRowId= "";

        foreach ($years as $year){
            $jsonData = self::getFTSdata("rpm/plan/year/".$year."?content=entities");
            $request_data = json_decode($jsonData);
            
            if(isset($request_data->data)){
                $listData = $request_data->data;

                for($i = 0; $i < count($listData); $i++) {
                    $planId = $listData[$i]->id;
                    $plan_planEntities = $listData[$i]->planEntities;
                    $plan_governingEntities = $listData[$i]->governingEntities;
                    
                    $plans = DB::table('plans')->where([
                        ['plan_id', '=', $listData[$i]->id],
                    ])->get();

                    if($plans->isEmpty()){
                        //CREAT NEW PLAN
                        echo "new plan<br/>";
                    }else{
                        $planRowId= $plans[0]->plan_rowid;
                    }

                    //PLAN ENTITIES
                    foreach ($plan_planEntities as $plan_planEntity) {
                        $planEntityType = $plan_planEntity->value->type->en->singular;
                        $entityId = $plan_planEntity->id;

                        switch ($planEntityType) {
                            case "Cluster Activity":
                                //R_API_PLAN_AVOIR_CLUSTER_ACTIVITIES
                                $search = r_api_plan_avoir_cluster_activity::where('plan_rowid', $planRowId)->where('CLUSTERACTIVITY_ID', $entityId)->get();
                                if($search->isEmpty()){
                                    $r_api_plan_avoir_cluster_activity = new r_api_plan_avoir_cluster_activity;
                                    $r_api_plan_avoir_cluster_activity->plan_rowid = $planRowId;
                                    $r_api_plan_avoir_cluster_activity->CLUSTERACTIVITY_ID = $entityId;
                                    $r_api_plan_avoir_cluster_activity->save();
                                }
                            
                                //API_RPM_CLUSTER_ACTIVITIES
                                $search = api_rpm_cluster_activity::where('CLUSTERACTIVITY_ID', $entityId)->get();
                                if($search->isEmpty()){
                                    $clusterActivityCustomReference = $plan_planEntity->customReference;
                                    $clusterActivityDescription = $plan_planEntity->value->description;
                                    $api_rpm_cluster_activity = new api_rpm_cluster_activity;
                                    $api_rpm_cluster_activity->CLUSTERACTIVITY_ID = $entityId;
                                    $api_rpm_cluster_activity->CLUSTERACTIVITY_CUSTOMREFERENCE = $clusterActivityCustomReference;
                                    $api_rpm_cluster_activity->CLUSTERACTIVITY_DESCRIPTION = $clusterActivityDescription;
                                    $api_rpm_cluster_activity->save();
                                }
                                break;
                            case "Strategic Objective":
                                
                                //R_API_PLAN_AVOIR_STRATEGIC_OBJECTIVES
                                $search = r_api_plan_avoir_cluster_strategic_objective::where('plan_rowid', $planRowId)->where('STRATEGIC_OBJECTIVE_ID', $entityId)->get();
                                if($search->isEmpty()){
                                    $r_api_plan_avoir_cluster_strategic_objective = new r_api_plan_avoir_cluster_strategic_objective;
                                    $r_api_plan_avoir_cluster_strategic_objective->plan_rowid = $planRowId;
                                    $r_api_plan_avoir_cluster_strategic_objective->STRATEGIC_OBJECTIVE_ID = $entityId;
                                    $r_api_plan_avoir_cluster_strategic_objective->save();
                                }

                                //API_RPM_STRATEGIC_OBJECTIVES
                                $search = api_rpm_strategic_objective::where('STRATEGIC_OBJECTIVE_ID', $entityId)->get();
                                if($search->isEmpty()){
                                    $CustomReference = $plan_planEntity->customReference;
                                    $description = $plan_planEntity->value->description;
                                    $api_rpm_strategic_objective = new api_rpm_strategic_objective;
                                    $api_rpm_strategic_objective->STRATEGIC_OBJECTIVE_ID = $entityId;
                                    $api_rpm_strategic_objective->STRATEGIC_OBJECTIVE_CUSTOMREFERENCE = $CustomReference;
                                    $api_rpm_strategic_objective->STRATEGIC_OBJECTIVE_DESCRIPTION = $description;
                                    $api_rpm_strategic_objective->save();
                                }


                                break;
                            case "Cluster Objective":
                                //R_API_PLAN_AVOIR_CLUSTER_OBJECTIVES
                                $search = r_api_plan_avoir_cluster_objective::where('plan_rowid', $planRowId)->where('CLUSTER_OBJECTIVE_ID', $entityId)->get();
                                if($search->isEmpty()){
                                    $r_api_plan_avoir_cluster_objective = new r_api_plan_avoir_cluster_objective;
                                    $r_api_plan_avoir_cluster_objective->plan_rowid = $planRowId;
                                    $r_api_plan_avoir_cluster_objective->CLUSTER_OBJECTIVE_ID = $entityId;
                                    $r_api_plan_avoir_cluster_objective->save();
                                }

                                //API_RPM_CLUSTER_OBJECTIVES
                                $search = api_rpm_cluster_objective::where('CLUSTER_OBJECTIVE_ID', $entityId)->get();
                                if($search->isEmpty()){
                                    $CustomReference = $plan_planEntity->customReference;
                                    $description = $plan_planEntity->value->description;
                                    $api_rpm_cluster_objective = new api_rpm_cluster_objective;
                                    $api_rpm_cluster_objective->CLUSTER_OBJECTIVE_ID = $entityId;
                                    $api_rpm_cluster_objective->CLUSTER_OBJECTIVE_CUSTOMREFERENCE = $CustomReference;
                                    $api_rpm_cluster_objective->CLUSTER_OBJECTIVE_DESCRIPTION = $description;
                                    $api_rpm_cluster_objective->save();
                                }

                                break;
                            case "Sub Strategic Objective":
                                    //R_API_PLAN_AVOIR_SUB_STRATEGIC_OBJECTIVES
                                    $search = r_api_plan_avoir_sub_strategic_objective::where('plan_rowid', $planRowId)->where('SUB_STRATEGIC_OBJECTIVE_ID', $entityId)->get();
                                    if($search->isEmpty()){
                                        $r_api_plan_avoir_sub_strategic_objective = new r_api_plan_avoir_sub_strategic_objective;
                                        $r_api_plan_avoir_sub_strategic_objective->plan_rowid = $planRowId;
                                        $r_api_plan_avoir_sub_strategic_objective->SUB_STRATEGIC_OBJECTIVE_ID = $entityId;
                                        $r_api_plan_avoir_sub_strategic_objective->save();
                                    }

                                    //API_RPM_STRATEGIC_OBJECTIVES
                                    $search = api_rpm_sub_strategic_objective::where('SUB_STRATEGIC_OBJECTIVE_ID', $entityId)->get();
                                    if($search->isEmpty()){
                                        $CustomReference = $plan_planEntity->customReference;
                                        $description = $plan_planEntity->value->description;
                                        $api_rpm_sub_strategic_objective = new api_rpm_sub_strategic_objective;
                                        $api_rpm_sub_strategic_objective->SUB_STRATEGIC_OBJECTIVE_ID = $entityId;
                                        $api_rpm_sub_strategic_objective->SUB_STRATEGIC_OBJECTIVE_CUSTOMREFERENCE = $CustomReference;
                                        $api_rpm_sub_strategic_objective->SUB_STRATEGIC_OBJECTIVE_DESCRIPTION = $description;
                                        $api_rpm_sub_strategic_objective->save();
                                    }
                                break;
                            default:
                                echo $planEntityType;
                                break;
                        }
                    }

                    //GOVERNING ENTITIES
                    print_r($plan_governingEntities);
                    foreach ($plan_governingEntities as $plan_governingEntitiy) {
                        $governingEntityType = $plan_governingEntitiy->entityPrototype->value->name->en->singular;
                        $entityId = $plan_governingEntitiy->id;
                        
                        if(isset($plan_governingEntitiy->value->icon)){
                            
                            //R_API_PLAN_AVOIR_CLUSTERS
                            $search = r_api_plan_avoir_cluster::where('plan_rowid', $planRowId)->where('CLUSTER_ID', $entityId)->get();
                            if($search->isEmpty()){
                                $r_api_plan_avoir_cluster = new r_api_plan_avoir_cluster;
                                $r_api_plan_avoir_cluster->plan_rowid = $planRowId;
                                $r_api_plan_avoir_cluster->CLUSTER_ID = $entityId;
                                $r_api_plan_avoir_cluster->save();
                            }
                        
                            //API_RPM_CLUSTERS
                            $search = api_rpm_cluster::where('CLUSTER_ID', $entityId)->get();
                            if($search->isEmpty()){
                                $api_rpm_cluster = new api_rpm_cluster;
                                $api_rpm_cluster->CLUSTER_ID = $entityId;
                                $api_rpm_cluster->CLUSTER_CUSTOMREFERENCE = $plan_governingEntitiy->customReference;
                                $api_rpm_cluster->CLUSTER_DESCRIPTION = $plan_governingEntitiy->value->description;
                                $api_rpm_cluster->CLUSTER_ICON = $plan_governingEntitiy->value->icon;
                                $api_rpm_cluster->save();
                            }

                            //ATTACHEMENTS
                            $attachements = $plan_governingEntitiy->attachments;
                            foreach ($attachements as $attachement) {
                                $attachementType = $attachement->type;
                                switch ($attachementType) {
                                    case "cost":
                                        //API_RPM_COSTS
                                        if(isset($attachement->value->cost)){
                                            $search = api_rpm_cost::where('CLUSTER_ID', $entityId)->where('COST_ID', $attachement->id)->get();
                                            if($search->isEmpty()){
                                                $api_rpm_cost = new api_rpm_cost;
                                                $api_rpm_cost->COST_ID = $attachement->id;
                                                $api_rpm_cost->CLUSTER_ID = $entityId;
                                                $api_rpm_cost->COST_CUSTOMREFERENCE = $attachement->customReference;
                                                $api_rpm_cost->COST_COST = $attachement->value->cost;
                                                $api_rpm_cost->save();
                                            }
                                        }
                                        
                                    break;
                                    case "indicator":
                                        //API_RPM_INDICATOR
                                        //print_r("INDCATOR");
                                        //print_r($attachement);
                                        //echo('<br/>');
                                    break;
                                    case "caseLoad":
                                        //API_RPM_COSTS
                                        //print_r("CASELOAD");
                                        //print_r($attachement);
                                        //echo('<br/>');
                                    break;
                                    case "contact":
                                        //API_RPM_CONTACTS
                                        $leadAgency = null;
                                        $contactName = null;
                                        $contactEmail = null;
                                        if(isset($attachement->value->leadAgency))
                                        $leadAgency = $attachement->value->leadAgency;

                                        if(isset($attachement->value->contactEmail))
                                        $contactEmail = $attachement->value->contactEmail;

                                        if(isset($attachement->value->contactName)){
                                            $search = api_rpm_contact::where('CLUSTER_ID', $entityId)->where('CONTACT_ID', $attachement->id)->get();
                                            if($search->isEmpty()){
                                                $api_rpm_contact = new api_rpm_contact;
                                                $api_rpm_contact->CONTACT_ID = $attachement->id;
                                                $api_rpm_contact->CLUSTER_ID = $entityId;
                                                $api_rpm_contact->CONTACT_CUSTOMREFERENCE = $attachement->customReference;
                                                $api_rpm_contact->CONTACT_LEADAGENCY = $leadAgency;
                                                $api_rpm_contact->CONTACT_NAME = $attachement->value->contactName;
                                                $api_rpm_contact->CONTACT_EMAIL = $contactEmail;
                                                $api_rpm_contact->save();
                                            }
                                        }
                                        
                                    break;
                                    default:
                                        var_dump($attachementType);
                                    break;
                                }
                            }
                        }

                        
                    }
                    $c++;
                }
                
                echo "Finished : ".$c." new plans created<br/>";
            }
        }

        
        
        
        echo "Finished : ".$numberNewRecords." new global clusters created on ".count($listData);
    }

    public function getFTSFlows()
    {
        $plans = DB::table('plans')->get();
        $r = 0;
        $pageEnd = 30;
        
        
        foreach ($plans as $plan_) {
            $HasNextPage = true;
            echo "plan  : ".$plan_->plan_id."<br/>";
            
            for($p = 1; $p <= $pageEnd; $p++){
                //FLOWS
                $c=0;
                $jsonData = self::getFTSdata("fts/flow?planid=".$plan_->plan_id."&limit=150&page=".$p);
                $request_data = json_decode($jsonData);
                if(isset($request_data->data)){
                    if(isset($request_data->data->flows)){
                        $listData = $request_data->data->flows;
                        $numberNewRecords = 0;
                        if(count($listData)>0){
                            for($i = 0; $i < count($listData); $i++) {
                            
                                $element = DB::table('fts_flows')->where([
                                    ['flow_id', '=', $listData[$i]->id],
                                ])->get();
                                
                                if($element->isEmpty()){
                                    $id = $listData[$i]->id;
                                $fts_flow = new fts_flow;
                                $fts_flow->flow_id = $listData[$i]->id;
                                $fts_flow->flow_versionid = $listData[$i]->versionId;
                                $fts_flow->flow_description = $listData[$i]->description;
                                $fts_flow->flow_status = $listData[$i]->status;
                                $fts_flow->flow_date = self::convertdate($listData[$i]->date);
                                $fts_flow->flow_amountusd = $listData[$i]->amountUSD;
                                if(isset($listData[$i]->originalAmount))
                                $fts_flow->flow_originalamount = $listData[$i]->originalAmount;
                                if(isset($listData[$i]->originalcurrency))
                                $fts_flow->flow_originalcurrency = $listData[$i]->originalcurrency;
                                if(isset($listData[$i]->exchangeRate))
                                $fts_flow->flow_exchangerate = $listData[$i]->exchangeRate;
                                $fts_flow->flow_firstreporteddate = self::convertdate($listData[$i]->firstReportedDate);
                                $fts_flow->flow_budgetyear = $listData[$i]->budgetYear;
                                $fts_flow->flow_decisiondate = self::convertdate($listData[$i]->decisionDate);
                                $fts_flow->flow_flowtype = $listData[$i]->flowType;
                                $fts_flow->flow_contributiontype = $listData[$i]->contributionType;
                                //$fts_flow->flow_keywords = $listData[$i]->keywords;
                                $fts_flow->flow_method = $listData[$i]->method;
                                //$fts_flow->flow_parentflowid = $listData[$i]->parentflowid;
                                //$fts_flow->flow_childflowids = $listData[$i]->childflowids;
                                $fts_flow->flow_newmoney = $listData[$i]->newMoney;
                                //$fts_flow->flow_createdat = self::convertdate($listData[$i]->createdAt);
                                //$fts_flow->flow_updatedat = self::convertdate($listData[$i]->updatedAt);
                                $fts_flow->flow_boundary = $listData[$i]->boundary;
                                $fts_flow->flow_onboundary = $listData[$i]->onBoundary;
                                if(isset($listData[$i]->refCode))
                                $fts_flow->flow_refcode = $listData[$i]->refCode;
                                $fts_flow->save();
    
                                //CREATE SOURCE OBJECTS
                                if(isset($listData[$i]->sourceObjects)){
                                    $flowSources = $listData[$i]->sourceObjects;
                                    
                                    for($x = 0; $x < count($flowSources); $x++) {
                                        
                                        if(isset($flowSources[$x]->type)){
                                            $sourceType = $flowSources[$x]->type;
    
                                            switch ($sourceType) {
                                                case "Organization":
                                                    //organizations
                                                    $elements = DB::table('organizations')->where([
                                                        ['organization_id', '=', $flowSources[$x]->id],
                                                    ])->get();
                                                    if($elements->isEmpty()){
                                                        $organization = new organization;
                                                        $organization->organization_id = $flowSources[$x]->id;
                                                        $organization->organization_name = $flowSources[$x]->name;
                                                        $organization->save();
                                                    };
    
                                                    //ftsflow_from_org
                                                    $elements = DB::table('ftsflow_from_orgs')->where([
                                                        ['flow_id', '=', $id],
                                                        ['organization_id', '=', $flowSources[$x]->id],
                                                    ])->get();
                                                    
                                                    if($elements->isEmpty()){
                                                        $ftsflow_from_org = new ftsflow_from_org;
                                                        $ftsflow_from_org->flow_id = $id;
                                                        $ftsflow_from_org->organization_id = $flowSources[$x]->id;
                                                        $ftsflow_from_org->save();
                                                    };
    
                                                    break;
                                                case "UsageYear":
                                                    //usageyears
                                                    $elements = DB::table('usageyears')->where([
                                                        ['usageyear_id', '=', $flowSources[$x]->id],
                                                    ])->get();
                                                    if($elements->isEmpty()){
                                                        $usageyear = new usageyear;
                                                        $usageyear->usageyear_id = $flowSources[$x]->id;
                                                        $usageyear->usageyear_name = $flowSources[$x]->name;
                                                        $usageyear->save();
                                                    };
    
                                                    //ftsflow_from_usageyear
                                                    $elements = DB::table('ftsflow_from_usageyears')->where([
                                                        ['flow_id', '=', $id],
                                                        ['usageyear_id', '=', $flowSources[$x]->id],
                                                    ])->get();
                                                    
                                                    
                                                    if($elements->isEmpty()){
                                                        $ftsflow_from_usageyear = new ftsflow_from_usageyear;
                                                        $ftsflow_from_usageyear->flow_id = $id;
                                                        $ftsflow_from_usageyear->usageyear_id = $flowSources[$x]->id;
                                                        $ftsflow_from_usageyear->save();
                                                    };
                                                    break;
                                                case "Location":
                                                    $elements = DB::table('ftsflow_from_locations')->where([
                                                        ['flow_id', '=',  $id],
                                                        ['location_other_id', '=', $flowSources[$x]->id],
                                                    ])->get();
                                                    
                                                    if($elements->isEmpty()){
                                                        $element = DB::table('location_others')->where([
                                                            ['location_other_id', '=', $flowSources[$x]->id],
                                                        ])->get();
                                                        
                                                        if($element->isEmpty()){
                                                            $IdLocation=date('YmdHis').rand (0, 9999)."i";
                                                            $location = new location;
                                                            $location->location_id = $IdLocation;
                                                            $location->locationtype_id = 2;
                                                            $location->location_caption_fr =$flowSources[$x]->name;
                                                            $location->location_caption_en = $flowSources[$x]->name;
                                                            $location->location_pcode_iso2 = "";
                                                            $location->location_pcode_iso3 ="";
                                                            $location->location_admin_level = 0;
                                                            $location->location_to_update = true;
                                                            $location->location_upd_comment = "FTS : Location without pcode";
                                                            $location->location_source = "FTS";
                                                            $location->save();
                                            
                                                            $location_other = new location_other;
                                                            $location_other->location_other_id = $flowSources[$x]->id;
                                                            $location_other->location_id = $IdLocation;
                                                            $location_other->location_other_source = "fts";
                                                            $location_other->save();
                                            
                                                            $c++;
                                                        }
    
                                                        $ftsflow_from_location = new ftsflow_from_location;
                                                        $ftsflow_from_location->flow_id = $id;
                                                        $ftsflow_from_location->location_other_id = $flowSources[$x]->id;
                                                        $ftsflow_from_location->save();
                                                    }
    
                                                    break;
                                                case "Plan":
                                                    $plans = DB::table('plans')->where([
                                                        ['plan_id', '=', $flowSources[$x]->id],
                                                    ])->get();
    
                                                    if($plans->isEmpty()){
                                                        $plan = new plan;
                                                        $plan->plan_id = $flowSources[$x]->id;
                                                        $plan->plan_name = $flowSources[$x]->name;
                                                        $plan->save();
                                                        $c++;
                                                    }
    
                                                    //ftsflow_dest_plan
                                                    $elements = DB::table('ftsflow_from_plans')->where([
                                                        ['flow_id', '=', $id],
                                                        ['plan_id', '=', $flowSources[$x]->id],
                                                    ])->get();
                                                    
                                                    if($elements->isEmpty()){
                                                        $ftsflow_from_plan = new ftsflow_from_plan;
                                                        $ftsflow_from_plan->flow_id = $id;
                                                        $ftsflow_from_plan->plan_id = $flowSources[$x]->id;
                                                        $ftsflow_from_plan->save();
                                                    };
                                                    break;
                                                case "Cluster":
                                                    //rpm_clusters
                                                        $elements = DB::table('rpm_clusters')->where([
                                                            ['cluster_id', '=', $flowSources[$x]->id],
                                                        ])->get();
                                                        if($elements->isEmpty()){
                                                            $rpm_cluster = new rpm_cluster;
                                                            $rpm_cluster->cluster_id = $flowSources[$x]->id;
                                                            $rpm_cluster->cluster_description = $flowSources[$x]->name;
                                                            $rpm_cluster->save();
                                                        };
    
                                                    //ftsflow_from_cluster
                                                        $elements = DB::table('ftsflow_from_clusters')->where([
                                                            ['flow_id', '=', $id],
                                                            ['cluster_id', '=', $flowSources[$x]->id],
                                                        ])->get();
                                                        
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_from_cluster = new ftsflow_from_cluster;
                                                            $ftsflow_from_cluster->flow_id = $id;
                                                            $ftsflow_from_cluster->cluster_id = $flowSources[$x]->id;
                                                            $ftsflow_from_cluster->save();
                                                        };
                                                    break;
                                                case "GlobalCluster":
                                                    //globalcluster
                                                        $elements = DB::table('globalclusters')->where([
                                                            ['globalclusters_id', '=', $flowSources[$x]->id],
                                                        ])->get();
                                                        if($elements->isEmpty()){
                                                            $globalcluster = new globalcluster;
                                                            $globalcluster->globalclusters_id = $flowSources[$x]->id;
                                                            $globalcluster->globalclusters_name = $flowSources[$x]->name;
                                                            $globalcluster->save();
                                                        };
    
                                                    //ftsflow_dest_globalcluster
                                                        $elements = DB::table('ftsflow_dest_globalclusters')->where([
                                                            ['flow_id', '=', $id],
                                                            ['globalclusters_id', '=', $flowSources[$x]->id],
                                                        ])->get();
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_dest_globalcluster = new ftsflow_dest_globalcluster;
                                                            $ftsflow_dest_globalcluster->flow_id = $id;
                                                            $ftsflow_dest_globalcluster->globalclusters_id = $flowSources[$x]->id;
                                                            $ftsflow_dest_globalcluster->save();
                                                        };
                                                    break;
                                                case "Emergency": 
                                                    //EMERGENCY
                                                        $elements = DB::table('emergencies')->where([
                                                            ['emergency_id', '=', $flowSources[$x]->id],
                                                        ])->get();
                                                        
                                                        if($elements->isEmpty()){
                                                            $emergency = new emergency;
                                                            $emergency->emergency_id = $flowSources[$x]->id;
                                                            $emergency->emergency_name = $flowSources[$x]->name;
                                                            $emergency->save();
                                                        }
    
                                                    //ftsflow_dest_emergencies
                                                        $elements = DB::table('ftsflow_from_emergencies')->where([
                                                            ['flow_id', '=', $id],
                                                            ['emergency_id', '=', $flowSources[$x]->id],
                                                        ])->get();
                                                        
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_from_emergency = new ftsflow_from_emergency;
                                                            $ftsflow_from_emergency->flow_id = $id;
                                                            $ftsflow_from_emergency->emergency_id = $flowSources[$x]->id;
                                                            $ftsflow_from_emergency->save();
                                                        };
                                                    break;
                                                case "Project":
                                                    //project
                                                        $elements = DB::table('projects')->where([
                                                            ['project_id', '=', $flowSources[$x]->id],
                                                        ])->get();
                                                        if($elements->isEmpty()){
                                                            $code = "";
                                                            if(isset($flowSources[$x]->code)){
                                                                $code = $flowSources[$x]->code;
                                                            }
                                                            $project = new project;
                                                            $project->project_id = $flowSources[$x]->id;
                                                            $project->project_code = $code;
                                                            $project->project_name = $flowSources[$x]->name;
                                                            $project->save();
                                                        };
    
                                                    //ftsflow_from_projet
                                                        $elements = DB::table('ftsflow_from_projets')->where([
                                                            ['flow_id', '=', $id],
                                                            ['project_id', '=', $flowSources[$x]->id],
                                                        ])->get();
                                                        
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_from_projet = new ftsflow_from_projet;
                                                            $ftsflow_from_projet->flow_id = $id;
                                                            $ftsflow_from_projet->project_id = $flowSources[$x]->id;
                                                            $ftsflow_from_projet->save();
                                                        };
                                                    break;
                                                default:
                                                    echo "New flow source : ".$sourceType."</br>";
                                                    break;
                                            } 
                                        }
                                    }
                                }
                                
                                //CREATE DESTINATION OBJECTS
                                if(isset($listData[$i]->destinationObjects)){
                                    $flowDestinations = $listData[$i]->destinationObjects;
                                    $c=0;
                                    for($x = 0; $x < count($flowDestinations); $x++) {
                                        
                                        if(isset($flowDestinations[$x]->type)){
                                            $sourceType = $flowDestinations[$x]->type;
    
                                            switch ($sourceType) {
                                                case "Plan":
    
                                                    $plans = DB::table('plans')->where([
                                                        ['plan_id', '=', $flowDestinations[$x]->id],
                                                    ])->get();
                                                
                                                    if($plans->isEmpty()){
                                                        $plan = new plan;
                                                        $plan->plan_id = $flowDestinations[$x]->id;
                                                        $plan->plan_name = $flowDestinations[$x]->name;
                                                        $plan->save();
                                                    }
                                                    //ftsflow_dest_plan
                                                    $elements = DB::table('ftsflow_dest_plans')->where([
                                                        ['flow_id', '=', $id],
                                                        ['plan_id', '=', $flowDestinations[$x]->id],
                                                    ])->get();
                                                    
                                                    if($elements->isEmpty()){
                                                        $ftsflow_dest_plan = new ftsflow_dest_plan;
                                                        $ftsflow_dest_plan->flow_id = $id;
                                                        $ftsflow_dest_plan->plan_id = $flowDestinations[$x]->id;
                                                        $ftsflow_dest_plan->save();
                                                    };
    
                                                    $c++;
                                                    break;
                                                case "Organization":
                                                    //organizations
                                                        $elements = DB::table('organizations')->where([
                                                            ['organization_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        if($elements->isEmpty()){
                                                            $organization = new organization;
                                                            $organization->organization_id = $flowDestinations[$x]->id;
                                                            $organization->organization_name = $flowDestinations[$x]->name;
                                                            $organization->save();
                                                        };
    
                                                    //ftsflow_dest_org
                                                        $elements = DB::table('ftsflow_dest_orgs')->where([
                                                            ['flow_id', '=', $id],
                                                            ['organization_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_dest_org = new ftsflow_dest_org;
                                                            $ftsflow_dest_org->flow_id = $id;
                                                            $ftsflow_dest_org->organization_id = $flowDestinations[$x]->id;
                                                            $ftsflow_dest_org->save();
                                                        };
                                                    break;
                                                case "Cluster":
                                                    //rpm_clusters
                                                        $elements = DB::table('rpm_clusters')->where([
                                                            ['cluster_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        if($elements->isEmpty()){
                                                            $rpm_cluster = new rpm_cluster;
                                                            $rpm_cluster->cluster_id = $flowDestinations[$x]->id;
                                                            $rpm_cluster->cluster_description = $flowDestinations[$x]->name;
                                                            $rpm_cluster->save();
                                                        };
    
                                                    //ftsflow_dest_cluster
                                                        $elements = DB::table('ftsflow_dest_clusters')->where([
                                                            ['flow_id', '=', $id],
                                                            ['cluster_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_dest_cluster = new ftsflow_dest_cluster;
                                                            $ftsflow_dest_cluster->flow_id = $id;
                                                            $ftsflow_dest_cluster->cluster_id = $flowDestinations[$x]->id;
                                                            $ftsflow_dest_cluster->save();
                                                        };
                                                    break;
                                                case "GlobalCluster":
                                                    //globalcluster
                                                        $elements = DB::table('globalclusters')->where([
                                                            ['globalclusters_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        if($elements->isEmpty()){
                                                            $globalcluster = new globalcluster;
                                                            $globalcluster->globalclusters_id = $flowDestinations[$x]->id;
                                                            $globalcluster->globalclusters_name = $flowDestinations[$x]->name;
                                                            $globalcluster->save();
                                                        };
    
                                                    //ftsflow_dest_globalcluster
                                                        $elements = DB::table('ftsflow_dest_globalclusters')->where([
                                                            ['flow_id', '=', $id],
                                                            ['globalclusters_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_dest_globalcluster = new ftsflow_dest_globalcluster;
                                                            $ftsflow_dest_globalcluster->flow_id = $id;
                                                            $ftsflow_dest_globalcluster->globalclusters_id = $flowDestinations[$x]->id;
                                                            $ftsflow_dest_globalcluster->save();
                                                        };
                                                    break;
                                                case "Location":
                                                    //$api_rpm_location = api_rpm_location::firstOrCreate(['LOCATION_ID' => $flowDestinations[$x]->id],['LOCATION_NAME' => $flowDestinations[$x]->name]);
                                                    //$ftsflow_dest_location = ftsflow_dest_location::firstOrCreate(['flow_id' => $id],['location_other_id' => $flowDestinations[$x]->id]);
                                                    $elements = DB::table('ftsflow_dest_locations')->where([
                                                        ['flow_id', '=',  $id],
                                                        ['location_other_id', '=', $flowDestinations[$x]->id],
                                                    ])->get();
                                                    
                                                    if($elements->isEmpty()){
                                                        $element = DB::table('location_others')->where([
                                                            ['location_other_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        
                                                        if($element->isEmpty()){
                                                            $IdLocation=date('YmdHis').rand (0, 9999)."i";
                                                            $location = new location;
                                                            $location->location_id = $IdLocation;
                                                            $location->locationtype_id = 2;
                                                            $location->location_caption_fr =$flowDestinations[$x]->name;
                                                            $location->location_caption_en = $flowDestinations[$x]->name;
                                                            $location->location_pcode_iso2 = "";
                                                            $location->location_pcode_iso3 ="";
                                                            $location->location_admin_level = 0;
                                                            $location->location_to_update = true;
                                                            $location->location_upd_comment = "FTS : Location without pcode";
                                                            $location->location_source = "FTS";
                                                            $location->save();
                                            
                                                            $location_other = new location_other;
                                                            $location_other->location_other_id = $flowDestinations[$x]->id;
                                                            $location_other->location_id = $IdLocation;
                                                            $location_other->location_other_source = "fts";
                                                            $location_other->save();
                                            
                                                            $c++;
                                                        }
    
                                                        $ftsflow_dest_location = new ftsflow_dest_location;
                                                        $ftsflow_dest_location->flow_id = $id;
                                                        $ftsflow_dest_location->location_other_id = $flowDestinations[$x]->id;
                                                        $ftsflow_dest_location->save();
                                                    }
    
                                                    break;
                                                case "Project":
                                                    //project
                                                        $elements = DB::table('projects')->where([
                                                            ['project_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        if($elements->isEmpty()){
                                                            $code = "";
                                                            if(isset($flowDestinations[$x]->code)){
                                                                $code = $flowDestinations[$x]->code;
                                                            }
                                                            $project = new project;
                                                            $project->project_id = $flowDestinations[$x]->id;
                                                            $project->project_code = $code;
                                                            $project->project_name = $flowDestinations[$x]->name;
                                                            $project->save();
                                                        };
    
                                                    //ftsflow_dest_projet
                                                        $elements = DB::table('ftsflow_dest_projets')->where([
                                                            ['flow_id', '=', $id],
                                                            ['project_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_dest_projet = new ftsflow_dest_projet;
                                                            $ftsflow_dest_projet->flow_id = $id;
                                                            $ftsflow_dest_projet->project_id = $flowDestinations[$x]->id;
                                                            $ftsflow_dest_projet->save();
                                                        };
                                                    break;
                                                case "UsageYear":
                                                    //usageyears
                                                        $elements = DB::table('usageyears')->where([
                                                            ['usageyear_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        if($elements->isEmpty()){
                                                            $usageyear = new usageyear;
                                                            $usageyear->usageyear_id = $flowDestinations[$x]->id;
                                                            $usageyear->usageyear_name = $flowDestinations[$x]->name;
                                                            $usageyear->save();
                                                        };
    
                                                    //ftsflow_dest_usageyears
                                                        $elements = DB::table('ftsflow_dest_usageyears')->where([
                                                            ['flow_id', '=', $id],
                                                            ['usageyear_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_dest_usageyear = new ftsflow_dest_usageyear;
                                                            $ftsflow_dest_usageyear->flow_id = $id;
                                                            $ftsflow_dest_usageyear->usageyear_id = $flowDestinations[$x]->id;
                                                            $ftsflow_dest_usageyear->save();
                                                        };
                                                    break;
                                                case "Emergency": 
                                                    //EMERGENCY
                                                        $elements = DB::table('emergencies')->where([
                                                            ['emergency_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        
                                                        if($elements->isEmpty()){
                                                            $emergency = new emergency;
                                                            $emergency->emergency_id = $flowDestinations[$x]->id;
                                                            $emergency->emergency_name = $flowDestinations[$x]->name;
                                                            //$emergency->emergency_date = self::convertDate($flowDestinations[$x]->date);
                                                            $emergency->save();
                                                        }
    
                                                    //ftsflow_dest_emergencies
                                                        $elements = DB::table('ftsflow_dest_emergencies')->where([
                                                            ['flow_id', '=', $id],
                                                            ['emergency_id', '=', $flowDestinations[$x]->id],
                                                        ])->get();
                                                        
                                                        
                                                        if($elements->isEmpty()){
                                                            $ftsflow_dest_emergency = new ftsflow_dest_emergency;
                                                            $ftsflow_dest_emergency->flow_id = $id;
                                                            $ftsflow_dest_emergency->emergency_id = $flowDestinations[$x]->id;
                                                            $ftsflow_dest_emergency->save();
                                                        };
                                                    break;
                                                default:
                                                    echo "New flow destination : ".$sourceType."</br>";
                                                    break;
                                            } 
                                        }
                                        
                                    }
                                }
    
                                $numberNewRecords++;
                                }


                                
                            }
                            $r++;
                            echo "Finished : ".$numberNewRecords." new flows created on ".count($listData)." for plan:".$plan_->plan_id." - ".$plan_->plan_name."<br/>";
                        }else{
                            $HasNextPage = false;
                        }
                    }
                }

                if($HasNextPage == false){
                    echo "    Fin de page : ".$p."<br/>";
                    break; 
                }
            }
        }
        
    }



    public function convertDate($date)
    {
        $result = trim(substr($date,0,10).' '.substr($date,11,8));
        if($result==''){
            $result = null;
        }
        return $result;
    }

    public function clearTables()
    {
        plan::truncate();
        plan_emergency::truncate();
        plan_usage_year::truncate();
        plan_location::truncate();
        plan_category::truncate();
        plan_cluster_activity::truncate();
        cluster_activity::truncate();
        plan_cluster_objective::truncate();
        strategic_objective::truncate();
        cluster_objective::truncate();
        sub_strategic_objective::truncate();
        plan_substrat_objective::truncate();
        api_rpm_cluster::truncate();
        plan_cluster::truncate();
        rpm_cost::truncate();
        rpm_contact::truncate();
        fts_flow::truncate();
        globalcluster::truncate();
        fieldcluster::truncate();
        organization::truncate();
        ftsflow_from_org::truncate();
        ftsflow_usageyear::truncate();
        ftsflow_dest_org::truncate();
        ftsflow_dest_globalcluster::truncate();
        ftsflow_dest_cluster::truncate();
        ftsflow_location::truncate();
        project::truncate();
        ftsflow_dest_plan::truncate();
        ftsflow_dest_projet::truncate();
        organisation_category::truncate();
        category_organization::truncate();
        category_emergency::truncate();
        emergency_category::truncate();
        emergency_location::truncate();

        /*
        api_emergency::truncate();
        plan::truncate();
        r_api_plan_avoir_emergency::truncate();
        //planyear::truncate();
        r_api_plan_avoir_year::truncate();
        r_api_plan_avoir_location::truncate();
        r_api_plan_avoir_category::truncate();
        r_api_plan_avoir_cluster_activity::truncate();
        api_rpm_cluster_activity::truncate();
        r_api_plan_avoir_cluster_strategic_objective::truncate();
        api_rpm_strategic_objective::truncate();
        r_api_plan_avoir_cluster_objective::truncate();
        api_rpm_cluster_objective::truncate();
        api_rpm_sub_strategic_objective::truncate();
        r_api_plan_avoir_sub_strategic_objective::truncate();
        api_rpm_cluster::truncate();
        r_api_plan_avoir_cluster::truncate();
        api_rpm_cost::truncate();
        api_rpm_contact::truncate();
        fts_flow::truncate();
        api_globalcluster::truncate();
        api_rpm_location::truncate();
        api_organization::truncate();
        r_api_ftsflow_viens_de_organisation::truncate();
        usageyear::truncate();
        r_api_ftsflow_viens_de_usageyear::truncate();
        r_api_ftsflow_destine_a_organisation::truncate();
        r_api_ftsflow_destine_a_cluster::truncate();
        r_api_ftsflow_destine_a_globalcluster::truncate();
        r_api_ftsflow_destine_a_location::truncate();
        api_project::truncate();
        r_api_ftsflow_destine_a_usage_year::truncate();
        r_api_ftsflow_destine_a_plan::truncate();
        r_api_ftsflow_destine_a_projet::truncate();
        r_api_organisation_avoir_category::truncate();
        api_organization_category::truncate();
        r_api_emergency_avoir_category::truncate();
        api_emergency_category::truncate();
        r_emergency_avoir_location::truncate();
        */

    }

    public function Synchronise(){
        //PLANS
        Excel::store(new PlansExport, 'Plans.xlsx', 'local');
        self::UploadToCloud('Plans.xlsx');

        //FLOWS
        Excel::store(new flowsExport, 'Flows.xlsx', 'local');
        self::UploadToCloud('Flows.xlsx');

        //ORGANISATIONS
        Excel::store(new OrganisationsExport, 'Organisations.xlsx', 'local');
        self::UploadToCloud('Organisations.xlsx');

        //WORLD EMERGENCIES
        Excel::store(new WorldEmergencyExport, 'Emergencies.xlsx', 'local');
        self::UploadToCloud('Emergencies.xlsx');

    }

    public function XXXXwriteToCloud($spreadsheetId,$sheetName,$rows,$header) {
        Sheets::setService(Google::make('sheets'));
        Sheets::spreadsheet($spreadsheetId);
        Sheets::sheet($sheetName)->clear();
        $range = "";
        $line = 1;
        
        //WRITE HEADER
        Sheets::sheet($sheetName)->range($range)->append([$header]);

        //WRITE DATA
        foreach ($rows as $row){
            $range = "A".$line.":O".$line;
            $dataArray = [];
            
            foreach ($row as $key => $value) {
                if (is_numeric($value)) {
                    array_push($dataArray,floatval($value));
                }else{
                    array_push($dataArray,strval($value));
                    
                }
                
            }
    
            Sheets::sheet($sheetName)->range($range)->append([$dataArray]);
            $line++;
        }
    }

    public function UploadToCloud($filename){
        $mainDisk = Storage::disk('google');

        //delete old file
        $dir = '/';
        $recursive = false; // Get subdirectories also?
        $contents = collect(Storage::cloud()->listContents($dir, $recursive));
        $files = $contents
        ->where('type', '=', 'file')
        ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
        ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
        ->all();

        foreach ($files as $file){
            Storage::cloud()->delete($file['path']);
        }
        
        //ADD NEW FILE
        $filePath = base_path("storage/drive/".$filename);
        $fileData = File::get($filePath);
        Storage::cloud()->put($filename, $fileData);
        return 'File was saved to Google Drive';
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        //http://php.net/manual/fr/function.checkdate.php
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function deleteall()
    {
        DB::table('kf_disags')->delete();
        //DB::table('disaggregations')->delete();
        DB::table('type_disaggregations')->delete();
        DB::table('kf_reports')->delete();
        //DB::table('headers')->delete();
        DB::table('report_year_trends')->delete();
        DB::table('recalculated_reports')->delete();
        DB::table('kf_indicators')->delete();
        //DB::table('kf_subcategories')->delete();
        //DB::table('kf_categs')->delete();
        //DB::table('emergency_locations')->delete();
        DB::table('ftsflow_dest_locations')->delete();
        DB::table('ftsflow_from_locations')->delete();
        DB::table('plan_locations')->delete();
        DB::table('location_others')->delete();
        DB::table('locations')->delete();
        echo 'finish';
        //DB::table('location_types')->delete();
    }


}
