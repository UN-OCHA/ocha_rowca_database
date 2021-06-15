@extends('filterV2')
@section('title', 'Crisis zones')
@section('content')

<?php
    function convertToUnit($val,$decimal){
        $result = "";
        if($val<1000){
            $result = $val;
        }else{
            if($val<1000000){
                $result = round($val/1000)."K";
            }else{
                if($val<1000000000){
                    $result = round($val/1000000,$decimal)."M";
                }else{
                    $result = round($val/1000000000,$decimal)."B";
                }
            }
        }
        return $result;
    }

    function getTrendDataOld($datas){
        $dates = array();
        $locations = array();
        $trendsData = array();
        $arrayModel = array("year"=>0);

        foreach ($datas as $data){
            $year = intval(substr($data["date"],0,4));

            if (!in_array($year, $dates) && $year!="")
            {
                array_push($dates,$year);
            }

            if (!in_array($data["adminName"], $locations) && $data["adminName"]!="")
            {
                array_push($locations,$data["adminName"]);
                $arrayModel = array_merge($arrayModel, array($data["adminName"]=>0));
            }
        }
        
        
        foreach ($dates as $date){
            $arrayTemp = $arrayModel;
            if ($arrayTemp["year"]==0) {
                $arrayTemp["year"] = $date;
            }
            foreach ($locations as $location){
                $lastDate = "1900-01-01";
                $lastValue = 0;
                foreach ($datas as $data){

                    $year = intval(substr($data["date"],0,4));

                    if($date==$year && $location==$data["adminName"] && $data["value"]!=0 && $data["value"]!=""){

                        if($data["date"]==$lastDate){
                            $lastValue = $lastValue + $data["value"];
                        }else{
                            if($data["date"] > $lastDate){
                                $lastDate = $data["date"];
                                $lastValue = $data["value"];
                            }
                        }
                    }
                }
                $arrayTemp[$location] = $lastValue;
            }
            array_push($trendsData,$arrayTemp);
        }
        return $trendsData;
    }

    function getTrendData($datas){
        $locations = array();
        $trendsData = array();
        $dates = array();

        
        foreach ($datas as $data){
            $year = intval(substr($data["date"],0,4));

            //recuperation des années
            if (!in_array($year, $dates) && $year!="")
            {
                array_push($dates,$year);
            }

            //recuperation des localites
            if (!in_array($data["adminName"], $locations) && $data["adminName"]!="")
            {
                array_push($locations,$data["adminName"]);
            }
        }


        foreach ($locations as $location){
            $dataTemp = array();
            foreach ($dates as $date){
                $lastDate = "1900-01-01";
                $lastValue = 0;
                
                foreach ($datas as $data){
                    $year = intval(substr($data["date"],0,4));
                    if($date==$year && $location==$data["adminName"] && $data["value"]!=0 && $data["value"]!=""){
                        
                        if($data["date"]==$lastDate){
                            $lastValue = $lastValue + $data["value"];
                        }else{
                            if($data["date"] > $lastDate){
                                $lastDate = $data["date"];
                                $lastValue = $data["value"];
                            }
                        }
                    }
                }
                array_push($dataTemp,array($date,$lastValue));
            }
            array_push($trendsData,array("name"=>$location,"data"=>$dataTemp));
            
        }
        return $trendsData;
    }


    //TRENDS FOR EXCEL EXPORT
    function getTrendDataCaseloads($datas){
        $locations = array();
        $trendsData = array();
        $years = array();

        
        foreach ($datas as $data){
            $year = intval(substr($data["date"],0,4));

            if (!in_array($year, $years) && $year!="")
            {
                array_push($years,$year);
            }

            if (!in_array($data["admin0"]."*".$data["adminName"], $locations) && $data["adminName"]!="")
            {
                array_push($locations,$data["admin0"]."*".$data["adminName"]);
            }
        }

        
        foreach ($years as $year){
            foreach ($locations as $location){
                $lastDate = "1900-01-01";
                $locationArray = explode("*",$location);
                $adminName = $locationArray[1];
                $dataTemp = array();
                
                $pin = 0;
                $pt = 0;
                $pr = 0;

                foreach ($datas as $data){
                    $year_tmp = intval(substr($data["date"],0,4));
                    if($year==$year_tmp && $adminName==$data["adminName"]){
                        if($data["date"]==$lastDate){
                            if ($data["pin"]!=0 && $data["pin"]!="") {
                                $pin = $pin + $data["pin"];
                            }
                            if ($data["pt"]!=0 && $data["pt"]!="") {
                                $pt = $pt + $data["pt"];
                            }
                            if ($data["pr"]!=0 && $data["pr"]!="") {
                                $pr = $pr + $data["pr"];
                            }
                        }else{
                            if($data["date"] > $lastDate){
                                if ($data["pin"]!=0 && $data["pin"]!="") {
                                    $pin = $data["pin"];
                                    $lastDate = $data["date"];
                                }
                                if ($data["pt"]!=0 && $data["pt"]!="") {
                                    $pt = $data["pt"];
                                    $lastDate = $data["date"];
                                }
                                if ($data["pr"]!=0 && $data["pr"]!="") {
                                    $pr = $data["pr"];
                                    $lastDate = $data["date"];
                                }
                            }
                        }
                    }
                }
                array_push($trendsData,array("year"=>$year,"admin0"=>$locationArray[0],"adminName"=>$locationArray[1],"pin"=>$pin,"pt"=>$pt,"pr"=>$pr));
            }
        }
        return $trendsData;
    }

    function getTrendDataNutrition($datas){
        $locations = array();
        $trendsData = array();
        $years = array();

        
        foreach ($datas as $data){
            $year = intval(substr($data["date"],0,4));

            if (!in_array($year, $years) && $year!="")
            {
                array_push($years,$year);
            }

            if (!in_array($data["admin0"]."*".$data["adminName"], $locations) && $data["adminName"]!="")
            {
                array_push($locations,$data["admin0"]."*".$data["adminName"]);
            }
        }

        
        foreach ($years as $year){
            
            foreach ($locations as $location){
                $locationArray = explode("*",$location);
                $adminName = $locationArray[1];
                $dataTemp = array();
                $lastDate = "1900-01-01";
                $gam = 0;
                $mam = 0;
                $sam = 0;
                
                foreach ($datas as $data){
                    $year_tmp = intval(substr($data["date"],0,4));
                    if($year==$year_tmp && $adminName==$data["adminName"]){
                        if($data["date"]==$lastDate){
                            if ($data["gam"]!=0 && $data["gam"]!="") {
                                $gam = $gam + $data["gam"];
                            }
                            if ($data["mam"]!=0 && $data["mam"]!="") {
                                $mam = $mam + $data["mam"];
                            }
                            if ($data["sam"]!=0 && $data["sam"]!="") {
                                $sam = $sam + $data["sam"];
                            }
                        }else{
                            if($data["date"] > $lastDate){
                                if ($data["gam"]!=0 && $data["gam"]!="") {
                                    $gam = $data["gam"];
                                    $lastDate = $data["date"];
                                }
                                if ($data["mam"]!=0 && $data["mam"]!="") {
                                    $mam = $data["mam"];
                                    $lastDate = $data["date"];
                                }
                                if ($data["sam"]!=0 && $data["sam"]!="") {
                                    $sam = $data["sam"];
                                    $lastDate = $data["date"];
                                }
                            }
                        }
                    }
                }
                array_push($trendsData,array("year"=>$year,"admin0"=>$locationArray[0],"adminName"=>$locationArray[1],"gam"=>$gam,"mam"=>$mam,"sam"=>$sam));
            }
        }
        return $trendsData;
    }

    function getTrendDataCh($datas){
        $locations = array();
        $trendsData = array();
        $years = array();

        
        foreach ($datas as $data){
            $year = intval(substr($data["date"],0,4));

            if (!in_array($year, $years) && $year!="")
            {
                array_push($years,$year);
            }

            if (!in_array($data["admin0"]."*".$data["adminName"], $locations) && $data["adminName"]!="")
            {
                array_push($locations,$data["admin0"]."*".$data["adminName"]);
            }
        }

        
        foreach ($years as $year){
            foreach ($locations as $location){
                $locationArray = explode("*",$location);
                $adminName = $locationArray[1];
                $dataTemp = array();
                $lastDate = "1900-01-01";
                $ch1 = 0;
                $ch2 = 0;
                $ch3 = 0;
                $ch35 = 0;
                $ch4 = 0;
                $ch5 = 0;
                
                foreach ($datas as $data){
                    $year_tmp = intval(substr($data["date"],0,4));
                    if($year==$year_tmp && $adminName==$data["adminName"]){
                        if($data["date"]==$lastDate){
                            if ($data["ch1"]!=0 && $data["ch1"]!="") {
                                $ch1 = $ch1 + $data["ch1"];
                            }
                            if ($data["ch2"]!=0 && $data["ch2"]!="") {
                                $ch2 = $ch2 + $data["ch2"];
                            }
                            if ($data["ch3"]!=0 && $data["ch3"]!="") {
                                $ch3 = $ch3 + $data["ch3"];
                            }
                            if ($data["ch35"]!=0 && $data["ch35"]!="") {
                                $ch35 = $ch35 + $data["ch35"];
                            }
                            if ($data["ch4"]!=0 && $data["ch4"]!="") {
                                $ch4 = $ch4 + $data["ch4"];
                            }
                            if ($data["ch5"]!=0 && $data["ch5"]!="") {
                                $ch5 = $ch5 + $data["ch5"];
                            }
                        }else{
                            if($data["date"] > $lastDate){
                                
                                if ($data["ch1"]!=0 && $data["ch1"]!="") {
                                    $ch1 = $data["ch1"];
                                    $lastDate = $data["date"];
                                }
                                if ($data["ch2"]!=0 && $data["ch2"]!="") {
                                    $ch2 = $data["ch2"];
                                    $lastDate = $data["date"];
                                }
                                if ($data["ch3"]!=0 && $data["ch3"]!="") {
                                    $ch3 = $data["ch3"];
                                    $lastDate = $data["date"];
                                }
                                if ($data["ch35"]!=0 && $data["ch35"]!="") {
                                    $ch35 = $data["ch35"];
                                    $lastDate = $data["date"];
                                }
                                if ($data["ch4"]!=0 && $data["ch4"]!="") {
                                    $ch4 = $data["ch4"];
                                    $lastDate = $data["date"];
                                }
                                if ($data["ch5"]!=0 && $data["ch5"]!="") {
                                    $ch5 = $data["ch5"];
                                    $lastDate = $data["date"];
                                }
                            }
                        }
                    }
                }
                array_push($trendsData,array("year"=>$year,"admin0"=>$locationArray[0],"adminName"=>$locationArray[1],"ch1"=>$ch1,"ch2"=>$ch2,"ch3"=>$ch3,"ch35"=>$ch35,"ch4"=>$ch4,"ch5"=>$ch5));
            }
        }
        return $trendsData;
    }

    function getTrendDataDisplacement($datas){
        $locations = array();
        $trendsData = array();
        $years = array();

        
        foreach ($datas as $data){
            $year = intval(substr($data["date"],0,4));

            if (!in_array($year, $years) && $year!="")
            {
                array_push($years,$year);
            }

            if (!in_array($data["admin0"]."*".$data["adminName"], $locations) && $data["adminName"]!="")
            {
                array_push($locations,$data["admin0"]."*".$data["adminName"]);
            }
        }

        
        foreach ($years as $year){
            
            foreach ($locations as $location){
                $locationArray = explode("*",$location);
                $adminName = $locationArray[1];
                $dataTemp = array();
                $lastDateIdp = "1900-01-01";
                $lastDateRef = "1900-01-01";
                $lastDateRet = "1900-01-01";
                $idp = 0;
                $ref = 0;
                $ret = 0;
                
                foreach ($datas as $data){
                    $year_tmp = intval(substr($data["date"],0,4));
                    if($year==$year_tmp && $adminName==$data["adminName"]){

                        if($data["date"]==$lastDateIdp){
                            if ($data["idp"]!=0 && $data["idp"]!="") {
                                $idp = $idp + $data["idp"];
                            }
                        }else{
                            if($data["date"] > $lastDateIdp){
                                if ($data["idp"]!=0 && $data["idp"]!="") {
                                    $idp = $data["idp"];
                                    $lastDateIdp = $data["date"];
                                }
                            }
                        }

                        if($data["date"]==$lastDateRef){
                            if ($data["ref"]!=0 && $data["ref"]!="") {
                                $ref = $ref + $data["ref"];
                            }
                        }else{
                            if($data["date"] > $lastDateRef){
                                if ($data["ref"]!=0 && $data["ref"]!="") {
                                    $ref = $data["ref"];
                                    $lastDateRef = $data["date"];
                                }
                            }
                        }

                        if($data["date"]==$lastDateRet){
                            if ($data["ret"]!=0 && $data["ret"]!="") {
                                $ret = $ret + $data["ret"];
                            }
                        }else{
                            if($data["date"] > $lastDateRet){
                                if ($data["ret"]!=0 && $data["ret"]!="") {
                                    $ret = $data["ret"];
                                    $lastDateRet = $data["date"];
                                }
                            }
                        }
                    }
                }
                array_push($trendsData,array("year"=>$year,"admin0"=>$locationArray[0],"adminName"=>$locationArray[1],"idp"=>$idp,"ref"=>$ref,"ret"=>$ret));
            }
        }
        return $trendsData;
    }






    /*
    function getMapDataOld($datas,$dataFieldName){
        $mapData = array();
        foreach ($datas as $data){
            array_push($mapData,array("adminName"=>$data['adminName'], "value"=>$data[$dataFieldName]));
        }
        return $mapData;
    }*/

    function getMapData($datas,$dataFieldName){
        $mapData = array();
        foreach ($datas as $data){
            array_push($mapData,array(trim($data['adminName'])."*".trim($data['adminPcode']), $data[$dataFieldName]));
        }
        return $mapData;
    }



    function array_push_assoc($array, $key, $value){
        $array[$key] = $value;
        return $array;
    }
    
    $zone=$datas[0]["zone"];

    //caseloads
    $caseloads=$datas[0]["caseloads"];
    $KeyFigureCaseLoadsByAdmin = array();
    $TrendsCaseLoadsByAdmin = array();
    $adminName = "";

    $KeyFigureCaseLoads = array("pin"=>0,"pt"=>0,"pr"=>0);
    $trendCaseloads_PIN = array();
    $trendCaseloads_PIN_Raw = array();
    $trendCaseloads_Raw = array();
    $disclaimerCaseloads = array();
    $sourcesCaseloads = array();
    $disclaimerCaseload_text = "";

    
    $trendDisplacement_IDP_Raw = array();
    $trendDisplacement_Raw = array();
    $trendDisplacement = array();
    $disclaimerDisplacement_idp = array();
    $disclaimerDisplacement_ref = array();
    $disclaimerDisplacement_ret = array();
    $sourceDisplacements = array();
    $disclaimerDisplacement_text = "";
    

    $trendNutrition_SAM_Raw = array();
    $trendNutrition_Raw = array();
    $trendNutrition_SAM = array();
    $nutritionColumns = array();
    $disclaimerNutrition_SAM = array();
    $nutritionSource = array();
    $disclaimerNutrition_text = "";

    $trendCh_Current_Raw = array();
    $trendCh_Current2_Raw = array();
    $trendCh_Current = array();
    $trendCh_Current2 = array();
    $ch_CurrentColumns = array();
    $disclaimer_ch_mois_current = "";

    $trendCh_Projected_Raw = array();
    $trendCh_Projected2_Raw = array();
    $trendCh_Projected = array();
    $trendCh_Projected2 = array();
    $ch_ProjectedColumns = array();
    $disclaimer_ch_mois_projected = "";
    $disclaimer_ch_year = "";
    $disclaimerCh_text = "";

    $caseloadColumns = array();
    
    $displacementColumns = array();
    $pays = "";


    //liste localités
    foreach ($datas[0]["localites"] as $localite){
        $pays .= $localite->local_name.", ";
    }


    //caseloads
    foreach ($caseloads as $caseload){
        array_push($TrendsCaseLoadsByAdmin, array("adminName"=>$adminName,"date"=>$caseload->caseload_date, "pin"=>$caseload->caseload_people_in_need,  "pt"=>$caseload->caseload_people_targeted, "pr"=>$caseload->caseload_people_reached));
        

        //traitement key figure
        if ($adminLevel=="admin0") {
            $adminName = $caseload->caseload_country;
            $adminPcode = $caseload->admin0_pcode_iso3;
        } else {
            $adminName = $caseload->caseload_admin1_name;
            $adminPcode = $caseload->caseload_admin1_pcode;
        }
        
        //traitement trend
        if($adminPcode !=null){
            array_push($trendCaseloads_PIN_Raw, array("adminName"=>$adminName,"date"=>$caseload->caseload_date, "value"=>$caseload->caseload_people_in_need));
            array_push($trendCaseloads_Raw, array("admin0"=>$caseload->caseload_country,"adminName"=>$adminName,"date"=>$caseload->caseload_date, "pin"=>$caseload->caseload_people_in_need, "pt"=>$caseload->caseload_people_targeted, "pr"=>$caseload->caseload_people_reached));
            
            if (!in_array($adminName, $caseloadColumns) && $adminName!="")
            {
                array_push($caseloadColumns,$adminName);
            }
            //traitement trend fin
            
           
           if(array_key_exists($adminName,$KeyFigureCaseLoadsByAdmin)){
               if ($KeyFigureCaseLoadsByAdmin[$adminName]["date"]==$caseload->caseload_date) {
                   $KeyFigureCaseLoadsByAdmin[$adminName] = array( 
                       "adminName"=>$adminName,
                       "adminPcode"=>$adminPcode,
                       "admin0"=>$caseload->caseload_country,
                       "date"=>$caseload->caseload_date, 
                       "source"=>$caseload->caseload_source, 
                       "pin"=>($caseload->caseload_people_in_need + $KeyFigureCaseLoadsByAdmin[$adminName]["pin"]),  
                       "pt"=>($caseload->caseload_people_targeted + $KeyFigureCaseLoadsByAdmin[$adminName]["pt"]), 
                       "pr"=>($caseload->caseload_people_reached + $KeyFigureCaseLoadsByAdmin[$adminName]["pr"])
                   );
               }else{
                   if ($KeyFigureCaseLoadsByAdmin[$adminName]["date"]<$caseload->caseload_date) {
                       $KeyFigureCaseLoadsByAdmin[$adminName] = array( "adminName"=>$adminName,"adminPcode"=>$adminPcode,"admin0"=>$caseload->caseload_country,"date"=>$caseload->caseload_date, "source"=>$caseload->caseload_source,"pin"=>$caseload->caseload_people_in_need,  "pt"=>$caseload->caseload_people_targeted, "pr"=>$caseload->caseload_people_reached);
                   }
               }
           }else{
               $KeyFigureCaseLoadsByAdmin = array_push_assoc( $KeyFigureCaseLoadsByAdmin,  $adminName,  array("adminName"=>$adminName, "adminPcode"=>$adminPcode,"admin0"=>$caseload->caseload_country,"date"=>$caseload->caseload_date,"source"=>$caseload->caseload_source, "pin"=>$caseload->caseload_people_in_need, "pt"=>$caseload->caseload_people_targeted, "pr"=>$caseload->caseload_people_reached ));
           }
        }
    }


    $trendCaseloads = getTrendDataCaseloads($trendCaseloads_Raw);
    $trendCaseloads_PIN = getTrendData($trendCaseloads_PIN_Raw);
    $mapCaseloads_PIN = getMapData($KeyFigureCaseLoadsByAdmin,"pin");

    foreach ($KeyFigureCaseLoadsByAdmin as $KeyFigure){
        //récupération des chiffres clés
        $KeyFigureCaseLoads["pin"] = $KeyFigureCaseLoads["pin"] + $KeyFigure["pin"];
        $KeyFigureCaseLoads["pt"] = $KeyFigureCaseLoads["pt"] + $KeyFigure["pt"];
        $KeyFigureCaseLoads["pr"] = $KeyFigureCaseLoads["pr"] + $KeyFigure["pr"];

        //récupération des disclaimer
        $datetmp=date_create($KeyFigure["date"]);
        $moisAnneeTmp = date_format($datetmp,"F Y");

        $adminNameTmp = $KeyFigure["admin0"].",";

        if(count($disclaimerCaseloads)==0){
            array_push($disclaimerCaseloads, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
        }else{

            $exist = false;
            $index = null;
            for ($i=0; $i < count($disclaimerCaseloads) ; $i++) { 
                if ($moisAnneeTmp==$disclaimerCaseloads[$i]["Mois"])
                {
                    $exist = true;
                    $index = $i;
                    break;
                }
            }

            if($exist){
                $pos = str_contains($disclaimerCaseloads[$index]["adminName"],$adminNameTmp);
                if (str_contains($disclaimerCaseloads[$index]["adminName"],$adminNameTmp)==0) {
                    $adminNames = $disclaimerCaseloads[$index]["adminName"]." ".$adminNameTmp;
                    $disclaimerCaseloads[$index] = array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNames);
                }
            }else{
                array_push($disclaimerCaseloads, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
            }
        }

        
        //recuperation source
        if (!in_array($KeyFigure["source"], $sourcesCaseloads))
        {
            array_push($sourcesCaseloads, $KeyFigure["source"]);
        }
    }
 

    //cadre harmonisé
    $cadre_harmonises=$datas[0]["cadre_harmonises"];
    $KeyFigureCHByAdminCurrent = array();
    $KeyFigureCHByAdmin2Current = array();
    $KeyFigureCHByAdminProjeted = array();
    $KeyFigureCHByAdmin2Projeted = array();
    $adminName = "";
    $KeyFigureCHCurrent = array("month"=>"","ch_phase1"=>0,"ch_phase2"=>0,"ch_phase3"=>0,"ch_phase35"=>0,"ch_phase4"=>0,"ch_phase5"=>0);
    $KeyFigureCHProjeted = array("month"=>"","ch_phase1"=>0,"ch_phase2"=>0,"ch_phase3"=>0,"ch_phase35"=>0,"ch_phase4"=>0,"ch_phase5"=>0);

    foreach ($cadre_harmonises as $ch){
        if ($adminLevel=="admin0") {
            $adminName = $ch->ch_country;
            $adminPcode = $ch->ch_adm0_pcode_iso3;
        } else {
            $adminName = $ch->ch_admin1_name;
            $adminPcode = $ch->ch_admin1_pcode_iso3;
        }
        $admin2Pcode = $ch->ch_admin2_pcode_iso3;
        $admin2Name = $ch->ch_admin2_name;

        
        if($adminPcode !=null){
            if ($ch->ch_situation=="Current") {
                //traitement trend
                array_push($trendCh_Current_Raw, array("adminName"=>$adminName,"date"=>$ch->ch_date, "value"=>$ch->ch_phase35));
                array_push($trendCh_Current2_Raw, array("admin0"=>$ch->ch_country,"adminName"=>$adminName,"date"=>$ch->ch_date,"ch1"=>$ch->ch_phase1,"ch2"=>$ch->ch_phase2,"ch3"=>$ch->ch_phase3,"ch35"=>$ch->ch_phase35,"ch4"=>$ch->ch_phase4,"ch5"=>$ch->ch_phase5 ));
             
                
                if (!in_array($adminName, $ch_CurrentColumns) && $adminName!="")
                {
                    array_push($ch_CurrentColumns,$adminName);
                }
                //traitement trend fin 
    
                //for map
                if(array_key_exists($admin2Pcode,$KeyFigureCHByAdmin2Current)){
                    if ($KeyFigureCHByAdmin2Current[$admin2Pcode]["date"]==$ch->ch_date) {
                        $KeyFigureCHByAdmin2Current[$admin2Pcode] = array( 
                            "adminName"=>$admin2Name,
                            "adminPcode"=>$admin2Pcode,
                            "admin0"=>$ch->ch_country,
                            "month"=>$ch->ch_exercise_month, 
                            "year"=>$ch->ch_exercise_year, 
                            "date"=>$ch->ch_date,
                            "ch_ipc_level"=>$ch->ch_ipc_level,
                        );
                    }else{
                        if ($KeyFigureCHByAdmin2Current[$admin2Pcode]["date"]<$ch->ch_date) {
                            $KeyFigureCHByAdmin2Current[$admin2Pcode] = array("adminName"=>$admin2Name,"adminPcode"=>$admin2Pcode,"admin0"=>$ch->ch_country, "month"=>$ch->ch_exercise_month,"year"=>$ch->ch_exercise_year,"date"=>$ch->ch_date,"ch_ipc_level"=>$ch->ch_ipc_level);
                        }
                    }
                }else{
                    $KeyFigureCHByAdmin2Current = array_push_assoc($KeyFigureCHByAdmin2Current, $admin2Pcode, array("adminName"=>$admin2Name,"adminPcode"=>$admin2Pcode,"admin0"=>$ch->ch_country, "month"=>$ch->ch_exercise_month,"year"=>$ch->ch_exercise_year,"date"=>$ch->ch_date, "ch_ipc_level"=>$ch->ch_ipc_level));
                }
                //for map end

                //current
                if(array_key_exists($adminName,$KeyFigureCHByAdminCurrent)){
                    if ($KeyFigureCHByAdminCurrent[$adminName]["date"]==$ch->ch_date) {
                        $KeyFigureCHByAdminCurrent[$adminName] = array( 
                            "adminName"=>$adminName,
                            "adminPcode"=>$adminPcode,
                            "admin0"=>$ch->ch_country,
                            "month"=>$ch->ch_exercise_month, 
                            "year"=>$ch->ch_exercise_year, 
                            "date"=>$ch->ch_date, 
                            "ch_phase1"=>($ch->ch_phase1 + $KeyFigureCHByAdminCurrent[$adminName]["ch_phase1"]),  
                            "ch_phase2"=>($ch->ch_phase2 + $KeyFigureCHByAdminCurrent[$adminName]["ch_phase2"]), 
                            "ch_phase3"=>($ch->ch_phase3 + $KeyFigureCHByAdminCurrent[$adminName]["ch_phase3"]),
                            "ch_phase35"=>($ch->ch_phase35 + $KeyFigureCHByAdminCurrent[$adminName]["ch_phase35"]),
                            "ch_phase4"=>($ch->ch_phase4 + $KeyFigureCHByAdminCurrent[$adminName]["ch_phase4"]),
                            "ch_phase5"=>($ch->ch_phase5 + $KeyFigureCHByAdminCurrent[$adminName]["ch_phase5"]),
                        );
                    }else{
                        if ($KeyFigureCHByAdminCurrent[$adminName]["date"]<$ch->ch_date) {
                            $KeyFigureCHByAdminCurrent[$adminName] = array("adminName"=>$adminName,"adminPcode"=>$adminPcode,"admin0"=>$ch->ch_country, "month"=>$ch->ch_exercise_month,"year"=>$ch->ch_exercise_year,"date"=>$ch->ch_date, "ch_phase1"=>$ch->ch_phase1, "ch_phase2"=>$ch->ch_phase2, "ch_phase3"=>$ch->ch_phase3,"ch_phase35"=>$ch->ch_phase35,"ch_phase4"=>$ch->ch_phase4,"ch_phase5"=>$ch->ch_phase5);
                        }
                    }
                }else{
                    $KeyFigureCHByAdminCurrent = array_push_assoc($KeyFigureCHByAdminCurrent, $adminName, array("adminName"=>$adminName,"adminPcode"=>$adminPcode,"admin0"=>$ch->ch_country, "month"=>$ch->ch_exercise_month,"year"=>$ch->ch_exercise_year,"date"=>$ch->ch_date,  "ch_phase1"=>$ch->ch_phase1,  "ch_phase2"=>$ch->ch_phase2, "ch_phase3"=>$ch->ch_phase3, "ch_phase35"=>$ch->ch_phase35, "ch_phase4"=>$ch->ch_phase4, "ch_phase5"=>$ch->ch_phase5));
                }
            } else {
                //projected
    
                //traitement trend
                array_push($trendCh_Projected_Raw, array("adminName"=>$adminName,"date"=>$ch->ch_date, "value"=>$ch->ch_phase35));
                array_push($trendCh_Projected2_Raw, array("admin0"=>$ch->ch_country,"adminName"=>$adminName,"date"=>$ch->ch_date,"ch1"=>$ch->ch_phase1,"ch2"=>$ch->ch_phase2,"ch3"=>$ch->ch_phase3,"ch35"=>$ch->ch_phase35,"ch4"=>$ch->ch_phase4,"ch5"=>$ch->ch_phase5 ));
             
                
                if (!in_array($adminName, $ch_ProjectedColumns) && $adminName!="")
                {
                    array_push($ch_ProjectedColumns,$adminName);
                }
                //traitement trend fin
    
                //for map
                if(array_key_exists($admin2Pcode,$KeyFigureCHByAdmin2Projeted)){
                    if ($KeyFigureCHByAdmin2Projeted[$admin2Pcode]["date"]==$ch->ch_date) {
                        $KeyFigureCHByAdmin2Projeted[$admin2Pcode] = array( 
                            "adminName"=>$admin2Name,
                            "adminPcode"=>$admin2Pcode,
                            "admin0"=>$ch->ch_country,
                            "month"=>$ch->ch_exercise_month, 
                            "year"=>$ch->ch_exercise_year, 
                            "date"=>$ch->ch_date,
                            "ch_ipc_level"=>$ch->ch_ipc_level,
                        );
                    }else{
                        if ($KeyFigureCHByAdmin2Projeted[$admin2Pcode]["date"]<$ch->ch_date) {
                            $KeyFigureCHByAdmin2Projeted[$admin2Pcode] = array("adminName"=>$admin2Name,"adminPcode"=>$admin2Pcode,"admin0"=>$ch->ch_country, "month"=>$ch->ch_exercise_month,"year"=>$ch->ch_exercise_year,"date"=>$ch->ch_date,"ch_ipc_level"=>$ch->ch_ipc_level);
                        }
                    }
                }else{
                    $KeyFigureCHByAdmin2Projeted = array_push_assoc($KeyFigureCHByAdmin2Projeted, $admin2Pcode, array("adminName"=>$admin2Name,"adminPcode"=>$admin2Pcode,"admin0"=>$ch->ch_country, "month"=>$ch->ch_exercise_month,"year"=>$ch->ch_exercise_year,"date"=>$ch->ch_date, "ch_ipc_level"=>$ch->ch_ipc_level));
                }
                //for map end


                if(array_key_exists($adminName,$KeyFigureCHByAdminProjeted)){
                    if ($KeyFigureCHByAdminProjeted[$adminName]["date"]==$ch->ch_date) {
                        $KeyFigureCHByAdminProjeted[$adminName] = array( 
                            "adminName"=>$adminName,
                            "adminPcode"=>$adminPcode,
                            "admin0"=>$ch->ch_country,
                            "month"=>$ch->ch_exercise_month, 
                            "year"=>$ch->ch_exercise_year,
                            "date"=>$ch->ch_date, 
                            "ch_phase1"=>($ch->ch_phase1 + $KeyFigureCHByAdminProjeted[$adminName]["ch_phase1"]),  
                            "ch_phase2"=>($ch->ch_phase2 + $KeyFigureCHByAdminProjeted[$adminName]["ch_phase2"]), 
                            "ch_phase3"=>($ch->ch_phase3 + $KeyFigureCHByAdminProjeted[$adminName]["ch_phase3"]),
                            "ch_phase35"=>($ch->ch_phase35 + $KeyFigureCHByAdminProjeted[$adminName]["ch_phase35"]),
                            "ch_phase4"=>($ch->ch_phase4 + $KeyFigureCHByAdminProjeted[$adminName]["ch_phase4"]),
                            "ch_phase5"=>($ch->ch_phase5 + $KeyFigureCHByAdminProjeted[$adminName]["ch_phase5"]),
                        );
                    }else{
                        if ($KeyFigureCHByAdminProjeted[$adminName]["date"]<$ch->ch_date) {
                            $KeyFigureCHByAdminProjeted[$adminName] = array("adminName"=>$adminName,"adminPcode"=>$adminPcode,"admin0"=>$ch->ch_country, "month"=>$ch->ch_exercise_month,"year"=>$ch->ch_exercise_year,"date"=>$ch->ch_date, "ch_phase1"=>$ch->ch_phase1, "ch_phase2"=>$ch->ch_phase2, "ch_phase3"=>$ch->ch_phase3,"ch_phase35"=>$ch->ch_phase35,"ch_phase4"=>$ch->ch_phase4,"ch_phase5"=>$ch->ch_phase5);
                        }
                    }
                }else{
                    $KeyFigureCHByAdminProjeted = array_push_assoc($KeyFigureCHByAdminProjeted, $adminName, array("adminName"=>$adminName,"adminPcode"=>$adminPcode, "admin0"=>$ch->ch_country,"month"=>$ch->ch_exercise_month,"year"=>$ch->ch_exercise_year,"date"=>$ch->ch_date,  "ch_phase1"=>$ch->ch_phase1,  "ch_phase2"=>$ch->ch_phase2, "ch_phase3"=>$ch->ch_phase3, "ch_phase35"=>$ch->ch_phase35, "ch_phase4"=>$ch->ch_phase4, "ch_phase5"=>$ch->ch_phase5));
                }
            }
        }
        
    }

    $trendCh_Current = getTrendData($trendCh_Current_Raw);
    $trendCh_Current2 = getTrendDataCh($trendCh_Current2_Raw);
    $trendCh_Projected = getTrendData($trendCh_Projected_Raw);
    $trendCh_Projected2 = getTrendDataCh($trendCh_Projected2_Raw);

    $mapCH_current = getMapData($KeyFigureCHByAdmin2Current,"ch_ipc_level");
    $mapCH_projected = getMapData($KeyFigureCHByAdmin2Projeted,"ch_ipc_level");
	
    foreach ($KeyFigureCHByAdminCurrent as $KeyFigure){
        $KeyFigureCHCurrent["ch_phase1"] = $KeyFigureCHCurrent["ch_phase1"] + $KeyFigure["ch_phase1"];
        $KeyFigureCHCurrent["ch_phase2"] = $KeyFigureCHCurrent["ch_phase2"] + $KeyFigure["ch_phase2"];
        $KeyFigureCHCurrent["ch_phase3"] = $KeyFigureCHCurrent["ch_phase3"] + $KeyFigure["ch_phase3"];
        $KeyFigureCHCurrent["ch_phase35"] = $KeyFigureCHCurrent["ch_phase35"] + $KeyFigure["ch_phase35"];
        $KeyFigureCHCurrent["ch_phase4"] = $KeyFigureCHCurrent["ch_phase4"] + $KeyFigure["ch_phase4"];
        $KeyFigureCHCurrent["ch_phase5"] = $KeyFigureCHCurrent["ch_phase5"] + $KeyFigure["ch_phase5"];

        $disclaimer_ch_mois_current = $KeyFigure["month"];
        $disclaimer_ch_year = $KeyFigure["year"];
    }

    foreach ($KeyFigureCHByAdminProjeted as $KeyFigure){
        $KeyFigureCHProjeted["ch_phase1"] = $KeyFigureCHProjeted["ch_phase1"] + $KeyFigure["ch_phase1"];
        $KeyFigureCHProjeted["ch_phase2"] = $KeyFigureCHProjeted["ch_phase2"] + $KeyFigure["ch_phase2"];
        $KeyFigureCHProjeted["ch_phase3"] = $KeyFigureCHProjeted["ch_phase3"] + $KeyFigure["ch_phase3"];
        $KeyFigureCHProjeted["ch_phase35"] = $KeyFigureCHProjeted["ch_phase35"] + $KeyFigure["ch_phase35"];
        $KeyFigureCHProjeted["ch_phase4"] = $KeyFigureCHProjeted["ch_phase4"] + $KeyFigure["ch_phase4"];
        $KeyFigureCHProjeted["ch_phase5"] = $KeyFigureCHProjeted["ch_phase5"] + $KeyFigure["ch_phase5"];

        $disclaimer_ch_mois_projected = $KeyFigure["month"];
    }



    //nutrition
    $nutritions=$datas[0]["nutrition"];
  
    
    $KeyFigurenutritionsByAdmin = array();
    $adminName = "";
    $KeyFigurenutritions = array("sam"=>0,"mam"=>0,"gam"=>0);

    foreach ($nutritions as $nutrition){
        if ($adminLevel=="admin0") {
            $adminName = $nutrition->nut_country;
            $adminPcode = $nutrition->nut_admin0_pcode;
        }else{
            $adminName = $nutrition->nut_admin1;
            $adminPcode = $nutrition->nut_admin1_pcode;
        }

        //traitement trend
        if($adminPcode !=null){
            array_push($trendNutrition_SAM_Raw, array("adminName"=>$adminName,"date"=>$nutrition->nut_date, "value"=>$nutrition->nut_sam));
            array_push($trendNutrition_Raw, array("admin0"=>$nutrition->nut_country,"adminName"=>$adminName,"date"=>$nutrition->nut_date, "gam"=>$nutrition->nut_gam, "mam"=>$nutrition->nut_sam, "sam"=>$nutrition->nut_sam));
              
            if (!in_array($adminName, $nutritionColumns) && $adminName!="")
            {
                array_push($nutritionColumns,$adminName);
            }
            
            if(array_key_exists($adminName,$KeyFigurenutritionsByAdmin)){
                if ($KeyFigurenutritionsByAdmin[$adminName]["date"]==$nutrition->nut_date) {
                    $KeyFigurenutritionsByAdmin[$adminName] = array( 
                        "adminName"=>$adminName,
                        "adminPcode"=>$adminPcode,
                        "admin0"=>$nutrition->nut_country,
                        "date"=>$nutrition->nut_date, 
                        "source"=>$nutrition->nut_source, 
                        "sam"=>($nutrition->nut_sam + $KeyFigurenutritionsByAdmin[$adminName]["sam"]),  
                        "mam"=>($nutrition->nut_gam + $KeyFigurenutritionsByAdmin[$adminName]["mam"]), 
                        "gam"=>($nutrition->nut_mam + $KeyFigurenutritionsByAdmin[$adminName]["gam"])
                    );
                }else{
                    if ($KeyFigurenutritionsByAdmin[$adminName]["date"]<$nutrition->nut_date) {
                        $KeyFigurenutritionsByAdmin[$adminName] = array( "adminName"=>$adminName,"adminPcode"=>$adminPcode,"admin0"=>$nutrition->nut_country, "date"=>$nutrition->nut_date,"source"=>$nutrition->nut_source, "sam"=>$nutrition->nut_sam,  "mam"=>$nutrition->nut_gam, "gam"=>$nutrition->nut_mam);
                    }
                }
            }else{
                $KeyFigurenutritionsByAdmin = array_push_assoc( $KeyFigurenutritionsByAdmin,  $adminName,  array("adminName"=>$adminName,"adminPcode"=>$adminPcode,"admin0"=>$nutrition->nut_country, "date"=>$nutrition->nut_date, "source"=>$nutrition->nut_source,"sam"=>$nutrition->nut_sam, "mam"=>$nutrition->nut_gam, "gam"=>$nutrition->nut_mam ));
            }
        }
    }

    foreach ($KeyFigurenutritionsByAdmin as $KeyFigure){
        $KeyFigurenutritions["sam"] = $KeyFigurenutritions["sam"] + $KeyFigure["sam"];
        $KeyFigurenutritions["mam"] = $KeyFigurenutritions["mam"] + $KeyFigure["mam"];
        $KeyFigurenutritions["gam"] = $KeyFigurenutritions["gam"] + $KeyFigure["gam"];

        //récupération des disclaimer
        $datetmp=date_create($KeyFigure["date"]);
        $moisAnneeTmp = date_format($datetmp,"F Y");

        $adminNameTmp = $KeyFigure["admin0"].",";
        
        if(count($disclaimerNutrition_SAM)==0){
            array_push($disclaimerNutrition_SAM, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
        }else{
            $exist = false;
            $index = null;
            for ($i=0; $i < count($disclaimerNutrition_SAM) ; $i++) { 
                if ($moisAnneeTmp==$disclaimerNutrition_SAM[$i]["Mois"])
                {
                    $exist = true;
                    $index = $i;
                    break;
                }
            }

            if($exist){
                if (str_contains($disclaimerNutrition_SAM[$index]["adminName"],$adminNameTmp)==0) {
                    $adminNames = $disclaimerNutrition_SAM[$index]["adminName"]." ".$adminNameTmp;
                    $disclaimerNutrition_SAM[$i] = array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNames);
                }
            }else{
                array_push($disclaimerNutrition_SAM, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
            }
        }

        //recuperation source
        if (!in_array($KeyFigure["source"], $nutritionSource))
        {
            array_push($nutritionSource, $KeyFigure["source"]);
        }
    }


    $trendNutrition = getTrendDataNutrition($trendNutrition_Raw);
    $trendNutrition_SAM = getTrendData($trendNutrition_SAM_Raw);
    $mapNutrition_SAM = getMapData($KeyFigurenutritionsByAdmin,"sam");


    //displacements
    $displacements=$datas[0]["displacements"];

    $KeyFigureDisplacementsByAdmin = array();
    $adminName = "";
    $adminPcode = "";
    $KeyFigureDisplacements = array("idp"=>0, "refugees"=>0, "returnees"=>0);

    foreach ($displacements as $displacement){
        if ($adminLevel=="admin0") {
            $adminName = $displacement->dis_country;
            $adminPcode = $displacement->dis_admin0_pcode;
        } else {
            $adminName = $displacement->dis_admin1_name;
            $adminPcode = $displacement->dis_admin1_pcode;
        }

        if($adminPcode !=null){
            if(array_key_exists($adminName,$KeyFigureDisplacementsByAdmin)){
                $temp = $KeyFigureDisplacementsByAdmin[$adminName];
    
                switch ($displacement->dis_type) {
                    case 'IDP':
                         //traitement trend
                        array_push($trendDisplacement_IDP_Raw, array("adminName"=>$adminName,"date"=>$displacement->dis_date, "value"=>$displacement->dis_value));
                        array_push($trendDisplacement_Raw, array("admin0"=>$displacement->dis_country,"adminName"=>$adminName,"date"=>$displacement->dis_date, "idp"=>$displacement->dis_value, "ref"=>0, "ret"=>0));
            
                        if (!in_array($adminName, $displacementColumns) && $adminName!="")
                        {
                            array_push($displacementColumns,$adminName);
                        }
    
                        if ($KeyFigureDisplacementsByAdmin[$adminName]["idp_date"]==$displacement->dis_date) {
                            $KeyFigureDisplacementsByAdmin[$adminName] = array(
                                "adminName"=>$adminName,
                                "adminPcode"=>$adminPcode,
                                "admin0"=>$displacement->dis_country,
                                "idp"=>($displacement->dis_value + $KeyFigureDisplacementsByAdmin[$adminName]["idp"]),
                                "idp_date"=>$displacement->dis_date,  
                                "idp_source"=>$displacement->dis_source,
                                "refugees"=>$temp["refugees"], 
                                "refugees_date"=>$temp["refugees_date"],
                                "refugees_source"=>$temp["refugees_source"],
                                "returnees"=>$temp["returnees"],
                                "returnees_date"=>$temp["returnees_date"],
                                "returnees_source"=>$temp["returnees_source"],

                            );
                        }else{
                            if ($KeyFigureDisplacementsByAdmin[$adminName]["idp_date"]<$displacement->dis_date) {
                                $KeyFigureDisplacementsByAdmin[$adminName] = array(
                                    "adminName"=>$adminName,
                                    "adminPcode"=>$adminPcode,
                                    "admin0"=>$displacement->dis_country,
                                    "idp"=>$displacement->dis_value,
                                    "idp_date"=>$displacement->dis_date,  
                                    "idp_source"=>$displacement->dis_source,
                                    "refugees"=>$temp["refugees"], 
                                    "refugees_date"=>$temp["refugees_date"],
                                    "refugees_source"=>$temp["refugees_source"],
                                    "returnees"=>$temp["returnees"],
                                    "returnees_date"=>$temp["returnees_date"],
                                    "returnees_source"=>$temp["returnees_source"],

                                );
                            }
                        }
                        break;
                    case 'Refugee':
                        array_push($trendDisplacement_Raw, array("admin0"=>$displacement->dis_country,"adminName"=>$adminName,"date"=>$displacement->dis_date, "idp"=>0, "ref"=>$displacement->dis_value, "ret"=>0));
                        if ($KeyFigureDisplacementsByAdmin[$adminName]["refugees_date"]==$displacement->dis_date) {
                            $KeyFigureDisplacementsByAdmin[$adminName] = array(
                                "adminName"=>$adminName,
                                "adminPcode"=>$adminPcode,
                                "admin0"=>$displacement->dis_country,
                                "idp"=>$temp["idp"], 
                                "idp_date"=>$temp["idp_date"],
                                "idp_source"=>$temp["idp_source"],
                                "refugees"=>($displacement->dis_value + $KeyFigureDisplacementsByAdmin[$adminName]["refugees"]),
                                "refugees_date"=>$displacement->dis_date,  
                                "refugees_source"=>$displacement->dis_source,
                                "returnees"=>$temp["returnees"],
                                "returnees_date"=>$temp["returnees_date"],
                                "returnees_source"=>$temp["returnees_source"],
                            );
                        }else{
                            if ($KeyFigureDisplacementsByAdmin[$adminName]["refugees_date"]<$displacement->dis_date) {
                                $KeyFigureDisplacementsByAdmin[$adminName] = array(
                                    "adminName"=>$adminName,
                                    "adminPcode"=>$adminPcode,
                                    "admin0"=>$displacement->dis_country,
                                    "idp"=>$temp["idp"], 
                                    "idp_date"=>$temp["idp_date"],
                                    "idp_source"=>$temp["idp_source"],
                                    "refugees"=>$displacement->dis_value,
                                    "refugees_date"=>$displacement->dis_date,  
                                    "refugees_source"=>$displacement->dis_source,
                                    "returnees"=>$temp["returnees"],
                                    "returnees_date"=>$temp["returnees_date"],
                                    "returnees_source"=>$temp["returnees_source"],
                                );
                            }
                        }
                        break;
                    case 'Returnee':
                        array_push($trendDisplacement_Raw, array("admin0"=>$displacement->dis_country,"adminName"=>$adminName,"date"=>$displacement->dis_date, "idp"=>0, "ref"=>0, "ret"=>$displacement->dis_value));
                        if ($KeyFigureDisplacementsByAdmin[$adminName]["returnees_date"]==$displacement->dis_date) {
                            $KeyFigureDisplacementsByAdmin[$adminName] = array(
                                "adminName"=>$adminName,
                                "adminPcode"=>$adminPcode,
                                "admin0"=>$displacement->dis_country,
                                "idp"=>$temp["idp"], 
                                "idp_date"=>$temp["idp_date"],
                                "idp_source"=>$temp["idp_source"],
                                "refugees"=>$temp["refugees"],
                                "refugees_date"=>$temp["refugees_date"],
                                "refugees_source"=>$temp["refugees_source"],
                                "returnees"=>($displacement->dis_value + $KeyFigureDisplacementsByAdmin[$adminName]["returnees"]),
                                "returnees_date"=>$displacement->dis_date,
                                "returnees_source"=>$displacement->dis_source,
                            );
                        }else{
                            if ($KeyFigureDisplacementsByAdmin[$adminName]["returnees_date"]<$displacement->dis_date) {
                                $KeyFigureDisplacementsByAdmin[$adminName] = array(
                                    "adminName"=>$adminName,
                                    "adminPcode"=>$adminPcode,
                                    "admin0"=>$displacement->dis_country,
                                    "idp"=>$temp["idp"],
                                    "idp_date"=>$temp["idp_date"], 
                                    "idp_source"=>$temp["idp_source"],
                                    "refugees"=>$temp["refugees"], 
                                    "refugees_date"=>$temp["refugees_date"],
                                    "refugees_source"=>$temp["refugees_source"],
                                    "returnees"=>$displacement->dis_value,
                                    "returnees_date"=>$displacement->dis_date,
                                    "returnees_source"=>$displacement->dis_source,
                                );
                            }
                        }
                        break;
                }
            }else{
                switch ($displacement->dis_type) {
                    case 'IDP':
                        $KeyFigureDisplacementsByAdmin[$adminName] = array(
                                "adminName"=>$adminName,
                                "adminPcode"=>$adminPcode,
                                "admin0"=>$displacement->dis_country,
                                "idp"=>$displacement->dis_value,
                                "idp_date"=>$displacement->dis_date,  
                                "idp_source"=>$displacement->dis_source,
                                "refugees"=>0, 
                                "refugees_date"=>"",
                                "refugees_source"=>"",
                                "returnees"=>0,
                                "returnees_date"=>"",
                                "returnees_source"=>"",
                            );
                        break;
                    case 'Refugee':
                        $KeyFigureDisplacementsByAdmin[$adminName] = array(
                                "adminName"=>$adminName,
                                "adminPcode"=>$adminPcode,
                                "admin0"=>$displacement->dis_country,
                                "idp"=>0, 
                                "idp_date"=>"",
                                "idp_source"=>"",
                                "refugees"=>$displacement->dis_value,
                                "refugees_date"=>$displacement->dis_date,  
                                "refugees_source"=>$displacement->dis_source,
                                "returnees"=>0,
                                "returnees_date"=>"",
                                "returnees_source"=>"",
                            );
                        break;
                    case 'Returnee':
                        $KeyFigureDisplacementsByAdmin[$adminName] = array(
                                "adminName"=>$adminName,
                                "adminPcode"=>$adminPcode,
                                "admin0"=>$displacement->dis_country,
                                "idp"=>0,
                                "idp_date"=>"", 
                                "idp_source"=>"",
                                "refugees"=>0, 
                                "refugees_date"=>"",
                                "refugees_source"=>"",
                                "returnees"=>$displacement->dis_value,
                                "returnees_date"=>$displacement->dis_date,
                                "returnees_source"=>$displacement->dis_source,
                            );
                        break;
                }
            }
        }
    }

    foreach ($KeyFigureDisplacementsByAdmin as $KeyFigure){
        $KeyFigureDisplacements["idp"] = $KeyFigureDisplacements["idp"] + $KeyFigure["idp"];
        $KeyFigureDisplacements["refugees"] = $KeyFigureDisplacements["refugees"] + $KeyFigure["refugees"];
        $KeyFigureDisplacements["returnees"] = $KeyFigureDisplacements["returnees"] + $KeyFigure["returnees"];


        //Récupération des sources
        if (!in_array($KeyFigure["idp_source"], $sourceDisplacements))
        {
            array_push($sourceDisplacements, $KeyFigure["idp_source"]);
        }

        if (!in_array($KeyFigure["refugees_source"], $sourceDisplacements))
        {
            array_push($sourceDisplacements, $KeyFigure["refugees_source"]);
        }

        if (!in_array($KeyFigure["returnees_source"], $sourceDisplacements))
        {
            array_push($sourceDisplacements, $KeyFigure["returnees_source"]);
        }

        //récupération des disclaimer idps
        $datetmp=date_create($KeyFigure["idp_date"]);
        $moisAnneeTmp = date_format($datetmp,"F Y");
        $adminNameTmp = $KeyFigure["admin0"].",";
        
        if(count($disclaimerDisplacement_idp)==0){
            array_push($disclaimerDisplacement_idp, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
        }else{
            $exist = false;
            $index = null;
            for ($i=0; $i < count($disclaimerDisplacement_idp) ; $i++) { 
                if ($moisAnneeTmp==$disclaimerDisplacement_idp[$i]["Mois"])
                {
                    $exist = true;
                    $index = $i;
                    break;
                }
            }

            if($exist){
                if (str_contains($disclaimerDisplacement_idp[$index]["adminName"],$adminNameTmp)==0) {
                    $adminNames = $disclaimerDisplacement_idp[$index]["adminName"]." ".$adminNameTmp;
                    $disclaimerDisplacement_idp[$index] = array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNames);
                }
            }else{
                array_push($disclaimerDisplacement_idp, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
            }
        }

        //récupération des disclaimer refugees
        $datetmp=date_create($KeyFigure["refugees_date"]);
        $moisAnneeTmp = date_format($datetmp,"F Y");
        $adminNameTmp = $KeyFigure["admin0"].",";

        if(count($disclaimerDisplacement_ref)==0){
            array_push($disclaimerDisplacement_ref, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
        }else{
            $exist = false;
            $index = null;
            for ($i=0; $i < count($disclaimerDisplacement_ref) ; $i++) { 
                if ($moisAnneeTmp==$disclaimerDisplacement_ref[$i]["Mois"])
                {
                    $exist = true;
                    $index = $i;
                    break;
                }
            }

            if($exist){
                if (str_contains($disclaimerDisplacement_ref[$index]["adminName"],$adminNameTmp)==0) {
                    $adminNames = $disclaimerDisplacement_ref[$index]["adminName"]." ".$adminNameTmp;
                    $disclaimerDisplacement_ref[$index] = array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNames);
                }
            }else{
                array_push($disclaimerDisplacement_ref, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
            }
        }

        //récupération des disclaimer returnees
        $datetmp=date_create($KeyFigure["returnees_date"]);
        $moisAnneeTmp = date_format($datetmp,"F Y");
        $adminNameTmp = $KeyFigure["admin0"].",";

        if(count($disclaimerDisplacement_ret)==0){
            array_push($disclaimerDisplacement_ret, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
        }else{
            $exist = false;
            $index = null;

            for ($i=0; $i < count($disclaimerDisplacement_ret) ; $i++) { 
                if ($moisAnneeTmp==$disclaimerDisplacement_ret[$i]["Mois"])
                {
                    $exist = true;
                    $index = $i;
                    break;
                }
            }

            if($exist){
                if (str_contains($disclaimerDisplacement_ret[$index]["adminName"],$adminNameTmp)==0) {
                    $adminNames = $disclaimerDisplacement_ret[$index]["adminName"].", ".$adminNameTmp;
                    $disclaimerDisplacement_ret[$index] = array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNames);
                }
                
            }else{
                array_push($disclaimerDisplacement_ret, array("Mois"=>$moisAnneeTmp,"adminName"=>$adminNameTmp));
            }
        }
    }


    $trendDisplacement_IDP = getTrendData($trendDisplacement_IDP_Raw);
    $trendDisplacement = getTrendDataDisplacement($trendDisplacement_Raw);
    $mapDisplacement_IDP = getMapData($KeyFigureDisplacementsByAdmin,"idp");



?>
<div class="col-12 loading" id="loading" style="text-align:center;">
    <img src="{{asset('images/loading.gif')}}" alt="..." class="img-fluid" style="height:50px;">

</div>

<div class='col-12 pt-3' >
    <div class="row mb-3">
        <div class="col">
            @if ($zone->zone_code== "WCA")
                Datas for the <strong>{{$pays}}</strong> <em>by {{$adminLevel}} from {{$periodFrom}} to {{$periodTo}}</em><br>
            @else
                Datas for the <strong>{{$zone->zone_name}}</strong>, <em>by {{$adminLevel}} from {{$periodFrom}} to {{$periodTo}}</em><br>
            @endif
        </div>
        <div class="col d-flex justify-content-center" id='buttonOk'>
            @if ($adminLevel == "admin0")
                <a href="/filterV2/{{$category}}/{{$items}}/{{$periodFrom}}/{{$periodTo}}/admin0" class="btn text-white" style="background-color:#E56A54;border:none;" id="buttonDone">Admin 0</a>
                <a href="/filterV2/{{$category}}/{{$items}}/{{$periodFrom}}/{{$periodTo}}/admin1" class="btn text-black ms-2" style="background-color:none;border:none;" id="buttonDone">Admin 1</a>
            @else
                <a href="/filterV2/{{$category}}/{{$items}}/{{$periodFrom}}/{{$periodTo}}/admin0" class="btn text-black" style="background-color:none;border:none;" id="buttonDone">Admin 0</a>
                <a href="/filterV2/{{$category}}/{{$items}}/{{$periodFrom}}/{{$periodTo}}/admin1" class="btn text-white ms-2" style="background-color:#E56A54;border:none;" id="buttonDone">Admin 1</a>
            @endif
        </div>
        <div class="col text-end">
            <img src="{{asset('images/powerpoint.svg')}}" class="exportImage me-3" onclick="ExportPowerPoint()" style="height:40px;"  alt="Exporter vers Power Point"/> 
            <img src="{{asset('images/excel.svg')}}" class="exportImage me-3" onclick="ExportExcel()" style="height:40px;"  alt="Exporter vers Excel"/> 
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-3 keyFigure-card cards-selected rounded me-1 ms-1" id="keyFigure-caseloads" onclick="showData('caseloads')">
            <p>Caseloads</p>
            <div class="row">
                <div class='col' >
                    <div>
                        <img src="{{asset('images/People-in-need.svg')}}" style="height:26px;"  alt="..."/> 
                        <span class="keyfigure">{{convertToUnit($KeyFigureCaseLoads["pin"],1)}}</span>
                    </div>
                    <p class="labelkeyfigure lh-sm mt-2">People in need</p>
                </div>
                <div class='col'>
                    <div>
                        <img src="{{asset('images/People-targeted.svg')}}" style="height:26px;"  alt="..."/> 
                        <span class="keyfigure">{{convertToUnit($KeyFigureCaseLoads["pt"],1)}}</span>
                    </div>
                    <p class="labelkeyfigure lh-sm mt-2">People targeted</p>
                </div>
                <div class='col'>
                    <div>
                        <img src="{{asset('images/Person-2.svg')}}" style="height:26px;"  alt="..."/> 
                        <span class="keyfigure">{{convertToUnit($KeyFigureCaseLoads["pr"],1)}}</span>
                    </div>
                    <p class="labelkeyfigure lh-sm mt-2">People reached</p>
                </div>
            </div>
            <div class="row">
                <div class='col disclaimer'>
                    <?php
                        $disclaimerCaseload_text = "People in need, Targeted and Reached as of ";
                    ?>
                    <strong>People in need, Targeted and Reached</strong> as of 
                    @foreach ($disclaimerCaseloads as $disclaimer)
                        {{$disclaimer["Mois"]}} ({{$disclaimer["adminName"]}})
                        <?php
                            $disclaimerCaseload_text .= $disclaimer["Mois"]." ".$disclaimer["adminName"];
                        ?>
                    @endforeach
                    <br/><strong>Sources</strong> : 
                    <?php
                        $disclaimerCaseload_text .= " | Sources : ";
                    ?>
                    @foreach ($sourcesCaseloads as $source)
                        <?php
                            $disclaimerCaseload_text .= $source.", ";
                        ?>
                        {{$source}},
                    @endforeach 
                </div>
            </div>
        </div>
        <div class="col-3 keyFigure-card cards rounded me-1 ms-1"  id="keyFigure-disp" onclick="showData('disp')">
            <p>Displacements</p>
            <div class="row">
                <div class='col'>
                    <div>
                        <img src="{{asset('images/Internally-displaced.svg')}}" style="height:26px;"  alt="..."/> 
                        <span class="keyfigure">{{convertToUnit($KeyFigureDisplacements["idp"],1)}}</span>
                    </div>
                    <p class="labelkeyfigure lh-sm mt-2">Internally displaced persons</p>
                </div>
                <div class='col'>
                    <div>
                        <img src="{{asset('images/Refugee.svg')}}" style="height:26px;"  alt="..."/> 
                        <span class="keyfigure">{{convertToUnit($KeyFigureDisplacements["refugees"],1)}}</span>
                    </div>
                    <p class="labelkeyfigure lh-sm mt-2">Refugees</p>
                </div>
                <div class='col'>
                    <div>
                        <img src="{{asset('images/Population-return.svg')}}" style="height:26px;"  alt="..."/> 
                        <span class="keyfigure">{{convertToUnit($KeyFigureDisplacements["returnees"],1)}}</span>
                    </div>
                    <p class="labelkeyfigure lh-sm mt-2">Returnees</p>
                </div>
            </div>
            <div class="row">
                <div class='col disclaimer'>
                    <?php $disclaimerDisplacement_text = "IDPs as of "; ?>
                    <strong>IDPs</strong> as of
                    @foreach ($disclaimerDisplacement_idp as $disclaimer)
                        {{$disclaimer["Mois"]}} ({{$disclaimer["adminName"]}})  
                        <?php
                            $disclaimerDisplacement_text .= $disclaimer["Mois"]." ".$disclaimer["adminName"];
                        ?>
                    @endforeach |

                    <strong>Refugees</strong> as of
                    <?php 
                        $disclaimerDisplacement_text .= " | Refugees as of "; 
                    ?>
                    @foreach ($disclaimerDisplacement_ref as $disclaimer)
                         {{$disclaimer["Mois"]}} ({{$disclaimer["adminName"]}})
                         <?php
                            $disclaimerDisplacement_text .= $disclaimer["Mois"]." ".$disclaimer["adminName"];
                        ?>
                    @endforeach
                     | 
                    <strong>Returnees</strong> as of
                    <?php 
                        $disclaimerDisplacement_text .= " | Returnees as of "; 
                    ?>
                    @foreach ($disclaimerDisplacement_ret as $disclaimer)
                         {{$disclaimer["Mois"]}} ({{$disclaimer["adminName"]}})
                         <?php
                            $disclaimerDisplacement_text .= $disclaimer["Mois"]." ".$disclaimer["adminName"];
                        ?>
                    @endforeach 
                     <br/><strong>Sources</strong> : 
                     <?php
                        $disclaimerDisplacement_text .= " | Sources : ";
                    ?>
                    @foreach ($sourceDisplacements as $source)
                        {{$source}},
                        <?php
                            $disclaimerDisplacement_text .= $source.", ";
                        ?>
                    @endforeach 
                </div>
            </div>
        </div>

        <div class="col-1 keyFigure-card cards rounded me-1 ms-1"  id="keyFigure-nutrition" onclick="showData('nutrition')">
            <p>Nutrition</p>
            <div class="row">
                <div class='col'>
                    <div>
                        <img src="{{asset('images/Nutrition.svg')}}" style="height:26px;"  alt="..."/> 
                        <span class="keyfigure">{{convertToUnit($KeyFigurenutritions["sam"],1)}}</span>
                    </div>
                    <p class="labelkeyfigure lh-sm mt-2">Severe Accure Malnourished</p>
                </div>
            </div>
            <div class="row">
                <div class='col disclaimer'>
                    <strong></strong> as of
                    <?php 
                        $disclaimerNutrition_text = "SAM, MAM, GAM as of "; 
                    ?>
                    @foreach ($disclaimerNutrition_SAM as $disclaimer)
                        {{$disclaimer["Mois"]}} ({{$disclaimer["adminName"]}})
                        <?php
                            $disclaimerNutrition_text .= $disclaimer["Mois"]." ".$disclaimer["adminName"];
                        ?>
                    @endforeach
                    <br/><strong>Sources</strong> : 
                    <?php
                        $disclaimerNutrition_text .= " | Sources : ";
                    ?>
                    @foreach ($nutritionSource as $source)
                        {{$source}},
                        <?php
                            $disclaimerNutrition_text .= $source.", ";
                        ?>
                    @endforeach 
                </div>
            </div>
        </div> 

        <div class="col-2 keyFigure-card cards rounded me-1 ms-1" id="keyFigure-foodSecurity"  onclick="showData('foodSecurity')">
            <p>Food security</p>
            <div class="row">
                <div class='col'>
                    <div>
                        <img src="{{asset('images/Food-Security.svg')}}" style="height:20px;"  alt="..."/> 
                        <span class="keyfigure">{{convertToUnit($KeyFigureCHCurrent["ch_phase35"],1)}}</span>
                    </div>
                    <p class="labelkeyfigure lh-sm mt-2">Current Food Insecure</p>
                </div>
                <div class='col'>
                    <div>
                        <img src="{{asset('images/Food-Security.svg')}}" style="height:20px;"  alt="..."/> 
                        <span class="keyfigure">{{convertToUnit($KeyFigureCHProjeted["ch_phase35"],1)}}</span>
                    </div>
                    <p class="labelkeyfigure lh-sm mt-2">Projected Food Insecure</p>
                </div>
            </div>
            <div class="row">
                <div class='col disclaimer'>
                    @if ($disclaimer_ch_mois_current != $disclaimer_ch_mois_projected)
                        <?php 
                            $disclaimerCh_text = "Source  : Cadre Harmonisé Current : ".$disclaimer_ch_mois_current.", Projected : ".$disclaimer_ch_mois_projected." ".$disclaimer_ch_year ; 
                        ?>
                        <strong>Source</strong> : Cadre Harmonisé Current : {{$disclaimer_ch_mois_current}}, Projected : {{$disclaimer_ch_mois_projected}} {{$disclaimer_ch_year}}
                    @else
                        <strong>Source</strong> : Cadre Harmonisé {{$disclaimer_ch_mois_current}} {{$disclaimer_ch_year}}
                        <?php 
                            $disclaimerCh_text = "Source  : Cadre Harmonisé Current : ".$disclaimer_ch_mois_current." ".$disclaimer_ch_year ; 
                        ?>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>

    <div class="row white-blocs">
        <div class="col-12 bloc-data" id="bloc-data-caseloads">
            <div class="row">
                <div class="col-8">
                    <div class="row">
                        <div class="col">
                            <p>People in need by {{$adminLevel}}</p>
                            <a href="#" class="btn-link" onclick="downloadMap('caseloads')"><em>download map</em></a>
                            <div class="map-caseloads mb-2" id="map-caseloads" style="width:auto;height:500px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="row m-1">
                        <div class="col-12 mb-3">
                            <p>Trend by year</p>
                            <!--a href="#" class="btn-link" onclick="downloadTrend('caseloads')"><em>image</em></a-->
                            <div class="trend-caseloads" id="trend-caseloads">
                            </div>
                        </div>
                        <div class="col-12">
                            <p>Key figures by country</p>
                            <a href="#" class="btn-link" onclick="downloadData('caseloads')"><em>excel</em></a>
                            <table class="table" style="font-size:12px;" id="caseload-table">
                                <thead>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                        @else
                                            <th scope="col">Country</th>
                                            <th scope="col">Admin1</th>
                                        @endif
                                        <th scope="col">In need</th>
                                        <th scope="col">Targeted</th>
                                        <th scope="col">Reached</th>
                                    </tr>
                                </thead>
                                <?php 
                                    $totalPin= 0;
                                    $totalPt= 0;
                                    $totalPr= 0;
                                ?>
                                <tbody>
                                    @foreach ($KeyFigureCaseLoadsByAdmin as $caseload)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="col">{{$caseload["adminName"]}}</th>
                                            @else
                                                <th scope="col">{{$caseload["admin0"]}}</th>
                                                <th scope="col">{{$caseload["adminName"]}}</th>
                                            @endif
                                            
                                            <td>{{convertToUnit($caseload["pin"],1)}}</td>
                                            <td>{{convertToUnit($caseload["pt"],1)}}</td>
                                            <td>{{convertToUnit($caseload["pr"],1)}}</td>
                                        </tr>
                                        <?php 
                                            $totalPin+= $caseload["pin"];
                                            $totalPt+= $caseload["pt"];
                                            $totalPr+= $caseload["pr"];
                                        ?>
                                    @endforeach
                                    <tr>
                                        
                                        @if ($adminLevel == "admin0")
                                            <th scope="row">Total</th>
                                        @else
                                            <th scope="row">Total</th>
                                            <th scope="col"></th>
                                        @endif
                                        <th scope="row">{{convertToUnit($totalPin,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalPt,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalPr,1)}}</th>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table" id="keyFigure-data-caseloads" style="display:none;">
                                <thead>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <td colspan="4">Disclaimer : {{$disclaimerCaseload_text}}</td>
                                        @else
                                            <td colspan="5">Disclaimer : {{$disclaimerCaseload_text}}</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                        @else
                                            <th scope="col">Country</th>
                                            <th scope="col">Admin1</th>
                                        @endif
                                        <th scope="col">In need</th>
                                        <th scope="col">Targeted</th>
                                        <th scope="col">Reached</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($KeyFigureCaseLoadsByAdmin as $caseload)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$caseload["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$caseload["admin0"]}}</th>
                                                <th scope="row">{{$caseload["adminName"]}}</th>
                                            @endif
                                            
                                            <td>{{$caseload["pin"]}}</td>
                                            <td>{{$caseload["pt"]}}</td>
                                            <td>{{$caseload["pr"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                         
                            <table class="table" id="trend-data-caseloads" style="display:none;">
                                <thead>
                                    <tr>
                                        <th scope="col">Year</th>
                                        @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                        @else
                                            <th scope="col">Country</th>
                                            <th scope="col">Admin1</th>
                                        @endif
                                        <th scope="col">In need</th>
                                        <th scope="col">Targeted</th>
                                        <th scope="col">Reached</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trendCaseloads as $caseload)
                                        <tr>
                                            <th scope="row">{{$caseload["year"]}}</th>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$caseload["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$caseload["admin0"]}}</th>
                                                <th scope="row">{{$caseload["adminName"]}}</th>
                                            @endif
                                            
                                            <td>{{$caseload["pin"]}}</td>
                                            <td>{{$caseload["pt"]}}</td>
                                            <td>{{$caseload["pr"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 bloc-data" id="bloc-data-disp" >
            <div class="row">
                <div class="col-8">
                    <div class="row">
                        <div class="col">
                            <p># of Internally Displaced Persons by {{$adminLevel}}</p>
                            <a href="#" class="btn-link" onclick="downloadMap('displacements')"><em>download map</em></a>
                            <div class="map-displacements" id="map-displacements"  style="width:auto;height:500px;"></div>
                        </div>
                    </div>
                </div>
                

                
                <div class="col-4">
                    <div class="row m-1">
                        <div class="col-12 mb-3">
                            <p>Trend by year</p>
                            <!--a href="#" class="btn-link" onclick="downloadTrend('displacements')"><em>image</em></a-->
                            <div class="trend-displacements" id="trend-displacements">
                            </div>

                        </div>
                        <div class="col-12">
                            <p>Key figures by country</p>
                            <a href="#" class="btn-link" onclick="downloadData('displacements')"><em>Excel</em></a>
                            <table class="table" style="font-size:12px;" id="displacement-table">
                                <thead>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                        @else
                                            <th scope="col">Country</th>
                                            <th scope="col">Admin1</th>
                                        @endif
                                        <th scope="col">IDPs</th>
                                        <th scope="col">Refugees</th>
                                        <th scope="col">Returnees</th>
                                    </tr>
                                </thead>
                                <?php 
                                    $totalIdps= 0;
                                    $totalRef= 0;
                                    $totalRet= 0;
                                ?>
                                <tbody>
                                    @foreach ($KeyFigureDisplacementsByAdmin as $displacement)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$displacement["adminName"]}}</th>
                                            @else
                                                <th scope="row">{{$displacement["admin0"]}}</th>
                                                <th scope="row">{{$displacement["adminName"]}}</th>
                                            @endif
                                            
                                            <td>{{convertToUnit($displacement["idp"],1)}}</td>
                                            <td>{{convertToUnit($displacement["refugees"],1)}}</td>
                                            <td>{{convertToUnit($displacement["returnees"],1)}}</td>
                                        </tr>
                                        <?php 
                                            $totalIdps+= $displacement["idp"];
                                            $totalRef+= $displacement["refugees"];
                                            $totalRet+= $displacement["returnees"];
                                        ?>
                                    @endforeach
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="row">Total</th>
                                        @else
                                            <th scope="row">Total</th>
                                            <th scope="col"></th>
                                        @endif
                                        <th scope="row">{{convertToUnit($totalIdps,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalRef,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalRet,1)}}</th>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table" id="keyFigure-data-displacements" style="display:none;">
                                <thead>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <td colspan="4">Disclaimer : {{$disclaimerDisplacement_text}}</td>
                                        @else
                                            <td colspan="5">Disclaimer : {{$disclaimerDisplacement_text}}</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                        @else
                                            <th scope="col">Country</th>
                                            <th scope="col">Admin1</th>
                                        @endif
                                        <th scope="col">IDPs</th>
                                        <th scope="col">Refugees</th>
                                        <th scope="col">Returnees</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($KeyFigureDisplacementsByAdmin as $displacement)
                                        <tr>
                                            <th scope="row">{{$displacement["adminName"]}}</th>
                                            <td>{{$displacement["idp"]}}</td>
                                            <td>{{$displacement["refugees"]}}</td>
                                            <td>{{$displacement["returnees"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <table class="table" id="trend-data-displacements" style="display:none;">
                                <thead>
                                    <tr>
                                        <th scope="col">Year</th>
                                        @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                        @else
                                            <th scope="col">Country</th>
                                            <th scope="col">Admin1</th>
                                        @endif
                                        <th scope="col">IDPs</th>
                                        <th scope="col">Refugees</th>
                                        <th scope="col">Returnees</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trendDisplacement as $data)
                                        <tr>
                                            <th scope="row">{{$data["year"]}}</th>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$data["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$data["admin0"]}}</th>
                                                <th scope="row">{{$data["adminName"]}}</th>
                                            @endif
                                            <td>{{$data["idp"]}}</td>
                                            <td>{{$data["ref"]}}</td>
                                            <td>{{$data["ret"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 bloc-data" id="bloc-data-nutrition" >
            <div class="row">
                <div class="col-8">
                    <div class="row">
                        <div class="col">
                            <p># of Severe Acuted Malnourished children by {{$adminLevel}}</p>
                            <a href="#" class="btn-link" onclick="downloadMap('nutrition')"><em>download map</em></a>
                            <div class="map-nutrition" id="map-nutrition"  style="width:auto;height:500px;"> </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="row m-1">
                        <div class="col-12 mb-3">
                            <p>Trend by year</p>
                            <!--a href="#" class="btn-link" onclick="downloadTrend('nutrition')"><em>image</em></a-->
                            <div class="trend-nutrition" id="trend-nutrition">
                            </div>
                        </div>
                        <div class="col-12">
                            <p>Key figures by country</p>
                            <a href="#" class="btn-link" onclick="downloadData('nutrition')"><em>excel</em></a>
                            <table class="table" style="font-size:12px;" id="nutrition-table">
                                <thead>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                        @else
                                            <th scope="col">Country</th>
                                            <th scope="col">Admin1</th>
                                        @endif
                                        <th scope="col">GAM</th>
                                        <th scope="col">MAM</th>
                                        <th scope="col">SAM</th>
                                    </tr>
                                </thead>
                                <?php 
                                    $totalGam= 0;
                                    $totalmam= 0;
                                    $totalSam= 0;
                                ?>

                                <tbody>
                                    @foreach ($KeyFigurenutritionsByAdmin as $nutrition)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$nutrition["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$nutrition["admin0"]}}</th>
                                                <th scope="row">{{$nutrition["adminName"]}}</th>
                                            @endif
                                            <td>{{convertToUnit($nutrition["gam"],1)}}</td>
                                            <td>{{convertToUnit($nutrition["mam"],1)}}</td>
                                            <td>{{convertToUnit($nutrition["sam"],1)}}</td>
                                        </tr>
                                        <?php 
                                            $totalGam+= $nutrition["gam"];
                                            $totalmam+= $nutrition["mam"];
                                            $totalSam+= $nutrition["sam"];
                                        ?>
                                    @endforeach
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="row">Total</th>
                                        @else
                                            <th scope="row">Total</th>
                                            <th scope="col"></th>
                                        @endif
                                        <th scope="row">{{convertToUnit($totalGam,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalmam,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalSam,1)}}</th>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table" id="keyFigure-data-nutrition" style="display:none;">
                                <thead>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <td colspan="4">Disclaimer : {{$disclaimerNutrition_text}}</td>
                                        @else
                                            <td colspan="5">Disclaimer : {{$disclaimerNutrition_text}}</td>
                                        @endif
                                    </tr>
                                    <tr>
                                    @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                    @else
                                        <th scope="col">Country</th>
                                        <th scope="col">Admin1</th>
                                    @endif
                                    <th scope="col">GAM</th>
                                    <th scope="col">MAM</th>
                                    <th scope="col">SAM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($KeyFigurenutritionsByAdmin as $nutrition)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$nutrition["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$nutrition["admin0"]}}</th>
                                                <th scope="row">{{$nutrition["adminName"]}}</th>
                                            @endif
                                            <th scope="row">{{$nutrition["adminName"]}}</th>
                                            <td>{{$nutrition["gam"]}}</td>
                                            <td>{{$nutrition["mam"]}}</td>
                                            <td>{{$nutrition["sam"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            

        
                            <table class="table" id="trend-data-nutrition" style="display:none;">
                                <thead>
                                    <tr>
                                        <th scope="col">Year</th>
                                        @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                        @else
                                            <th scope="col">Country</th>
                                            <th scope="col">Admin1</th>
                                        @endif
                                        <th scope="col">GAM</th>
                                        <th scope="col">MAM</th>
                                        <th scope="col">SAM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trendNutrition as $data)
                                        <tr>
                                            <th scope="row">{{$data["year"]}}</th>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$data["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$data["admin0"]}}</th>
                                                <th scope="row">{{$data["adminName"]}}</th>
                                            @endif
                                            
                                            <td>{{$data["gam"]}}</td>
                                            <td>{{$data["mam"]}}</td>
                                            <td>{{$data["sam"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 bloc-data" id="bloc-data-foodSecurity" >
            <div class="row">
                <!-- CURRENT -->
                <div class="col">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="row">
                                <div class="col">
                                    <p>Current Food Insecure by admin 2</p>
                                    <a href="#" class="btn-link" onclick="downloadMap('ch-current')"><em>download map</em></a>
                                    <div class="map-ch" id="map-ch-current"  style="width:auto;height:500px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <p>Trend by year</p>
                            <div class="trend-ch" id="trend-ch-current"></div>
                        </div>
                        <div class="col-12">
                            <p>Key figures by country</p>
                            <a href="#" class="btn-link" onclick="downloadData('ch-current')"><em>excel</em></a>
                            <table class="table" style="font-size:12px;" id="chCurrent-table">
                                <?php 
                                    $totalChP1= 0;
                                    $totalChP2= 0;
                                    $totalChP3= 0;
                                    $totalChP35= 0;
                                    $totalChP4= 0;
                                    $totalChP5= 0;
                                ?>
                                <thead>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="col">Country</th>
                                        @else
                                            <th scope="col">Country</th>
                                            <th scope="col">Admin1</th>
                                        @endif
                                        <th scope="col">Year</th>
                                        <th scope="col">Month</th>
                                        <th scope="col">Phase 1</th>
                                        <th scope="col">Phase 2</th>
                                        <th scope="col">Phase 3</th>
                                        <th scope="col">Phase 3+</th>
                                        <th scope="col">Phase 4</th>
                                        <th scope="col">Phase 5</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($KeyFigureCHByAdminCurrent as $foodSec)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                                <th scope="row">{{$foodSec["adminName"]}}</th>
                                            @endif
                                            <td>{{$foodSec["year"]}}</td>
                                            <td>{{$foodSec["month"]}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase1"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase2"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase3"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase35"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase4"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase5"],1)}}</td>
                                        </tr>
                                        <?php 
                                            $totalChP1+= $foodSec["ch_phase1"];
                                            $totalChP2+= $foodSec["ch_phase2"];
                                            $totalChP3+= $foodSec["ch_phase3"];
                                            $totalChP35+= $foodSec["ch_phase35"];
                                            $totalChP4+= $foodSec["ch_phase4"];
                                            $totalChP5+= $foodSec["ch_phase5"];
                                        ?>

                                    @endforeach

                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="row">Total</th>
                                        @else
                                            <th scope="row">Total</th>
                                            <th scope="col"></th>
                                        @endif
                                        <td> </td>
                                        <td> </td>
                                        <th scope="row">{{convertToUnit($totalChP1,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP2,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP3,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP35,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP4,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP5,1)}}</th>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table" id="keyFigure-data-ch-current"  style="display:none;">
                                <thead>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <td colspan="9">Disclaimer : {{$disclaimerCh_text}}</td>
                                        @else
                                            <td colspan="10">Disclaimer : {{$disclaimerCh_text}}</td>
                                        @endif
                                    </tr>
                                    <tr>
                                    @if ($adminLevel == "admin0")
                                        <th scope="col">Country</th>
                                    @else
                                        <th scope="col">Country</th>
                                        <th scope="col">Admin1</th>
                                    @endif
                                    <th scope="col">Year</th>
                                    <th scope="col">Month</th>
                                    <th scope="col">Phase_1 </th>
                                    <th scope="col">Phase_2 </th>
                                    <th scope="col">Phase_3 </th>
                                    <th scope="col">Phase_3+</th>
                                    <th scope="col">Phase_4 </th>
                                    <th scope="col">Phase_5 </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($KeyFigureCHByAdminCurrent as $foodSec)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                                <th scope="row">{{$foodSec["adminName"]}}</th>
                                            @endif
                                            <td>{{$foodSec["year"]}}</td>
                                            <td>{{$foodSec["month"]}}</td>
                                            <td>{{$foodSec["ch_phase1"]}}</td>
                                            <td>{{$foodSec["ch_phase2"]}}</td>
                                            <td>{{$foodSec["ch_phase3"]}}</td>
                                            <td>{{$foodSec["ch_phase35"]}}</td>
                                            <td>{{$foodSec["ch_phase4"]}}</td>
                                            <td>{{$foodSec["ch_phase5"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            
                            <table class="table" id="trend-data-ch-current"  style="display:none;">
                                <thead>
                                    <tr>
                                    @if ($adminLevel == "admin0")
                                        <th scope="col">Country</th>
                                    @else
                                        <th scope="col">Country</th>
                                        <th scope="col">Admin1</th>
                                    @endif
                                    <th scope="col">Year</th>
                                    <th scope="col">Phase_1 </th>
                                    <th scope="col">Phase_2 </th>
                                    <th scope="col">Phase_3 </th>
                                    <th scope="col">Phase_3+</th>
                                    <th scope="col">Phase_4 </th>
                                    <th scope="col">Phase_5 </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trendCh_Current2 as $foodSec)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                                <th scope="row">{{$foodSec["adminName"]}}</th>
                                            @endif
                                            <td>{{$foodSec["year"]}}</td>
                                            <td>{{$foodSec["ch1"]}}</td>
                                            <td>{{$foodSec["ch2"]}}</td>
                                            <td>{{$foodSec["ch3"]}}</td>
                                            <td>{{$foodSec["ch35"]}}</td>
                                            <td>{{$foodSec["ch4"]}}</td>
                                            <td>{{$foodSec["ch5"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            
                        </div>
                    </div>
                </div>


                <!-- PROJECTED -->
                <div class="col">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="row">
                                <div class="col">
                                    <p>
                                        Projected Food Insecure by admin 2 <br/>
                                        <a href="#" class="btn-link" onclick="downloadMap('ch-projected')"><em>download map</em></a>
                                    </p>
                                    
                                    <div class="map-ch" id="map-ch-projected"  style="width:auto;height:500px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <p>Trend by year</p>
                            <div class="trend-ch" id="trend-ch-projected"></div>
                        </div>
                        <div class="col-12 ">
                            <p>Key figures by country</p>
                            <a href="#" class="btn-link" onclick="downloadData('ch-projected')"><em>excel</em></a>
                            <table class="table" style="font-size:12px;" id="chProjected-table">
                                <thead>
                                    <tr>
                                    @if ($adminLevel == "admin0")
                                        <th scope="col">Country</th>
                                    @else
                                        <th scope="col">Country</th>
                                        <th scope="col">Admin1</th>
                                    @endif
                                    <th scope="col">Year</th>
                                    <th scope="col">Month</th>
                                    <th scope="col">Phase 1</th>
                                    <th scope="col">Phase 2</th>
                                    <th scope="col">Phase 3</th>
                                    <th scope="col">Phase 3+</th>
                                    <th scope="col">Phase 4</th>
                                    <th scope="col">Phase 5</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $totalChP1= 0;
                                        $totalChP2= 0;
                                        $totalChP3= 0;
                                        $totalChP35= 0;
                                        $totalChP4= 0;
                                        $totalChP5= 0;
                                    ?>
                                    @foreach ($KeyFigureCHByAdminProjeted as $foodSec)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                                <th scope="row">{{$foodSec["adminName"]}}</th>
                                            @endif
                                            <td>{{$foodSec["year"]}}</td>
                                            <td>{{$foodSec["month"]}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase1"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase2"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase3"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase35"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase4"],1)}}</td>
                                            <td>{{convertToUnit($foodSec["ch_phase5"],1)}}</td>
                                        </tr>
                                        
                                        <?php 
                                            $totalChP1+= $foodSec["ch_phase1"];
                                            $totalChP2+= $foodSec["ch_phase2"];
                                            $totalChP3+= $foodSec["ch_phase3"];
                                            $totalChP35+= $foodSec["ch_phase35"];
                                            $totalChP4+= $foodSec["ch_phase4"];
                                            $totalChP5+= $foodSec["ch_phase5"];
                                        ?>
                                    @endforeach
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <th scope="row">Total</th>
                                        @else
                                            <th scope="row">Total</th>
                                            <th scope="col"></th>
                                        @endif
                                        <td> </td>
                                        <td> </td>
                                        <th scope="row">{{convertToUnit($totalChP1,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP2,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP3,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP35,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP4,1)}}</th>
                                        <th scope="row">{{convertToUnit($totalChP5,1)}}</th>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table" id="keyFigure-data-ch-projected"  style="display:none;">
                                <thead>
                                    <tr>
                                        @if ($adminLevel == "admin0")
                                            <td colspan="9">Disclaimer : {{$disclaimerCh_text}}</td>
                                        @else
                                            <td colspan="10">Disclaimer : {{$disclaimerCh_text}}</td>
                                        @endif
                                    </tr>
                                    <tr>
                                    @if ($adminLevel == "admin0")
                                        <th scope="col">Country</th>
                                    @else
                                        <th scope="col">Country</th>
                                        <th scope="col">Admin1</th>
                                    @endif
                                    <th scope="col">Year</th>
                                    <th scope="col">Month</th>
                                    <th scope="col">Phase_1 </th>
                                    <th scope="col">Phase_2 </th>
                                    <th scope="col">Phase_3 </th>
                                    <th scope="col">Phase_3+</th>
                                    <th scope="col">Phase_4 </th>
                                    <th scope="col">Phase_5 </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach ($KeyFigureCHByAdminProjeted as $foodSec)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                                <th scope="row">{{$foodSec["adminName"]}}</th>
                                            @endif
                                            <td>{{$foodSec["year"]}}</td>
                                            <td>{{$foodSec["month"]}}</td>
                                            <td>{{$foodSec["ch_phase1"]}}</td>
                                            <td>{{$foodSec["ch_phase2"]}}</td>
                                            <td>{{$foodSec["ch_phase3"]}}</td>
                                            <td>{{$foodSec["ch_phase35"]}}</td>
                                            <td>{{$foodSec["ch_phase4"]}}</td>
                                            <td>{{$foodSec["ch_phase5"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <table class="table" id="trend-data-ch-projected"  style="display:none;">
                                <thead>
                                    <tr>
                                    @if ($adminLevel == "admin0")
                                        <th scope="col">Country</th>
                                    @else
                                        <th scope="col">Country</th>
                                        <th scope="col">Admin1</th>
                                    @endif
                                    <th scope="col">Year</th>
                                    <th scope="col">Phase_1 </th>
                                    <th scope="col">Phase_2 </th>
                                    <th scope="col">Phase_3 </th>
                                    <th scope="col">Phase_3+</th>
                                    <th scope="col">Phase_4 </th>
                                    <th scope="col">Phase_5 </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trendCh_Projected2 as $foodSec)
                                        <tr>
                                            @if ($adminLevel == "admin0")
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                            @else
                                                <th scope="row">{{$foodSec["admin0"]}}</th>
                                                <th scope="row">{{$foodSec["adminName"]}}</th>
                                            @endif
                                            <td>{{$foodSec["year"]}}</td>
                                            <td>{{$foodSec["ch1"]}}</td>
                                            <td>{{$foodSec["ch2"]}}</td>
                                            <td>{{$foodSec["ch3"]}}</td>
                                            <td>{{$foodSec["ch35"]}}</td>
                                            <td>{{$foodSec["ch4"]}}</td>
                                            <td>{{$foodSec["ch5"]}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</div>

<div id='status'></div>


<script>
$(document).ready(function(){
    map_caseload = null;
    map_displacement = null;
    map_nutrition = null;
    map_ch_Current = null;
    map_ch_projected = null;

    disclaimerCaseload_text = {!! json_encode($disclaimerCaseload_text) !!};
    disclaimerDisplacement_text = {!! json_encode($disclaimerDisplacement_text) !!};
    disclaimerNutrition_text = {!! json_encode($disclaimerNutrition_text) !!};
    disclaimerCh_text = {!! json_encode($disclaimerCh_text) !!};
    adminLevel = {!! json_encode($adminLevel) !!};
    pays = {!! json_encode($pays) !!};
    zoneCode = {!! json_encode($zone->zone_code) !!};
    zoneName = {!! json_encode($zone->zone_name) !!};
    //trend data
    trendCaseloads_pin = {!! json_encode($trendCaseloads_PIN) !!};
    caseloadColumns = {!! json_encode($caseloadColumns) !!};
    KeyFigureCaseLoads = {!! json_encode($KeyFigureCaseLoads) !!};

    trendDisplacement_IDP = {!! json_encode($trendDisplacement_IDP) !!};
    displacementColumns = {!! json_encode($displacementColumns) !!};
    KeyFigureDisplacements = {!! json_encode($KeyFigureDisplacements) !!};

    trendNutrition_SAM = {!! json_encode($trendNutrition_SAM) !!};
    nutritionColumns = {!! json_encode($nutritionColumns) !!};
    KeyFigurenutritions = {!! json_encode($KeyFigurenutritions) !!};

    trendCh_Current = {!! json_encode($trendCh_Current) !!};
    ch_CurrentColumns = {!! json_encode($ch_CurrentColumns) !!};
    KeyFigureCHCurrent = {!! json_encode($KeyFigureCHCurrent) !!};

    trendCh_Projected = {!! json_encode($trendCh_Projected) !!};
    ch_ProjectedColumns = {!! json_encode($ch_ProjectedColumns) !!};
    KeyFigureCHProjeted = {!! json_encode($KeyFigureCHProjeted) !!};

    //maps data
    mapCaseloads_PIN = {!! json_encode($mapCaseloads_PIN) !!};
    mapDisplacement_IDP = {!! json_encode($mapDisplacement_IDP) !!};
    mapNutrition_SAM = {!! json_encode($mapNutrition_SAM) !!};
    mapCH_current = {!! json_encode($mapCH_current) !!};
    mapCH_projected = {!! json_encode($mapCH_projected) !!};

    //trends call functions
    AddChart(trendCaseloads_pin,"trend-caseloads",zoneName+": People in need")
    AddChart(trendDisplacement_IDP,"trend-displacements",zoneName+": Internally displaced persons")
    AddChart(trendNutrition_SAM,"trend-nutrition",zoneName+": Severe Acuted Malnourished")
    AddChart(trendCh_Current,"trend-ch-current",zoneName+": Current food insecure")
    AddChart(trendCh_Projected,"trend-ch-projected",zoneName+": Projected food insecure")

    adminCoordinatesFile = "/maps/admin1_coordinates.json"
    if (adminLevel=="admin0") {
        adminCoordinatesFile = "/maps/admin0_coordinates.json"
    }

    if(zoneCode=="WCA"){
        zoneName = pays
    }
    
    admin_coordinates = []

    d3.json(adminCoordinatesFile).then(function(adminCoordinatesTemp){
        admin_coordinates = adminCoordinatesTemp
        addMap_caseload(adminLevel,mapCaseloads_PIN,"map-caseloads","1")
        addMap_displacement(adminLevel,mapDisplacement_IDP,"map-displacements","2")
        addMap_nutrition(adminLevel,mapNutrition_SAM,"map-nutrition","3")
    });
    image1= 0;

    addChMap_current(mapCH_current,"map-ch-current","4")
    addChMap_projected(mapCH_projected,"map-ch-projected","5")
    //addMap2(adminLevel,mapCaseloads_PIN,"chart-test");
});

//other functions
function stopLoading(){
    $('#loading').hide();
    //showData("caseloads");
};

function showLoading(){
    $('#loading').show();
    //showData("caseloads");
};

function UpdateStatus(nb){
    tmpContent = $('#status').html();
    tmpContent+=nb;
    $('#status').html(tmpContent);
    console.log(tmpContent)
    if(tmpContent.length == 5){
        window.setTimeout( stopLoading, 2000 );
        showData("caseloads");
    }
}

function convertToUnit(val,decimal){
    result = "";
    if(val<1000){
        result = val;
    }else{
        if(val<1000000){
            calc = val/1000
            result = calc.toFixed(decimal)+"K";
        }else{
            if(val<1000000000){
                calc = val/1000000
                result = calc.toFixed(decimal)+"M";
            }else{
                calc = val/1000000000
                result = calc.toFixed(decimal)+"B";
            }
        }
    }
    return result;
}

function showData(bloc) {
    $(".bloc-data").hide();
    blocName = "#bloc-data-"+bloc;
    cardId = "#keyFigure-"+bloc;
    
    $(".keyFigure-card").removeClass("cards-selected");
    $(".keyFigure-card").removeClass("cards");
    $(".keyFigure-card").addClass("cards");
    $(cardId).removeClass("cards");
    $(cardId).addClass("cards-selected");
    $(blocName).show();
}





//map functions
function getColor(adminPcode,mapData, grades) {

    dcc= mapData
    d=0;
    for(var i = 0; i < dcc.length; i++){
        arrayAdmin = dcc[i][0].split("*")
        if(adminPcode==arrayAdmin[1]){
            d=dcc[i][1]
        }
    }

    couleur = getColorsAt(0);
    for(var i = 0; i < grades.length; i++){
        if(d>=grades[i]){
            couleur=getColorsAt(i)
            
        }
    }

    return couleur;
}

function getColorCH(adminPcode,mapData) {

    dcc= mapData
    d=0;
    for(var i = 0; i < dcc.length; i++){
        arrayAdmin = dcc[i][0].split("*")
        if(adminPcode==arrayAdmin[1]){
            d=dcc[i][1]
        }
    }

    switch (d) {
        case "1":
            return "#cdfacd";
            break;
        case "2":
            return "#fae61e";
            break;
        case "3":
            return "#e67800";
            break;
        case "4":
            return "#c80000";
            break;
        case "5":
            return "#640000";
            break;
        default:
            return "#ffffff";
            break;
    }
}

function getColorsAt(index){
    colors = [
        /* UN Blue */"#E9F2FB", "#D4E5F7", "#82B5E9", "#418FDE", "#1F69B3", "#144372", "#0B2641",
        /* Purple */"#F1ECF9", "#E4D8F3", "#B99DE0", "#9063CD", "#6937AC", "#462472", "#23133A",
        /* Turquoise */"#EBFAF9", "#D6F5F3", "#AEEAE6", "#71DBD4", "#34CCC1", "#248F88", "#0F3D3A",
        /* Salmon */"#FCECE9", "#F8D8D3", "#EFA497", "#E56A54", "#CD3A1F", "#8B2715", "#42130A",
        /* Orange */"#FCF2E8", "#FAE6D1", "#F4C799", "#ECA154", "#DB7B18",  "#965410", "#452707",
        /* Yellow */"#FBFCE9","#F7F8D3","#EFF2AA","#E2E868","#D5DE26","#989F18","#40420A",
        /* Green */"#F4FAEB","#E8F5D6","#C6E69B","#A4D65E","#7FB92F","#557C1F","#2A3D10",
        /* Brown */"#F8F4EC","#F1E9DA","#E8DCC4","#D3BC8D","#BE9C56","#907337","#372C15",
    ]

    return colors[index]
}

function filterCH(feature) {
    include = false;
    for(var i = 0; i < mapCH_current.length; i++){
        arrayAdmin = mapCH_current[i][0].split("*")
        
        if(feature.properties.adminPcode==arrayAdmin[1]){
            include = true
            //console.log(feature.properties.adminPcode+" ==== "+arrayAdmin[0])
        }
    }
    return include;
}

function filter(feature) {
    include = false;
    for(var i = 0; i < mapCaseloads_PIN.length; i++){
        arrayAdmin = mapCaseloads_PIN[i][0].split("*")
        
        if(feature.properties.adminPcode==arrayAdmin[1]){
            include = true
            //console.log(feature.properties.adminPcode+" ==== "+arrayAdmin[0])
        }
    }
    return include;
}

function filter_caseload(feature) {
    include = false;
    for(var i = 0; i < mapCaseloads_PIN.length; i++){
        arrayAdmin = mapCaseloads_PIN[i][0].split("*")
        
        if(feature.properties.adminPcode==arrayAdmin[1]){
            include = true
        }
    }
    return include;
}

function filter_displacement(feature) {
    include = false;
    for(var i = 0; i < mapDisplacement_IDP.length; i++){
        arrayAdmin = mapDisplacement_IDP[i][0].split("*")
        
        if(feature.properties.adminPcode==arrayAdmin[1]){
            include = true
        }
    }
    return include;
}
function filter_nutrition(feature) {
    include = false;
    for(var i = 0; i < mapNutrition_SAM.length; i++){
        arrayAdmin = mapNutrition_SAM[i][0].split("*")
        
        if(feature.properties.adminPcode==arrayAdmin[1]){
            include = true
        }
    }
    return include;
}

function addMap2(adminLevel,dataMap,place,numMap){
    geoJsonFile = "/maps/wca_admin1.json"
    if (adminLevel=="admin0") {
        geoJsonFile = "/maps/wca_admin0.json"
    }
    
    d3.json(geoJsonFile).then(function(us){
    
        var mapboxAccessToken = 'pk.eyJ1Ijoib2NoYXJvd2NhIiwiYSI6ImNrYncwenh5aTBiZWgycnA3N29jZmx2ZnoifQ.yCtQthC-Ft81ojuRTNoY1g';
        var map = L.map(place, { scrollWheelZoom: false }).setView([37.8, -96], 4);
        grades2  =GetGrades(dataMap)
    
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxAccessToken, {
            id: 'mapbox/light-v9',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(map);
        
        switch (place) {
            case "map-caseloads":
                area  = new L.geoJSON(us, { style: function (feature) { return { fillColor: getColor(feature.properties.adminPcode,dataMap,grades2), weight: 1, opacity: 1, color: '#ccc', dashArray: '3', fillOpacity: 1}; }, filter: filter_caseload})
                break;
            case "map-displacements":
                area  = new L.geoJSON(us, { style: function (feature) { return { fillColor: getColor(feature.properties.adminPcode,dataMap,grades2), weight: 1, opacity: 1, color: '#ccc', dashArray: '3', fillOpacity: 1}; }, filter: filter_displacement})
                break;
            case "map-nutrition":
                area  = new L.geoJSON(us, { style: function (feature) { return { fillColor: getColor(feature.properties.adminPcode,dataMap,grades2), weight: 1, opacity: 1, color: '#ccc', dashArray: '3', fillOpacity: 1}; }, filter: filter_nutrition})
                break;
            default:
                break;
        }
        
        //légende
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend'),
                grades = GetGrades(dataMap),
                labels = [];
                
            // loop through our density intervals and generate a label with a colored square for each interval
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<span style="display:block;height: 21px;"><i style="background:' + getColorsAt(i) + ';border:1px solid #ccc;"></i> ' +
                    convertToUnit(grades[i],0) + (grades[i + 1] ? '&ndash;' + convertToUnit(grades[i + 1],0) + '</span>' : '+');
            }
            return div;
        };
        legend.addTo(map);
        
        //labels
        var markers = new L.FeatureGroup();
        for (var i = 0; i < dataMap.length; i++) {
            for (var j = 0; j < admin_coordinates.length; j++) {
                arrayAdmin = dataMap[i][0].split("*")
                if (admin_coordinates[j].adminPcod==arrayAdmin[1]) {
                    var myIcon = L.divIcon({className: 'labelCarte',html: convertToUnit(dataMap[i][1],1),iconAnchor: [2, 0]});
                    // you can set .my-div-icon styles in CSS
                    markerTemp = L.marker([admin_coordinates[j].Lat,admin_coordinates[j].Long], { icon: myIcon });

                    //var markerTemp = L.marker([admin0_coordinates[j].lat,admin0_coordinates[j].Long],{opacity:0.5}).bindPopup(convertToUnit(dataMap[i][1],1)).openPopup();
                    markers.addLayer(markerTemp);
                }
            }
        }

        

        map.addLayer(markers);
        area.addTo(map);
        map.fitBounds(area.getBounds());


        UpdateStatus(numMap);
    });
    
   
}

function addMap_caseload(adminLevel,dataMap,place,numMap){
    geoJsonFile = "/maps/wca_admin1.json"
    if (adminLevel=="admin0") {
        geoJsonFile = "/maps/wca_admin0.json"
    }
    
    d3.json(geoJsonFile).then(function(us){
    
        var mapboxAccessToken = 'pk.eyJ1Ijoib2NoYXJvd2NhIiwiYSI6ImNrYncwenh5aTBiZWgycnA3N29jZmx2ZnoifQ.yCtQthC-Ft81ojuRTNoY1g';
        map_caseload = L.map(place, { scrollWheelZoom: false }).setView([37.8, -96], 4);
        grades2  = GetGrades(dataMap)
    
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxAccessToken, {
            id: 'mapbox/light-v9',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(map_caseload);
        
        area  = new L.geoJSON(us, { style: function (feature) { return { fillColor: getColor(feature.properties.adminPcode,dataMap,grades2), weight: 1, opacity: 1, color: '#ccc', dashArray: '3', fillOpacity: 1}; }, filter: filter_caseload})

        
        //légende
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend'),
                grades = GetGrades(dataMap),
                labels = [];
                
            // loop through our density intervals and generate a label with a colored square for each interval
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<span style="display:block;height: 21px;"><i style="background:' + getColorsAt(i) + ';border:1px solid #ccc;"></i> ' +
                    convertToUnit(grades[i],0) + (grades[i + 1] ? '&ndash;' + convertToUnit(grades[i + 1],0) + '</span>' : '+');
            }
            return div;
        };
        legend.addTo(map_caseload);
        
        //labels
        var markers = new L.FeatureGroup();
        for (var i = 0; i < dataMap.length; i++) {
            for (var j = 0; j < admin_coordinates.length; j++) {
                arrayAdmin = dataMap[i][0].split("*")
                if (admin_coordinates[j].adminPcod==arrayAdmin[1]) {
                    var myIcon = L.divIcon({className: 'labelCarte',html: convertToUnit(dataMap[i][1],1),iconAnchor: [2, 0]});
                    // you can set .my-div-icon styles in CSS
                    markerTemp = L.marker([admin_coordinates[j].Lat,admin_coordinates[j].Long], { icon: myIcon });

                    //var markerTemp = L.marker([admin0_coordinates[j].lat,admin0_coordinates[j].Long],{opacity:0.5}).bindPopup(convertToUnit(dataMap[i][1],1)).openPopup();
                    markers.addLayer(markerTemp);
                }
            }
        }

        map_caseload.addLayer(markers);
        area.addTo(map_caseload);
        map_caseload.fitBounds(area.getBounds());


        UpdateStatus(numMap);
    });
}

function addMap_displacement(adminLevel,dataMap,place,numMap){
    geoJsonFile = "/maps/wca_admin1.json"
    if (adminLevel=="admin0") {
        geoJsonFile = "/maps/wca_admin0.json"
    }
    
    d3.json(geoJsonFile).then(function(us){
    
        var mapboxAccessToken = 'pk.eyJ1Ijoib2NoYXJvd2NhIiwiYSI6ImNrYncwenh5aTBiZWgycnA3N29jZmx2ZnoifQ.yCtQthC-Ft81ojuRTNoY1g';
        map_displacement = L.map(place, { scrollWheelZoom: false }).setView([37.8, -96], 4);
        grades2  = GetGrades(dataMap)
    
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxAccessToken, {
            id: 'mapbox/light-v9',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(map_displacement);
        
        area  = new L.geoJSON(us, { style: function (feature) { return { fillColor: getColor(feature.properties.adminPcode,dataMap,grades2), weight: 1, opacity: 1, color: '#ccc', dashArray: '3', fillOpacity: 1}; }, filter: filter_displacement})

        
        //légende
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend'),
                grades = GetGrades(dataMap),
                labels = [];
                
            // loop through our density intervals and generate a label with a colored square for each interval
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<span style="display:block;height: 21px;"><i style="background:' + getColorsAt(i) + ';border:1px solid #ccc;"></i> ' +
                    convertToUnit(grades[i],0) + (grades[i + 1] ? '&ndash;' + convertToUnit(grades[i + 1],0) + '</span>' : '+');
            }
            return div;
        };
        legend.addTo(map_displacement);
        
        //labels
        var markers = new L.FeatureGroup();
        for (var i = 0; i < dataMap.length; i++) {
            for (var j = 0; j < admin_coordinates.length; j++) {
                arrayAdmin = dataMap[i][0].split("*")
                if (admin_coordinates[j].adminPcod==arrayAdmin[1]) {
                    var myIcon = L.divIcon({className: 'labelCarte',html: convertToUnit(dataMap[i][1],1),iconAnchor: [2, 0]});
                    // you can set .my-div-icon styles in CSS
                    markerTemp = L.marker([admin_coordinates[j].Lat,admin_coordinates[j].Long], { icon: myIcon });

                    //var markerTemp = L.marker([admin0_coordinates[j].lat,admin0_coordinates[j].Long],{opacity:0.5}).bindPopup(convertToUnit(dataMap[i][1],1)).openPopup();
                    markers.addLayer(markerTemp);
                }
            }
        }

        map_displacement.addLayer(markers);
        area.addTo(map_displacement);
        map_displacement.fitBounds(area.getBounds(),4);

        UpdateStatus(numMap);
    });
}

function addMap_nutrition(adminLevel,dataMap,place,numMap){
    geoJsonFile = "/maps/wca_admin1.json"
    if (adminLevel=="admin0") {
        geoJsonFile = "/maps/wca_admin0.json"
    }
    
    d3.json(geoJsonFile).then(function(us){
    
        var mapboxAccessToken = 'pk.eyJ1Ijoib2NoYXJvd2NhIiwiYSI6ImNrYncwenh5aTBiZWgycnA3N29jZmx2ZnoifQ.yCtQthC-Ft81ojuRTNoY1g';
        map_nutrition = L.map(place, { scrollWheelZoom: false }).setView([37.8, -96], 4);
        grades2  = GetGrades(dataMap)
    
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxAccessToken, {
            id: 'mapbox/light-v9',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(map_nutrition);
        
        area  = new L.geoJSON(us, { style: function (feature) { return { fillColor: getColor(feature.properties.adminPcode,dataMap,grades2), weight: 1, opacity: 1, color: '#ccc', dashArray: '3', fillOpacity: 1}; }, filter: filter_nutrition})

        
        //légende
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend'),
                grades = GetGrades(dataMap),
                labels = [];
                
            // loop through our density intervals and generate a label with a colored square for each interval
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<span style="display:block;height: 21px;"><i style="background:' + getColorsAt(i) + ';border:1px solid #ccc;"></i> ' +
                    convertToUnit(grades[i],0) + (grades[i + 1] ? '&ndash;' + convertToUnit(grades[i + 1],0) + '</span>' : '+');
            }
            return div;
        };
        legend.addTo(map_nutrition);
        
        //labels
        var markers = new L.FeatureGroup();
        for (var i = 0; i < dataMap.length; i++) {
            for (var j = 0; j < admin_coordinates.length; j++) {
                arrayAdmin = dataMap[i][0].split("*")
                if (admin_coordinates[j].adminPcod==arrayAdmin[1]) {
                    var myIcon = L.divIcon({className: 'labelCarte',html: convertToUnit(dataMap[i][1],1),iconAnchor: [2, 0]});
                    // you can set .my-div-icon styles in CSS
                    markerTemp = L.marker([admin_coordinates[j].Lat,admin_coordinates[j].Long], { icon: myIcon });

                    //var markerTemp = L.marker([admin0_coordinates[j].lat,admin0_coordinates[j].Long],{opacity:0.5}).bindPopup(convertToUnit(dataMap[i][1],1)).openPopup();
                    markers.addLayer(markerTemp);
                }
            }
        }

        map_nutrition.addLayer(markers);
        area.addTo(map_nutrition);
        map_nutrition.fitBounds(area.getBounds());


        UpdateStatus(numMap);
    });
}

function addChMap_projected(dataMap,place,numMap){
    geoJsonFile = "/maps/wca_admin2.json"
    
    d3.json(geoJsonFile).then(function(us){

        var mapboxAccessToken = 'pk.eyJ1Ijoib2NoYXJvd2NhIiwiYSI6ImNrYncwenh5aTBiZWgycnA3N29jZmx2ZnoifQ.yCtQthC-Ft81ojuRTNoY1g';
        map_ch_projected = L.map(place, { scrollWheelZoom: false }).setView([37.8, -96], 4);

    
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxAccessToken, {
            id: 'mapbox/light-v9',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(map_ch_projected);
        area  = new L.geoJSON(us, {
            style: function (feature) {
                return {
                    fillColor: getColorCH(feature.properties.adminPcode,dataMap),
                    weight: 1,
                    opacity: 1,
                    color: '#ccc',
                    dashArray: '3',
                    fillOpacity: 1
                    };
                },
                filter: filterCH})

        area.addTo(map_ch_projected);
        map_ch_projected.fitBounds(area.getBounds());
        UpdateStatus(numMap);
    });
    
   
}

function addChMap_current(dataMap,place,numMap){
    geoJsonFile = "/maps/wca_admin2.json"
    
    d3.json(geoJsonFile).then(function(us){
 
        var mapboxAccessToken = 'pk.eyJ1Ijoib2NoYXJvd2NhIiwiYSI6ImNrYncwenh5aTBiZWgycnA3N29jZmx2ZnoifQ.yCtQthC-Ft81ojuRTNoY1g';
        map_ch_Current = L.map(place, { scrollWheelZoom: false }).setView([37.8, -96], 0);

    
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxAccessToken, {
            id: 'mapbox/light-v9',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(map_ch_Current);
        area  = new L.geoJSON(us, {
            style: function (feature) {
                return {
                    fillColor: getColorCH(feature.properties.adminPcode,dataMap),
                    weight: 1,
                    opacity: 1,
                    color: '#ccc',
                    dashArray: '3',
                    fillOpacity: 1
                    };
                },
                filter: filterCH})

        area.addTo(map_ch_Current);
        map_ch_Current.fitBounds(area.getBounds());

        UpdateStatus(numMap);
    });
}

function GetGrades(dataGrades){
    var dataArray = []
    for (var i = 0; i < dataGrades.length; i++) {
        dataArray.push(dataGrades[i][1]);
    }

    nbGrades = 4;
    minVal = d3.min(dataArray)
    maxVal = d3.max(dataArray)
    gradeStepTmp = Math.round((maxVal - minVal)/(nbGrades - 1)).toString()
    
    gradeStep = gradeStepTmp.substring(0, 1);
    for (var i = 0; i < (gradeStepTmp.length-1); i++) {
        gradeStep+="0"
    }

    minRange = gradeStep
    maxRange = gradeStep*nbGrades

    grades = d3.range(0,maxRange,gradeStep)



    return grades
}

function downloadMap(categ){
    var mapName = "#map-"+categ
    id="map-"+categ
    width = $(mapName).width()
    height = $(mapName).height()

    domtoimage.toJpeg(document.getElementById(id), { quality: 1,height:height,width:width }).then(function (dataUrl) {
        var link = document.createElement('a');
        link.download = 'map_'+categ;
        link.href = dataUrl;
        link.click();
    });
}





//trend functions
function downloadTrend(categ){
   
    mapName = "#trend-"+categ
    html2canvas(document.querySelector(mapName)).then(function(canvas) {
        saveAs(canvas.toDataURL(), 'file-name.png');
    });
}

function getColors(numberOfColors){
    colors = [
        /* UN Blue */"#E9F2FB", "#D4E5F7", "#82B5E9", "#418FDE", "#1F69B3", "#144372", "#0B2641",
        /* Purple */"#F1ECF9", "#E4D8F3", "#B99DE0", "#9063CD", "#6937AC", "#462472", "#23133A",
        /* Turquoise */"#EBFAF9", "#D6F5F3", "#AEEAE6", "#71DBD4", "#34CCC1", "#248F88", "#0F3D3A",
        /* Salmon */"#FCECE9", "#F8D8D3", "#EFA497", "#E56A54", "#CD3A1F", "#8B2715", "#42130A",
        /* Orange */"#FCF2E8", "#FAE6D1", "#F4C799", "#ECA154", "#DB7B18",  "#965410", "#452707",
        /* Yellow */"#FBFCE9","#F7F8D3","#EFF2AA","#E2E868","#D5DE26","#989F18","#40420A",
        /* Green */"#F4FAEB","#E8F5D6","#C6E69B","#A4D65E","#7FB92F","#557C1F","#2A3D10",
        /* Brown */"#F8F4EC","#F1E9DA","#E8DCC4","#D3BC8D","#BE9C56","#907337","#372C15",
    ]

    palette = []

    for (let index = 0; index < numberOfColors; index++) {
        if(index>colors.length){
            palette.push("#418fde");
        }else{
            palette.push(colors[index]);
        }
        
    }
    return palette
}

function AddChart(series,element,title){
    array_color = getColors(series.length)

    var options = {
        zoom: {
            enabled: false,
        },
          series: series,
          chart: {
          type: 'area',
          height: 350,
          stacked: true,
          events: {
            selection: function (chart, e) {
              //console.log(new Date(e.xaxis.min))
            }
          },
        },
        colors: array_color,
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth'
        },
        fill: {
          type: 'solid',
          opacity: 1,
        },
        legend: {
          position: 'top',
          horizontalAlign: 'left'
        },
        xaxis: {
          type: 'category'
        },
        yaxis: {
            show: true,
            labels: {
                formatter: (value) => { return convertToUnit(value,0) },
            },
        },
    };

    blocId = "#"+element;
    var chart = new ApexCharts(document.querySelector(blocId), options);
    chart.render();
}





//export functions
function ExportExcel() {
    var utc = new Date().toJSON().slice(0,10).replace(/-/g,' ');
    showLoading();
    tablesToExcel(
        [
            'keyFigure-data-caseloads',
            'keyFigure-data-displacements',
            'keyFigure-data-nutrition',
            'keyFigure-data-ch-current',
            'keyFigure-data-ch-projected',
            'trend-data-caseloads',
            'trend-data-displacements',
            'trend-data-nutrition',
            'trend-data-ch-current',
            'trend-data-ch-projected',
        ], 
        [
            'KF caseloads',
            'KF displacements',
            'KF nutrition',
            'KF ch-current',
            'KF ch-projected',
            'Trend by year caseloads',
            'Trend by year displacements',
            'Trend by year nutrition',
            'Trend by year ch-current',
            'Trend by year ch-projected',
        ], 'Database Export of all data as of '+utc+'.xls', 'Excel')
        stopLoading();
}

function ExportPowerPoint(){
    showLoading();
    $(".bloc-data").show();

    //centrer la carte
    //map_caseload.fitBounds(area.getBounds());
    //map_displacement.fitBounds(area.getBounds());
    //map_nutrition.fitBounds(area.getBounds());
    //map_ch_projected.fitBounds(area.getBounds());
    //map_ch_Current.fitBounds(area.getBounds());


    //DIMENSIONS OF TRENDS
    width_CL = $("#trend-caseloads").width()
    height_CL = $("#trend-caseloads").height()

    width_DI = $("#trend-displacements").width()
    height_DI = $("#trend-displacements").height()

    width_NU = $("#trend-nutrition").width()
    height_NU = $("#trend-nutrition").height()

    width_CC = $("#trend-ch-current").width()
    height_CC = $("#trend-ch-current").height()

    width_CP = $("#trend-ch-projected").width()
    height_CP = $("#trend-ch-projected").height()

    //DIMENSIONS OF MAPS
    width_m_CL = $("#map-caseloads").width()
    height_m_CL = $("#map-caseloads").height()

    width_m_DI = $("#map-displacements").width()
    height_m_DI = $("#map-displacements").height()

    width_m_NU = $("#map-nutrition").width()
    height_m_NU = $("#map-nutrition").height()

    width_m_CHc = $("#map-ch-current").width()
    height_m_CHc = $("#map-ch-current").height()

    width_m_CHp = $("#map-ch-projected").width()
    height_m_CHp = $("#map-ch-projected").height()


    domtoimage.toJpeg(document.getElementById("map-caseloads"), { quality: 1,height:height_m_CL,width:width_m_CL,bgcolor:'#ffffff'}).then(function (caseloadMap) {
        domtoimage.toJpeg(document.getElementById("map-displacements"), { quality: 1,height:height_m_DI,width:width_m_DI,bgcolor:'#ffffff' }).then(function (displacementsMap) {
            domtoimage.toJpeg(document.getElementById("map-nutrition"), { quality: 1,height:height_m_NU,width:width_m_NU ,bgcolor:'#ffffff'}).then(function (nutritionMap) {
                domtoimage.toJpeg(document.getElementById("map-ch-current"), { quality: 1,height:height_m_CHc,width:width_m_CHc ,bgcolor:'#ffffff'}).then(function (chCurrentMap) {
                    domtoimage.toJpeg(document.getElementById("map-ch-projected"), { quality: 1,height:height_m_CHp,width:width_m_CHp ,bgcolor:'#ffffff'}).then(function (chProjectedMap) {
                        domtoimage.toJpeg(document.getElementById("trend-caseloads"), { quality: 1,height:height_CL,width:width_CL,bgcolor:'#ffffff'}).then(function (caseloadImage) {
                            domtoimage.toJpeg(document.getElementById("trend-displacements"), { quality: 1,height:height_DI,width:width_DI,bgcolor:'#ffffff' }).then(function (displacementsImage) {
                                domtoimage.toJpeg(document.getElementById("trend-nutrition"), { quality: 1,height:height_NU,width:width_NU ,bgcolor:'#ffffff'}).then(function (nutritionImage) {
                                    domtoimage.toJpeg(document.getElementById("trend-ch-current"), { quality: 1,height:height_CC,width:width_CC ,bgcolor:'#ffffff'}).then(function (chCurrentImage) {
                                        domtoimage.toJpeg(document.getElementById("trend-ch-projected"), { quality: 1,height:height_CP,width:width_CP,bgcolor:'#ffffff' }).then(function (chProjectedImage) {


                                            var pptx = new PptxGenJS();

                                            // STEP 2: Add a new Slide to the Presentation
                                            var slide = pptx.addSlide();
                                            var slide_caseLoad = pptx.addSlide();
                                            pptx.tableToSlides("caseload-table");
                                            var slide_disp = pptx.addSlide();
                                            pptx.tableToSlides("displacement-table");
                                            var slide_nutrition = pptx.addSlide();
                                            pptx.tableToSlides("nutrition-table");
                                            var slide_foodSecCurrent = pptx.addSlide();
                                            pptx.tableToSlides("chCurrent-table");
                                            var slide_foodSecProjected = pptx.addSlide();
                                            pptx.tableToSlides("chProjected-table");

                                            slide.addText('Presentation for', { x:0.5, y:1.44, fontSize:18, color:'418fde' });
                                            slide.addText(zoneName, { x:0.5, y:1.86, fontSize:18, color:'418fde' });

                                            //CASELOADS
                                            slide_caseLoad.addText('Caseloads', { x:0.47, y:0.42, fontSize:18, color:'418fde' });
                                            slide_caseLoad.addText('People in need', { x:0.47,y:1.25, fontSize:11, color:'999999', w: 1.30});
                                            slide_caseLoad.addText(convertToUnit(KeyFigureCaseLoads.pin,1), { x:0.95,y:0.98, fontSize:14, color:'418fde', w: 1.30});
                                            slide_caseLoad.addText('People targeted', { x:2.12, y:1.25, fontSize:11, color:'999999', w: 1.30 });
                                            slide_caseLoad.addText(convertToUnit(KeyFigureCaseLoads.pt,1), { x:2.52, y:0.98, fontSize:14, color:'418fde', w: 1.30 });
                                            slide_caseLoad.addText('People reached', { x:3.76,y:1.25, fontSize:11, color:'999999', w: 1.30 });
                                            slide_caseLoad.addText(convertToUnit(KeyFigureCaseLoads.pr,1), { x:4.07,y:0.98, fontSize:14, color:'418fde', w: 1.30 });
                                            slide_caseLoad.addImage({ path: "/images/People-in-need.svg", x: 0.58,y: 0.86,  w: 0.37, h: 0.26 });
                                            slide_caseLoad.addImage({ path: "/images/People-targeted.svg", y: 0.86,x: 2.26,  w: 0.26, h: 0.26 });
                                            slide_caseLoad.addImage({ path: "/images/Person-2.svg", y: 0.86,x: 3.93,  w: 0.14, h: 0.26 });
                                            slide_caseLoad.addImage({ data: caseloadImage,x: 5.87,y: 1.87,  w: 4.00, h: 3.00 });
                                            slide_caseLoad.addImage({ data: caseloadMap,x: 0.22,y: 1.87,  w: 5.53, h: 3.00 });
                                            slide_caseLoad.addText('Disclaimer : ' + disclaimerCaseload_text, { x:0.12, y:5.04, fontSize:9, color:'999999' });

                                            

                                            //DISPLACEMENTS
                                            slide_disp.addText('Displacements', { x:0.47, y:0.42, fontSize:18, color:'418fde' });
                                            slide_disp.addText('IDPs', { x:0.47,y:1.25, fontSize:11, color:'999999', w: 1.30});
                                            slide_disp.addText(convertToUnit(KeyFigureDisplacements.idp,1), { x:0.95,y:0.98, fontSize:14, color:'418fde', w: 1.30});
                                            slide_disp.addText('Refugees', { x:2.12, y:1.25, fontSize:11, color:'999999', w: 1.30 });
                                            slide_disp.addText(convertToUnit(KeyFigureDisplacements.refugees,1), { x:2.52, y:0.98, fontSize:14, color:'418fde', w: 1.30 });
                                            slide_disp.addText('Returnees', { x:3.76,y:1.25, fontSize:11, color:'999999', w: 1.30 });
                                            slide_disp.addText(convertToUnit(KeyFigureDisplacements.returnees,1), { x:4.28,y:0.98, fontSize:14, color:'418fde', w: 1.30 });
                                            slide_disp.addImage({ path: "/images/Internally-displaced.svg", x: 0.58,y: 0.86,  w: 0.31, h: 0.26 });
                                            slide_disp.addImage({ path: "/images/Refugee.svg", y: 0.86,x: 2.26,  w: 0.26, h: 0.26 });
                                            slide_disp.addImage({ path: "/images/Population-return.svg", y: 0.86,x: 3.93,  w: 0.31, h: 0.26 });
                                            slide_disp.addImage({ data: displacementsImage,x: 5.87,y: 1.87,  w: 4.00, h: 3.00 });
                                            slide_disp.addImage({ data: displacementsMap,x: 0.22,y: 1.87,  w: 5.53, h: 3.00 });
                                            slide_disp.addText('Disclaimer : ' + disclaimerDisplacement_text, { x:0.12, y:5.19, w: 9.66, fontSize:9, color:'999999' });

                                            //NUTRITION
                                            slide_nutrition.addText('Nutrition', { x:0.47, y:0.42, fontSize:18, color:'418fde' });
                                            slide_nutrition.addText('SAM', { x:0.47,y:1.25, fontSize:11, color:'999999', w: 1.30});
                                            slide_nutrition.addText(convertToUnit(KeyFigurenutritions.sam,1), { x:0.95,y:0.98, fontSize:14, color:'418fde', w: 1.30});
                                            slide_nutrition.addText('MAM', { x:2.12, y:1.25, fontSize:11, color:'999999', w: 1.30 });
                                            slide_nutrition.addText(convertToUnit(KeyFigurenutritions.mam,1), { x:2.52, y:0.98, fontSize:14, color:'418fde', w: 1.30 });
                                            slide_nutrition.addText('GAM', { x:3.76,y:1.25, fontSize:11, color:'999999', w: 1.30 });
                                            slide_nutrition.addText(convertToUnit(KeyFigurenutritions.gam,1), { x:4.07,y:0.98, fontSize:14, color:'418fde', w: 1.30 });
                                            slide_nutrition.addImage({ path: "/images/Nutrition.svg", x: 0.58,y: 0.86,  w: 0.2, h: 0.26 });
                                            slide_nutrition.addImage({ path: "/images/Nutrition.svg", y: 0.86,x: 2.26,  w: 0.2, h: 0.26 });
                                            slide_nutrition.addImage({ path: "/images/Nutrition.svg", y: 0.86,x: 3.93,  w: 0.2, h: 0.26 });
                                            slide_nutrition.addImage({ data: nutritionImage,x: 5.87,y: 1.87,  w: 4.00, h: 3.00 });
                                            slide_nutrition.addImage({ data: nutritionMap,x: 0.22,y: 1.87,  w: 5.53, h: 3.00 });
                                            slide_nutrition.addText('Disclaimer : ' + disclaimerNutrition_text, { x:0.12, y:5.04, fontSize:9, color:'999999' });
                                            
                                            //CH CURRENT
                                            slide_foodSecCurrent.addText('Cadre harmonirsé current', { x:0.47, y:0.42, fontSize:18, color:'418fde' });
                                            slide_foodSecCurrent.addText('Current Food Insecure (phase 3+)', { x:0.47,y:1.25, fontSize:11, color:'999999'});
                                            slide_foodSecCurrent.addText(convertToUnit(KeyFigureCHCurrent.ch_phase35,1), { x:0.95,y:0.98, fontSize:14, color:'418fde', w: 1.30 });
                                            slide_foodSecCurrent.addImage({ path: "/images/Food-Security.svg", x: 0.58,y: 0.86,  w: 0.37, h: 0.26 });
                                            slide_foodSecCurrent.addImage({ data: chCurrentImage,x: 5.07,y: 1.87,  w: 4.71, h: 2.27 });
                                            slide_foodSecCurrent.addImage({ data: chCurrentMap,x: 0.22,y: 1.87,  w: 4.74, h: 3.23 });
                                            slide_foodSecCurrent.addText('Disclaimer : ' + disclaimerCh_text, { x:0.12, y:5.26, fontSize:9, color:'999999' });
                                            
                                
                                            //CH PROJECTED
                                            slide_foodSecProjected.addText('Cadre harmonirsé projected', { x:0.47, y:0.42, fontSize:18, color:'418fde' });
                                            slide_foodSecProjected.addText('Projected Food Insecure (phase 3+)', { x:0.47,y:1.25, fontSize:11, color:'999999'});
                                            slide_foodSecProjected.addText(convertToUnit(KeyFigureCHProjeted.ch_phase35,1), { x:0.95,y:0.98, fontSize:14, color:'418fde', w: 1.30 });
                                            slide_foodSecProjected.addImage({ path: "/images/Food-Security.svg", x: 0.58,y: 0.86,  w: 0.37, h: 0.26 });
                                            slide_foodSecProjected.addImage({ data: chProjectedImage,x: 5.07,y: 1.87,  w: 4.71, h: 2.27 });
                                            slide_foodSecProjected.addImage({ data: chProjectedMap,x: 0.22,y: 1.87,  w: 4.74, h: 3.23 });
                                            slide_foodSecProjected.addText('Disclaimer : ' + disclaimerCh_text, { x:0.12, y:5.26, fontSize:9, color:'999999' });

                                
                                        

                                            // STEP 4: Send the PPTX Presentation to the user, using your choice of file name
                                            
                                            //pptx.writeFile('PptxGenJs-Basic-Slide-Demo');

                                            var utc = new Date().toJSON().slice(0,10).replace(/-/g,' ');

                                            pptx.writeFile({ fileName: 'Database Presentation export for '+zoneName+' as of '+utc })
                                            .then(fileName => {
                                                window.setTimeout( stopLoading, 5000 );
                                                //$(".bloc-data").hide();
                                                //showData("caseloads");
                                            });
                                        });
                                    });
                                });
                            });
                        });
                    });
                });
            });
        });
    });
}

function downloadData(typeData){
    tableName = "keyFigure-data-"+typeData
    var tableToExport = TableExport(document.getElementById(tableName));

    var exportData = tableToExport.getExportData(); 

    var xlsxData = exportData[tableName].xlsx; 

    tableToExport.export2file(xlsxData.data, xlsxData.mimeType, typeData, xlsxData.fileExtension, xlsxData.merges, xlsxData.RTL, typeData)

}







function stylekk_old(feature) {
    return {
        fillColor: getColor(feature.properties.adminName),
        weight: 2,
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.7
    };
}

function addMap3Old(adminLevel,dataMap,place){
    geoJsonFile = "/maps/wca_admin1.json"
    if (adminLevel=="admin0") {
        geoJsonFile = "/maps/wca_admin0.json"
    }

    d3.json(geoJsonFile).then(function(us){
      
      console.log(us)
        var mapboxAccessToken = 'pk.eyJ1Ijoib2NoYXJvd2NhIiwiYSI6ImNrYncwenh5aTBiZWgycnA3N29jZmx2ZnoifQ.yCtQthC-Ft81ojuRTNoY1g';
        var map2 = L.map(place).setView([37.8, -96], 4);
        grades2  =GetGrades(dataMap)
        
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxAccessToken, {
            id: 'mapbox/light-v9',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(map2);
        area  = new L.geoJSON(us, {
            style: function (feature) {
                return {
                    fillColor: getColor(feature.properties.adminName,dataMap,grades2),
                    weight: 1,
                    opacity: 1,
                    color: '#ccc',
                    dashArray: '3',
                    fillOpacity: 0.7};
                },
            filter: filter})

        //légende
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map2) {
            var div = L.DomUtil.create('div', 'info legend'),
                grades = GetGrades(dataMap),
                labels = [];
                
            // loop through our density intervals and generate a label with a colored square for each interval
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<span style="display:block;height: 21px;"><i style="background:' + getColorsAt(i) + ';border:1px solid #ccc;"></i> ' +
                    convertToUnit(grades[i],0) + (grades[i + 1] ? '&ndash;' + convertToUnit(grades[i + 1],0) + '</span>' : '+');
            }
            return div;
        };

        legend.addTo(map2);
        
        area.addTo(map2);
        map2.fitBounds(area.getBounds());
    });
   
}















/*
function saveAs(uri, filename) {

    var link = document.createElement('a');

    if (typeof link.download === 'string') {

        link.href = uri;
        link.download = filename;

        //Firefox requires the link to be in the body
        document.body.appendChild(link);

        //simulate click
        link.click();

        //remove the link when done
        document.body.removeChild(link);

    } else {

        window.open(uri);

    }
}



function addTestMap(bloc,layerName,adminLevel,mapCaseloads_PIN ,title) {


    d3.json("/maps/"+layerName+"_"+adminLevel+".json").then(function(us){


    states = new Map(us.objects.admin.geometries.map(d => [d.properties.OBJECTID, d.properties]))

    data = Object.assign(new Map(mapCaseloads_PIN), {title: title})
    color = d3.scaleQuantize([d3.min(data, d => d[1]), d3.max(data, d => d[1])], d3.schemeBlues[9])

    path = d3.geoPath()

    format = d => `${d}`

    svg = d3.select('#'+bloc).append("svg")
    .attr("viewBox", [0, 0, 975, 610]);

    svg.append("g")
        .attr("transform", "translate(610,20)")
        .append(() => legend({color, title: data.title, width: 260,tickFormat: d3.format(".0s")}));


    svg.append("g")
        .selectAll("path")
        .data(topojson.feature(us, us.objects.admin).features)
        .join("path")
        .attr("fill", d => color(data.get(d.properties.adminName)))
        .attr("d", path)
        .append("title")
        .text(d => `${d.properties.adminName}, ${states.get(d.properties.OBJECTID).adminName}
    ${format(data.get(d.properties.adminName))}`);

    svg.append("path")
        .datum(topojson.mesh(us, us.objects.admin, (a, b) => a !== b))
        .attr("fill", "none")
        .attr("stroke", "white")
        .attr("stroke-linejoin", "round")
        .attr("d", path);
    })
}


function AddChartOld(data,Columns,element,title){

    //caseloads
    data = Object.assign(data, {y: title})
    series = d3.stack().keys(Columns)(data)

    height = 300
    width = 500
    //margin = ({top: 20, right: 30, bottom: 30, left: 50})
    margin = ({top: 20, right: 30, bottom: 30, left: 60})

    area = d3.area()
    .x(d => x(d.data.year))
    .y0(d => y(d[0]))
    .y1(d => y(d[1]))

    color = d3.scaleOrdinal(d3.schemeBlues[6])




    x = d3.scaleTime().domain(d3.extent(data, d => d.year)).range([margin.left, width - margin.right])
    y = d3.scaleLinear().domain([0, d3.max(series, d => d3.max(d, d => d[1]))]).nice().range([height - margin.bottom, margin.top])


    xAxis = g => g
    .attr("transform", `translate(0,${height - margin.bottom})`)
    .call(d3.axisBottom(x).ticks(width / 80).tickSizeOuter(0))

    yAxis = g => g
    .attr("transform", `translate(${margin.left},0)`)
    .call(d3.axisLeft(y))
    .call(g => g.select(".domain").remove())
    .call(g => g.select(".tick:last-of-type text").clone()
        .attr("x", 3)
        .attr("text-anchor", "start")
        .attr("font-weight", "bold")
        .text(data.y))



    const svg = d3.select("#"+element).append("svg")
        .attr("viewBox", [0, 0, width, height]);

    svg.append("g")
        .selectAll("path")
        .data(series)
        .join("path")
        .attr("fill", ({key}) => color(key))
        .attr("d", area)
        .append("title")
        .text(({key}) => key);

    svg.append("g").call(xAxis);
    svg.append("g").call(yAxis);
}


function AddCaseloadPinMapold2(KeyFigureDisplacementsByAdmin){

    //caseloads
    data = Object.assign(KeyFigureDisplacementsByAdmin, {y: "People in need"})
    //series = d3.stack().keys(caseloadColumns)(data)
    //Width and height
    var w = 500;
    var h = 500;

    var margin = {
        top: 60,
        bottom: 40,
        left: 70,
        right: 40
    };

    var width = w - margin.left - margin.right;
    var height = h - margin.top - margin.bottom;

      
    // define map projection
    var projection = d3.geoAlbersUsa()
        .translate([w/2, h/2])
        .scale([500]);

    //Define default path generator
    var path = d3.geoPath()
        .projection(projection);

    var svg = d3.select("#map-caseloads")
        .append("svg")
        .attr("id", "chart")
        .attr("width", w)
        .attr("height", h)
        .append("g")
        .attr("tranform", "translate(0" + margin.left + "," + margin.top + ")");

        var color = d3.scaleQuantile()
          .range(["rgb(237, 248, 233)", "rgb(186, 228, 179)", "rgb(116,196,118)", "rgb(49,163,84)", "rgb(0,109,44)"]);


          
        color.domain([ d3.min(data, function(d){ return d.pin; }),
          d3.max(data, function(d){ return d.pin; })
        ]);


   // $.getJSON("/maps/lcb_admbnda_adm1_ocha_database.json", function(json){
    d3.json("/maps/lcb_admbnda_adm1_ocha_database.json").then(function(json){ });
    d3.json("/maps/lcb_admbnda_adm1_ocha_database.json").then(function(json){    
        //Merge the agriculture and GeoJSON data
        //Loop through once for each agriculture data value

        mapData.features.forEach(function(feature) {
            json.geometry = turf.rewind(feature.geometry, {reverse:true});
        })

        for(var i = 0; i < data.length; i++){
          // grab state name
          var dataState = data[i].adminName;

          //grab data value, and convert from string to float
          var dataValue = parseFloat(data[i].pin);

          //find the corresponding state inside the GeoJSON
          for(var n = 0; n < json.features.length; n++){

            // properties name gets the states name
            var jsonState = json.features[n].properties.adminName;
            // if statment to merge by name of state
            if(dataState == jsonState){
              //Copy the data value into the JSON
              // basically creating a new value column in JSON data
              json.features[n].properties.value = dataValue;

              //stop looking through the JSON
              break;
            }
          }
        }


        //console.log(json)
        
        svg.selectAll("path")
          .data(json.features)
          .enter()
          .append("path")
          .attr("d", path)
          .style("fill", function(d){
            //get the data value
            var value = d.properties.value;
            
            if(value){
              //If value exists
              //console.log(color(value))
              return color(value);
            } else {
              // If value is undefined
              //we do this because alaska and hawaii are not in dataset we are using but still in projections
              return "#ccc"
            }

          });

        });




    


}

function AddCaseloadPinMap(data) {

    d3.json("/maps/lcb_admbnda_adm1_ocha_database.json").then(function(mapData){ 

    //reverse coodinates
    mapData.features.forEach(function(feature) {
        feature.geometry = turf.rewind(feature.geometry, {reverse:true});
    })

    for(var i = 0; i < data.length; i++){
        var dataState = data[i].adminName;
        var dataValue = parseFloat(data[i].value);

        //find the corresponding state inside the GeoJSON
        for(var n = 0; n < mapData.features.length; n++){

            // properties name gets the states name
            var jsonState = mapData.features[n].properties.adminName;
            // if statment to merge by name of state
            if(dataState == jsonState){
                //Copy the data value into the JSON
                // basically creating a new value column in JSON data
                mapData.features[n].properties.value = dataValue;
                break;
            }
        }
    }


    const countriesData = data;
    const mapContainer = d3.select('#map-caseloads');

    const mapRatio = 0.4;
    // The plus turns it into a number
    const width = 400;
    const height = 600;

    // Map and projection
    const projection = d3.geoMercator()
        .scale(200)
        .translate([width / 2, height / 2])
        .center([0, 0]);
  
    const pathBuilder = d3.geoPath(projection);

    var color = d3.scaleQuantile()
        .range(["rgb(212, 229, 247)", "rgb(130, 181, 233)", "rgb(65, 143, 222)", "rgb(31, 105, 179)", "rgb(20, 67, 114)"]);
   
    color.domain([ d3.min(data, function(d){ return d.value; }),
        d3.max(data, function(d){ return d.value; })
    ]);

  // The Tooltip
  const Tooltip = d3.select('body')
    .append('div')
    .attr('class', 'map-tooltip')
    .style('visibility', 'hidden')
    .style('background-color', 'white')
    .style('border', 'solid')
    .style('border-width', '1px')
    .style('border-radius', '1px')
    .style('padding', '5px')
    .style('position', 'absolute')
    .on('mouseover', (event) => {
      // A bug where if the user's cursor gets on top of the Tooltip, it flashes infinitely until the user's cursor moves
      // Very distracting and this gets rid of it completely. Besides, the cursor should never be over the Tooltip anyway
      Tooltip.style('visibility', 'hidden');
    });

  const zoom = d3.zoom()
    .on('zoom', (event) => {
      map.attr('transform', event.transform);
    })
    .scaleExtent([1, 40]);

    // The Map
    const map = mapContainer
        .append('svg')
        .attr('padding', 'none')
        .attr('height', height)
        .attr('width', width)
        .attr('border', '1px solid black')
        .attr('margin-left', '16px')
        .attr('preserveAspectRatio', 'xMinYMin meet')
        // This is for when you zoom on the background, it will zoom
        .call(zoom)
        // This is going to be the country group
        .append('g');

    map
        .selectAll('path')
        .data(mapData.features)
        .enter()
        // This will be the country appended
        .append('path')
        // Used for clearing out styling later
        .classed('country', true)
        // Used for selecting specific countries for styling
        .attr('id', (feature) => {
            return 'country' + feature.properties.OBJECTID;
        })
        // Simple stylings
        .attr('opacity', '.7')
        .attr('stroke', 'black')
        .attr('stroke-width', '.1px')
        .attr('d', (feature) => {
        // Using the projection, create the polygon for the country
        return pathBuilder(feature);
        })
        .attr('fill', (feature) => {
            return color(feature.properties.value);
        })
        // Events are given the event object and the feature object (AKA datum AKA d as it is usually shown in documentation)
        .on('mouseover', (event, feature) => {
        // This adds the styling to show the user they are hovering over the country
        d3.select('#country' + feature.properties.value)
            .transition()
            .duration(200)
            .attr('opacity', '1')
            .attr('stroke-width', '1px');
        // Show the Tooltip
        Tooltip.style('visibility', 'visible');
        })
    .on('mouseleave', (event, feature) => {
      // This clears out the remaining styles on all other countries not currently being hovered
      d3.selectAll('.country')
        .transition()
        .duration(200)
        .attr('opacity', '0.7')
        .attr('stroke-width', '.1px');
      // Hide the tooltip
      Tooltip.style('visibility', 'hidden');
    })
    .on('mousemove', (event, feature) => {
        Tooltip
          .html(feature.properties.adminName + '<br>' + 'Count: ' + feature.properties.value)
          .style('left', (event.x + 10) + 'px')
          .style('top', (event.y + 10) + 'px');
    
    });






    });
  
}

 //test

function legend({
    color,
    title,
    tickSize = 6,
    width = 320, 
    height = 44 + tickSize,
    marginTop = 18,
    marginRight = 0,
    marginBottom = 16 + tickSize,
    marginLeft = 0,
    ticks = width / 64,
    tickFormat,
    tickValues
    } = {}) {

  const svg = d3.create("svg")
      .attr("width", width)
      .attr("height", height)
      .attr("viewBox", [0, 0, width, height])
      .style("overflow", "visible")
      .style("display", "block");

  let tickAdjust = g => g.selectAll(".tick line").attr("y1", marginTop + marginBottom - height);
  let x;

  // Continuous
  if (color.interpolate) {
    const n = Math.min(color.domain().length, color.range().length);

    x = color.copy().rangeRound(d3.quantize(d3.interpolate(marginLeft, width - marginRight), n));

    svg.append("image")
        .attr("x", marginLeft)
        .attr("y", marginTop)
        .attr("width", width - marginLeft - marginRight)
        .attr("height", height - marginTop - marginBottom)
        .attr("preserveAspectRatio", "none")
        .attr("xlink:href", ramp(color.copy().domain(d3.quantize(d3.interpolate(0, 1), n))).toDataURL());
  }

  // Sequential
  else if (color.interpolator) {
    x = Object.assign(color.copy()
        .interpolator(d3.interpolateRound(marginLeft, width - marginRight)),
        {range() { return [marginLeft, width - marginRight]; }});

    svg.append("image")
        .attr("x", marginLeft)
        .attr("y", marginTop)
        .attr("width", width - marginLeft - marginRight)
        .attr("height", height - marginTop - marginBottom)
        .attr("preserveAspectRatio", "none")
        .attr("xlink:href", ramp(color.interpolator()).toDataURL());

    // scaleSequentialQuantile doesn’t implement ticks or tickFormat.
    if (!x.ticks) {
      if (tickValues === undefined) {
        const n = Math.round(ticks + 1);
        tickValues = d3.range(n).map(i => d3.quantile(color.domain(), i / (n - 1)));
      }
      if (typeof tickFormat !== "function") {
        tickFormat = d3.format(tickFormat === undefined ? ",f" : tickFormat);
      }
    }
  }

  // Threshold
  else if (color.invertExtent) {
    const thresholds
        = color.thresholds ? color.thresholds() // scaleQuantize
        : color.quantiles ? color.quantiles() // scaleQuantile
        : color.domain(); // scaleThreshold

    const thresholdFormat
        = tickFormat === undefined ? d => d
        : typeof tickFormat === "string" ? d3.format(tickFormat)
        : tickFormat;

    x = d3.scaleLinear()
        .domain([-1, color.range().length - 1])
        .rangeRound([marginLeft, width - marginRight]);

    svg.append("g")
      .selectAll("rect")
      .data(color.range())
      .join("rect")
        .attr("x", (d, i) => x(i - 1))
        .attr("y", marginTop)
        .attr("width", (d, i) => x(i) - x(i - 1))
        .attr("height", height - marginTop - marginBottom)
        .attr("fill", d => d);

    tickValues = d3.range(thresholds.length);
    tickFormat = i => thresholdFormat(thresholds[i], i);
  }

  // Ordinal
  else {
    x = d3.scaleBand()
        .domain(color.domain())
        .rangeRound([marginLeft, width - marginRight]);

    svg.append("g")
      .selectAll("rect")
      .data(color.domain())
      .join("rect")
        .attr("x", x)
        .attr("y", marginTop)
        .attr("width", Math.max(0, x.bandwidth() - 1))
        .attr("height", height - marginTop - marginBottom)
        .attr("fill", color);

    tickAdjust = () => {};
  }

  svg.append("g")
      .attr("transform", `translate(0,${height - marginBottom})`)
      .call(d3.axisBottom(x)
        .ticks(ticks, typeof tickFormat === "string" ? tickFormat : undefined)
        .tickFormat(typeof tickFormat === "function" ? tickFormat : undefined)
        .tickSize(tickSize)
        .tickValues(tickValues))
      .call(tickAdjust)
      .call(g => g.select(".domain").remove())
      .call(g => g.append("text")
        .attr("x", marginLeft)
        .attr("y", marginTop + marginBottom - height - 6)
        .attr("fill", "currentColor")
        .attr("text-anchor", "start")
        .attr("font-weight", "bold")
        .attr("class", "title")
        .text(title));

  return svg.node();
}

function swatches({
    color,
    columns = null,
    format = x => x,
    swatchSize = 15,
    swatchWidth = swatchSize,
    swatchHeight = swatchSize,
    marginLeft = 0
    }) {
  const id = DOM.uid().id;

  if (columns !== null) return html`<div style="display: flex; align-items: center; margin-left: ${+marginLeft}px; min-height: 33px; font: 10px sans-serif;">
  <style>

    .${id}-item {
    break-inside: avoid;
    display: flex;
    align-items: center;
    padding-bottom: 1px;
    }

    .${id}-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: calc(100% - ${+swatchWidth}px - 0.5em);
    }

    .${id}-swatch {
    width: ${+swatchWidth}px;
    height: ${+swatchHeight}px;
    margin: 0 0.5em 0 0;
    }

    </style>
    <div style="width: 100%; columns: ${columns};">${color.domain().map(value => {
        const label = format(value);
        return html`<div class="${id}-item">
        <div class="${id}-swatch" style="background:${color(value)};"></div>
        <div class="${id}-label" title="${label.replace(/["&]/g, entity)}">${document.createTextNode(label)}</div>
        </div>`;
    })}
    </div>
    </div>`;

    return html`<div style="display: flex; align-items: center; min-height: 33px; margin-left: ${+marginLeft}px; font: 10px sans-serif;">
    <style>

    .${id} {
    display: inline-flex;
    align-items: center;
    margin-right: 1em;
    }

    .${id}::before {
    content: "";
    width: ${+swatchWidth}px;
    height: ${+swatchHeight}px;
    margin-right: 0.5em;
    background: var(--color);
    }

    </style>
    <div>${color.domain().map(value => html`<span class="${id}" style="--color: ${color(value)}">${document.createTextNode(format(value))}</span>`)}</div>`;
}

function entity(character) {
  return `&#${character.charCodeAt(0).toString()};`;
}

function ramp(color, n = 256) {
  const canvas = DOM.canvas(n, 1);
  const context = canvas.getContext("2d");
  for (let i = 0; i < n; ++i) {
    context.fillStyle = color(i / (n - 1));
    context.fillRect(i, 0, 1, 1);
  }
  return canvas;
}
*/




</script>
    
@endsection