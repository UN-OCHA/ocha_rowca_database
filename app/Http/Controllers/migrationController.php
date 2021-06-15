<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\kf_categ as kf_categ;
use App\kf_subcategory as kf_subcategory;
use App\kf_indicator as kf_indicator;
use App\location as location;
use App\location_type as location_type;
use App\kf_report as kf_report;
use App\type_disaggregation as type_disaggregation;
use App\disaggregation as disaggregation;
use App\kf_disag as kf_disag;

class migrationController extends Controller
{

    public function getOrsDatas()
    {
        echo "Migration of ORS datas to OCHA db V3<br/>";

        //SUPPRESSION DES DONNEES
        
        
        DB::table('kf_disags')->delete();
        DB::table('disaggregations')->delete();
        DB::table('type_disaggregations')->delete();
        DB::table('kf_reports')->delete();
        DB::table('headers')->delete();
        DB::table('report_year_trends')->delete();
        DB::table('recalculated_reports')->delete();
        DB::table('kf_indicators')->delete();
        DB::table('kf_subcategories')->delete();
        DB::table('kf_categs')->delete();
        
        DB::table('emergency_locations')->delete();
        DB::table('ftsflow_dest_locations')->delete();
        DB::table('ftsflow_from_locations')->delete();
        DB::table('plan_locations')->delete();
        DB::table('location_others')->delete();
        DB::table('locations')->delete();
        
        DB::table('location_types')->delete();

        // kf_category
        $orsDatas = DB::connection('mysql2')->select("SELECT * FROM keyfigurecategorydetails");
        DB::connection('pgsql');
        foreach ($orsDatas as $element) {
            
            $object = kf_categ::where('kfcateg_id', $element->KeyFigureCategoryId)->first();
          
            if(!$object){
                $kf_categ = new kf_categ;
                $kf_categ->kfcateg_id = $element->KeyFigureCategoryId;
                $kf_categ->kfcateg_caption_fr = "";
                $kf_categ->kfcateg_caption_en = $element->KeyFigureCategory;
                $kf_categ->save();
            }else{
                $object->kfcateg_caption_fr = utf8_encode($element->KeyFigureCategory);
                $object->save();
            }
        }


        // kf_subcategory
        $orsDatas = DB::connection('mysql2')->select("SELECT kfscd.KeyFigureSubCategoryDetailId, kfscd.KeyFigureSubCategoryId, kfscd.SiteLanguageId, kfscd.KeyFigureSubCategory,kfsc.KeyFigureCategoryId FROM keyfiguresubcategorydetails kfscd left join keyfiguresubcategories kfsc on kfsc.KeyFigureSubCategoryId = kfscd.KeyFigureSubCategoryId");
        foreach ($orsDatas as $element) {
            $object = kf_subcategory::where('kfsubcategory_id', $element->KeyFigureSubCategoryId)->first();

            if(!$object){
                $kf_subcategory = new kf_subcategory;
                $kf_subcategory->kfsubcategory_id = $element->KeyFigureSubCategoryId;
                $kf_subcategory->kfcateg_id = $element->KeyFigureCategoryId;
                $kf_subcategory->kfsubcategory_caption_fr = "";
                $kf_subcategory->kfsubcategory_caption_en = $element->KeyFigureSubCategory;
                $kf_subcategory->save();
            }else{
                $object->kfsubcategory_caption_fr = utf8_encode($element->KeyFigureSubCategory);
                $object->save();
            }
        }


        // kf_indicator
        $orsDatas = DB::connection('mysql2')->select("SELECT kfdi.KeyFigureIndicatorDetailId, kfdi.KeyFigureIndicatorId, kfdi.SiteLanguageId, kfdi.KeyFigureIndicator,kfi.KeyFigureSubCategoryId FROM keyfigureindicatordetails kfdi left join keyfigureindicators kfi on kfdi.KeyFigureIndicatorId = kfi.KeyFigureIndicatorId");
        foreach ($orsDatas as $element) {
            $object = kf_indicator::where('kfind_id', $element->KeyFigureIndicatorId)->first();

            if(!$object){
                $kf_indicator = new kf_indicator;
                $kf_indicator->kfind_id = $element->KeyFigureIndicatorId;
                $kf_indicator->kfsubcategory_id = $element->KeyFigureSubCategoryId;
                $kf_indicator->kfindic_caption_fr = "";
                $kf_indicator->kfindic_caption_en = $element->KeyFigureIndicator;
                $kf_indicator->id_method = "1";
                $kf_indicator->save();
            }else{
                $object->kfindic_caption_fr = utf8_encode($element->KeyFigureIndicator);
                $object->save();
            }
        }




        // location_type
        $orsDatas = DB::connection('mysql2')->select("SELECT * FROM locationtypes");
        foreach ($orsDatas as $element) {
            $location_type = new location_type;
            $location_type->locationtype_id = $element->LocationTypeId;
            $location_type->location_type = $element->LocationType;
            $location_type->location_type_ors = $element->LocationTypeORS;
            $location_type->save();
        }


        // location
        $orsDatas = DB::connection('mysql2')->select("SELECT * FROM location2s");
        foreach ($orsDatas as $element) {
            $location = new location;
            $location->location_id = $element->LocationId;
            $location->location_caption_fr = "";
            $location->location_caption_en = utf8_encode($element->LocationName);
            $location->location_pcode_iso2 = "";
            $location->location_pcode_iso3 = $element->LocationPCode;
            $location->locationtype_id = $element->LocationTypeId;
            $location->location_parent_id = $element->LocationParentId;
            $location->save();
        }
        
        $location = new location;
        $location->location_id = "9999999";
        $location->location_caption_fr = "";
        $location->location_caption_en = "Inconnu";
        $location->location_pcode_iso2 = "";
        $location->location_pcode_iso3 = "";
        $location->locationtype_id = "1";
        $location->location_parent_id = "";
        $location->save();


        // kf_report
        $orsDatas = DB::connection('mysql2')->select("SELECT kfrd.KeyFigureReportDetailId, kfrd.KeyFigureReportId, kfrd.LocationId, kfr.KeyFigureIndicatorId, kfrd.TotalTotal, kfrd.TotalMen, kfrd.TotalWomen, kfrd.NeedTotal, kfrd.NeedMen, kfrd.NeedWomen, kfrd.TargetedTotal, kfrd.TargetedMen, kfrd.TargetedWomen, kfrd.FromLocation, kfrd.KeyFigureSource, kfr.KeyFigureReportedDate FROM keyfigurereportdetails kfrd left join keyfigurereports kfr on kfr.KeyFigureReportId = kfrd.KeyFigureReportId");
        
        //create type_disaggregation
        $idypeDisag=date('YmdHis').rand (0, 9999)."i";
        $type_disaggregation = new type_disaggregation;
        $type_disaggregation->id_type_disaggregation = $idypeDisag;
        $type_disaggregation->label_type_disaggregation = "Sexe";
        $type_disaggregation->save();


        //create disaggregation
        $idDisagHomme=date('YmdHis').rand (0, 9999)."i";
        $disaggregation = new disaggregation;
        $disaggregation->id_disaggregation = $idDisagHomme;
        $disaggregation->id_type_disaggregation = $idypeDisag;
        $disaggregation->label_disaggregation = "Homme";
        $disaggregation->save();

        $idDisagFemme=date('YmdHis').rand (0, 9999)."i";
        $disaggregation = new disaggregation;
        $disaggregation->id_disaggregation = $idDisagFemme;
        $disaggregation->id_type_disaggregation = $idypeDisag;
        $disaggregation->label_disaggregation = "Femme";
        $disaggregation->save();

        
        foreach ($orsDatas as $element) {
            $object = kf_report::where('kfreport_id', $element->KeyFigureReportId)->first();
            $locationId = "9999999";


            $verifLocation = location::where('location_id', $element->LocationId)->first();
            if($verifLocation){
                $locationId= $verifLocation->location_id;
            }else{
                echo "+".$locationId."+";
            }

            //Disaggregations
            $id=date('YmdHis').rand (0, 9999)."i";

            //kf_report
            if(!$object){
                $kf_report = new kf_report;
                $kf_report->kfreport_id = $element->KeyFigureReportId;
                $kf_report->kfind_id = $element->KeyFigureIndicatorId;
                $kf_report->location_id = $locationId;
                $kf_report->kfreport_value = $element->TotalTotal;
                $kf_report->kfreport_source = utf8_encode($element->KeyFigureSource);
                $kf_report->kfreport_date = $element->KeyFigureReportedDate;
                $kf_report->save();

                //add disaggregation Homme
                if($element->TotalMen!=NULL){
                    $idDisag=date('YmdHis').rand (0, 9999)."i";
                    $kf_disag = new kf_disag;
                    $kf_disag->kfreport_id = $element->KeyFigureReportId;
                    $kf_disag->id_disaggregation = $idDisagHomme;
                    $kf_disag->disaggregated_value_label = "Total Men";
                    $kf_disag->disaggregated_value = $element->TotalMen;;
                    $kf_disag->disaggregated_value_comment = "";
                    $kf_disag->save();
                }

                //add disaggregation Femme
                if($element->TotalWomen!=NULL){
                    $idDisag=date('YmdHis').rand (0, 9999)."i";
                    $kf_disag = new kf_disag;
                    $kf_disag->kfreport_id = $element->KeyFigureReportId;
                    $kf_disag->id_disaggregation = $idDisagFemme;
                    $kf_disag->disaggregated_value_label = "Total Women";
                    $kf_disag->disaggregated_value = $element->TotalWomen;;
                    $kf_disag->disaggregated_value_comment = "";
                    $kf_disag->save();
                }


            }else{
                
            }
        }
    }
}
