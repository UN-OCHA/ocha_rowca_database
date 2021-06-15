@extends('carousel')
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
?>


<div class='col'>
    <div class="row">
        <div class='col'>
            <p><em>Crisis zones in the <strong>West and Central Africa</strong></em>
            <br/>make <em><a href="/adavancedanalysis">Advanced analysis</a></em> of all the data</p>
            
        </div>
    </div>
    <div class="row   p-5">
        <div class="col cardList" style="display:none;">
            
            @foreach ($datas as $data)
                <h5><a href='/zone/{{$data["zone"]->zone_id}}'>{{$data["zone"]->zone_name}} <i class="bi bi-link-45deg"></i><a></h5>
                <?php
                    $liste_localites=$data["localites"];
                    $keyfigure_caseloads=$data["caseloads"];
                    $keyfigure_displacements=$data["displacements"];
                    $keyfigure_cadre_harmonises_projected=$data["cadre_harmonises_projected"];
                    $keyfigure_cadre_harmonises_current=$data["cadre_harmonises_current"];
                    $keyfigure_nutritions=$data["nutrition"];

                    $totalPop = 0;
                    $affectedPop = 0;
                    $pin = 0;
                    $pt = 0;
                    $pr = 0;

                    $idps = 0;
                    $refugees = 0;
                    $returnees = 0;

                    $chPhase5_projected = 0;
                    $chPhase4_projected = 0;
                    $chPhase3plus_projected = 0;
                    $chPhase3_projected = 0;
                    $chPhase2_projected = 0;
                    $chPhase1_projected = 0;

                    $chPhase5_current = 0;
                    $chPhase4_current = 0;
                    $chPhase3plus_current = 0;
                    $chPhase3_current = 0;
                    $chPhase2_current = 0;
                    $chPhase1_current = 0;

                    $sam = 0;
                    $mam = 0;
                    $gam = 0;

                    $idpAsOfDate = array();
                    $idpAsOfCountries = array();
                    $refAsOfDate = array();
                    $refAsOfCountries = array();
                    $retAsOfDate = array();
                    $retAsOfCountries = array();
                    $caseloadAsOfDate = array();
                    $caseloadAsOfCountries = array();
                    $nutritionAsOfDate = array();
                    $nutritionAsOfCountries = array();
                    $chCurrentAsOfDate = array();
                    $chCurrentAsOfCountries = array();
                    $chProjectedAsOfDate = array();
                    $chProjectedAsOfCountries = array();
                    $disSources = "";

                    
                    //caseloads
                    echo "<table id='tableCaseload".$data["zone"]->zone_code."' hidden='hidden'>";
                        echo "<tr>";
                            echo "<th>Admin0</th>";
                            echo "<th>local_name</th>";
                            echo "<th>local_pcode</th>";
                            echo "<th>caseload_total_population</th>";
                            echo "<th>caseload_people_affected</th>";
                            echo "<th>caseload_people_in_need</th>";
                            echo "<th>caseload_people_targeted</th>";
                            echo "<th>caseload_people_reached</th>";
                        echo "</tr>";

                    foreach ($keyfigure_caseloads as $keyfigure_caseload){
                        $totalPop += $keyfigure_caseload["caseload_total_population"];
                        $affectedPop += $keyfigure_caseload["caseload_people_affected"];
                        $pin += $keyfigure_caseload["caseload_people_in_need"];
                        $pt += $keyfigure_caseload["caseload_people_targeted"];
                        $pr += $keyfigure_caseload["caseload_people_reached"];

                        //AS OF DATES
                        $index = count($caseloadAsOfDate);
                        $search = array_search($keyfigure_caseload->caseload_date,$caseloadAsOfDate);
                        $isnew = false;
                        if($search ===false){
                            $isnew=true;
                        }else{
                            $index = $search;
                        }

                        if($isnew==true){
                            array_push($caseloadAsOfDate,$keyfigure_caseload->caseload_date);
                            array_push($caseloadAsOfCountries,$keyfigure_caseload->local_name);
                        }else{
                            if(stripos($caseloadAsOfCountries[$index],$keyfigure_caseload->local_name)===false){
                                $caseloadAsOfCountries[$index]=$caseloadAsOfCountries[$index].', '.$keyfigure_caseload->local_name;
                            }
                        }
                        //AS OF DATES END


                        //creation of the table
                        echo "<tr>";
                            echo "<td>".$keyfigure_caseload["admin0_pcode_iso3"]."</td>";
                            echo "<td>".$keyfigure_caseload["caseload_admin1_name"]."</td>";
                            echo "<td>".$keyfigure_caseload["local_pcode"]."</td>";
                            echo "<td>".$keyfigure_caseload["caseload_total_population"]."</td>";
                            echo "<td>".$keyfigure_caseload["caseload_people_affected"]."</td>";
                            echo "<td>".$keyfigure_caseload["caseload_people_in_need"]."</td>";
                            echo "<td>".$keyfigure_caseload["caseload_people_targeted"]."</td>";
                            echo "<td>".$keyfigure_caseload["caseload_people_reached"]."</td>";
                        echo "</tr>";

                    }
                    echo "</table>";
                    

                    //displacements

                    echo "<table id='tableDispl".$data["zone"]->zone_code."'  hidden='hidden'>";
                        echo "<tr>";
                            echo "<th>Admin0</th>";
                            echo "<th>local_name</th>";
                            echo "<th>local_pcode</th>";
                            echo "<th>dis_type</th>";
                            echo "<th>dis_value</th>";
                            echo "<th>dis_source</th>";
                            echo "<th>dis_date</th>";
                        echo "</tr>";
                    foreach ($keyfigure_displacements as $keyfigure_displacement){
                        switch ($keyfigure_displacement->dis_type){
                            case "IDP":
                                $idps += $keyfigure_displacement->dis_value;

                                //AS OF DATES
                                $index = count($idpAsOfDate);
                                $search = array_search($keyfigure_displacement->dis_date,$idpAsOfDate);
                                $isnew = false;
                                if($search ===false){
                                    $isnew=true;
                                }else{
                                    $index = $search;
                                }

                                if($isnew==true){
                                    array_push($idpAsOfDate,$keyfigure_displacement->dis_date);
                                    array_push($idpAsOfCountries,$keyfigure_displacement->local_name);
                                }else{
                                    if(stripos($idpAsOfCountries[$index],$keyfigure_displacement->local_name)===false){
                                        $idpAsOfCountries[$index]=$idpAsOfCountries[$index].', '.$keyfigure_displacement->local_name;
                                    }
                                }
                                //AS OF DATES END
                            break;
                            case "Returnee":
                                $returnees += $keyfigure_displacement->dis_value;

                                //AS OF DATES
                                $index = count($retAsOfDate);
                                $search = array_search($keyfigure_displacement->dis_date,$retAsOfDate);
                                $isnew = false;
                                if($search ===false){
                                    $isnew=true;
                                }else{
                                    $index = $search;
                                }

                                if($isnew==true){
                                    array_push($retAsOfDate,$keyfigure_displacement->dis_date);
                                    array_push($retAsOfCountries,$keyfigure_displacement->local_name);
                                }else{
                                    if(stripos($retAsOfCountries[$index],$keyfigure_displacement->local_name)===false){
                                        $retAsOfCountries[$index]=$retAsOfCountries[$index].', '.$keyfigure_displacement->local_name;
                                    }
                                }
                                //AS OF DATES END
                            break;
                            case "Refugee":
                                $refugees += $keyfigure_displacement->dis_value;

                                //AS OF DATES
                                $index = count($refAsOfDate);
                                $search = array_search($keyfigure_displacement->dis_date,$refAsOfDate);
                                $isnew = false;
                                if($search ===false){
                                    $isnew=true;
                                }else{
                                    $index = $search;
                                }

                                if($isnew==true){
                                    array_push($refAsOfDate,$keyfigure_displacement->dis_date);
                                    array_push($refAsOfCountries,$keyfigure_displacement->local_name);
                                }else{
                                    if(stripos($refAsOfCountries[$index],$keyfigure_displacement->local_name)===false){
                                        $refAsOfCountries[$index]=$refAsOfCountries[$index].', '.$keyfigure_displacement->local_name;
                                    }
                                }
                                //AS OF DATES END
                            break;
                        }

                        //GESTION DES SOURCES
                        if(stripos($disSources,$keyfigure_displacement->dis_source)===false){
                            $disSources=$disSources.', '.$keyfigure_displacement->dis_source;
                        }

                        //creation of the table
                        echo "<tr>";
                            echo "<td>".$keyfigure_displacement->dis_admin0_pcode."</td>";
                            echo "<td>".$keyfigure_displacement->dis_admin1_name."</td>";
                            echo "<td>".$keyfigure_displacement->local_pcode."</td>";
                            echo "<td>".$keyfigure_displacement->dis_type."</td>";
                            echo "<td>".$keyfigure_displacement->dis_value."</td>";
                            echo "<td>".$keyfigure_displacement->dis_source."</td>";
                            echo "<td>".$keyfigure_displacement->dis_date."</td>";
                        echo "</tr>";

                    }
                    echo "</table>";

                    //cadre harmonise projected
                    echo "<table id='tableCH".$data["zone"]->zone_code."'  hidden='hidden'>";
                        echo "<tr>";
                            echo "<th>ch_country</th>";
                            echo "<th>ch_admin1_name</th>";
                            echo "<th>ch_admin1_pcode_iso3</th>";
                            echo "<th>ch_ipc_level</th>";
                            echo "<th>ch_phase1</th>";
                            echo "<th>ch_phase2</th>";
                            echo "<th>ch_phase3</th>";
                            echo "<th>ch_phase4</th>";
                            echo "<th>ch_phase5</th>";
                            echo "<th>ch_phase35</th>";
                            echo "<th>ch_exercise_month</th>";
                            echo "<th>ch_exercise_year</th>";
                            echo "<th>ch_situation</th>";
                        echo "</tr>";
                    foreach ($keyfigure_cadre_harmonises_projected as $keyfigure_cadre_harmonise){
                        $chPhase5_projected += $keyfigure_cadre_harmonise->ch_phase5;
                        $chPhase4_projected += $keyfigure_cadre_harmonise->ch_phase4;
                        $chPhase3plus_projected += $keyfigure_cadre_harmonise->ch_phase35;
                        $chPhase3_projected += $keyfigure_cadre_harmonise->ch_phase3;
                        $chPhase2_projected += $keyfigure_cadre_harmonise->ch_phase2;
                        $chPhase1_projected += $keyfigure_cadre_harmonise->ch_phase1;

                        //AS OF DATES
                            $index = count($chProjectedAsOfDate);
                            $search = array_search($keyfigure_cadre_harmonise->ch_exercise_month." ".$keyfigure_cadre_harmonise->ch_exercise_year,$chProjectedAsOfDate);
                            $isnew = false;
                            if($search ===false){
                                $isnew=true;
                            }else{
                                $index = $search;
                            }
                            
                            if($isnew==true){
                                array_push($chProjectedAsOfDate,$keyfigure_cadre_harmonise->ch_exercise_month." ".$keyfigure_cadre_harmonise->ch_exercise_year);
                                array_push($chProjectedAsOfCountries,$keyfigure_cadre_harmonise->local_name);
                            }else{
                                if(stripos($chProjectedAsOfCountries[$index],$keyfigure_cadre_harmonise->local_name)===false){
                                    $chProjectedAsOfCountries[$index]=$chProjectedAsOfCountries[$index].', '.$keyfigure_cadre_harmonise->local_name;
                                }
                            }
                        //AS OF DATES END

                         //creation of the table
                         echo "<tr>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_country."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_admin1_name."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_admin1_pcode_iso3."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_ipc_level."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase1."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase2."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase3."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase4."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase5."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase35."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_exercise_month."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_exercise_year."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_situation."</td>";
                        echo "</tr>";
                    }
                   


                    //cadre harmonise current
                    foreach ($keyfigure_cadre_harmonises_current as $keyfigure_cadre_harmonise){
                        $chPhase5_current += $keyfigure_cadre_harmonise->ch_phase5;
                        $chPhase4_current += $keyfigure_cadre_harmonise->ch_phase4;
                        $chPhase3plus_current += $keyfigure_cadre_harmonise->ch_phase35;
                        $chPhase3_current += $keyfigure_cadre_harmonise->ch_phase3;
                        $chPhase2_current += $keyfigure_cadre_harmonise->ch_phase2;
                        $chPhase1_current += $keyfigure_cadre_harmonise->ch_phase1;


                        //AS OF DATES
                            $index = count($chCurrentAsOfDate);
                            $search = array_search($keyfigure_cadre_harmonise->ch_exercise_month." ".$keyfigure_cadre_harmonise->ch_exercise_year,$chCurrentAsOfDate);
                            $isnew = false;
                            if($search ===false){
                                $isnew=true;
                            }else{
                                $index = $search;
                            }

                            if($isnew==true){
                                array_push($chCurrentAsOfDate,$keyfigure_cadre_harmonise->ch_exercise_month." ".$keyfigure_cadre_harmonise->ch_exercise_year);
                                array_push($chCurrentAsOfCountries,$keyfigure_cadre_harmonise->local_name);
                            }else{
                                if(stripos($chCurrentAsOfCountries[$index],$keyfigure_cadre_harmonise->local_name)===false){
                                    $chCurrentAsOfCountries[$index]=$chCurrentAsOfCountries[$index].', '.$keyfigure_cadre_harmonise->local_name;
                                }
                            }
                        //AS OF DATES END
                         //creation of the table
                         echo "<tr>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_country."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_admin1_name."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_admin1_pcode_iso3."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_ipc_level."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase1."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase2."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase3."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase4."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase5."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_phase35."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_exercise_month."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_exercise_year."</td>";
                            echo "<td>".$keyfigure_cadre_harmonise->ch_situation."</td>";
                        echo "</tr>";
                    }
                    echo "</table>";


                    //nutrition
                    echo "<table id='tableNut".$data["zone"]->zone_code."'  hidden='hidden'>";
                        echo "<tr>";
                            echo "<th>nut_country</th>";
                            echo "<th>nut_admin1</th>";
                            echo "<th>nut_admin1_pcode</th>";
                            echo "<th>nut_sam</th>";
                            echo "<th>nut_gam</th>";
                            echo "<th>nut_mam</th>";
                            echo "<th>nut_date</th>";
                        echo "</tr>";

                    foreach ($keyfigure_nutritions as $keyfigure_nutrition){
                        $sam += $keyfigure_nutrition->nut_sam;
                        $mam += $keyfigure_nutrition->nut_mam;
                        $gam += $keyfigure_nutrition->nut_gam;

                        //AS OF DATES
                            $index = count($nutritionAsOfDate);
                            $search = array_search($keyfigure_nutrition->nut_date,$nutritionAsOfDate);
                            $isnew = false;
                            if($search ===false){
                                $isnew=true;
                            }else{
                                $index = $search;
                            }
                    
                            if($isnew==true){
                                array_push($nutritionAsOfDate,$keyfigure_nutrition->nut_date);
                                array_push($nutritionAsOfCountries,$keyfigure_nutrition->local_name);
                            }else{
                                if(stripos($nutritionAsOfCountries[$index],$keyfigure_nutrition->local_name)===false){
                                    $nutritionAsOfCountries[$index]=$nutritionAsOfCountries[$index].', '.$keyfigure_nutrition->local_name;
                                }
                            }
                        //AS OF DATES END

                        //creation of the table
                        echo "<tr>";
                            echo "<td>".$keyfigure_nutrition->nut_country."</td>";
                            echo "<td>".$keyfigure_nutrition->nut_admin1."</td>";
                            echo "<td>".$keyfigure_nutrition->nut_admin1_pcode."</td>";
                            echo "<td>".$keyfigure_nutrition->nut_sam."</td>";
                            echo "<td>".$keyfigure_nutrition->nut_gam."</td>";
                            echo "<td>".$keyfigure_nutrition->nut_mam."</td>";
                            echo "<td>".$keyfigure_nutrition->nut_date."</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                ?>

                <div class="row cartes mb-5">
                    <div class='col carte bg-white shadow-sm p-3 ml-3 mr-3 bg-white rounded'>
                        <div class="row border-bottom pb-2">
                            <div class="col-12">
                                <img src="{{asset('images/Food-Security.png')}}" style="height:25px;" class="d-inline">
                                <span class="position-absolute" style="right:10px;top:-10px;" >
                                    <i class="bi bi-download downloadImage" alt="Download Excel data" onclick="download('tableCH{{$data['zone']->zone_code}}','Cadre_harmonisé_{{$data['zone']->zone_code}}')" data-toggle="tooltip" data-placement="top" title="Download Excel data"></i>
                                </span>
                            </div>
                            <div class="col-12 mt-1 font-weight-bolder">
                                Cadre harmonisé 
                            </div>
                        </div>

                        <div class="row">
                            <div class='col'>
                                <br/>
                                Phase 5<br/>
                                Phase 4<br/>
                                Phase 3+<br/>
                                Phase 3<br/>
                                Phase 2<br/>
                                Phase 1
                            </div>
                            <div class='col'>
                                Current<br/>
                                <strong>{{convertToUnit($chPhase5_current,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase4_current,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase3plus_current,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase3_current,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase2_current,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase1_current,1)}}</strong>
                            </div>
                            <div class='col'>
                                Projected<br/>
                                <strong>{{convertToUnit($chPhase5_projected,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase4_projected,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase3plus_projected,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase3_projected,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase2_projected,1)}}</strong><br/>
                                <strong>{{convertToUnit($chPhase1_projected,1)}}</strong><br/><br/>
                            </div>
                        </div>

                        <div class="row">
                            <div class='col-12 d-flex justify-content-center'>
                                <img id="arrowDown_disclamerCH{{$data['zone']->zone_code}}" onclick="showDisclamer('disclamerCH{{$data['zone']->zone_code}}')" src="{{asset('bootstrap-icons/chevron-compact-down.svg')}}" fill="red" style="height:25px;" >
                                <img id="arrowUp_disclamerCH{{$data['zone']->zone_code}}" onclick="hideDisclamer('disclamerCH{{$data['zone']->zone_code}}')" src="{{asset('bootstrap-icons/chevron-compact-up.svg')}}" fill="red" style="height:25px;display:none;" >
                            </div>

                            <div class='col-12' style="display:none;" id="disclamerCH{{$data['zone']->zone_code}}">
                                @if (count($chCurrentAsOfDate) > 0)
                                    <footer class="blockquote-footer"><cite title="Source Title"> Current: 
                                        <?php
                                            for ($x = 0; $x < count($chCurrentAsOfDate); $x++) {
                                                echo $chCurrentAsOfDate[$x]." (".$chCurrentAsOfCountries[$x].") ";
                                            }
                                        ?>
                                    </cite></footer>
                                @endif
                                @if (count($chProjectedAsOfDate) > 0)
                                    <footer class="blockquote-footer"><cite title="Source Title"> Projected: 
                                        <?php
                                            for ($x = 0; $x < count($chProjectedAsOfDate); $x++) {
                                                echo $chProjectedAsOfDate[$x]." (".$chProjectedAsOfCountries[$x].") ";
                                            }
                                        ?>
                                    </cite></footer>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class='col carte bg-white shadow-sm p-3 ml-3 mr-3 bg-white rounded'>
                        <div class="row border-bottom pb-2">
                            <div class="col-12">
                                <img src="{{asset('images/People-in-need.png')}}" style="height:25px;" class="d-inline">
                                <span class="position-absolute" style="right:10px;top:-10px;" >
                                    <i class="bi bi-download downloadImage" alt="Download Excel data" onclick="download('tableCaseload{{$data['zone']->zone_code}}','Caseloads_{{$data['zone']->zone_code}}')" data-toggle="tooltip" data-placement="top" title="Download Excel data"></i>
                                </span>
                            </div>
                            <div class="col-12 mt-1 font-weight-bolder">
                                Caseloads 
                            </div>
                        </div>
            
                        <div class="row">
                            <div class='col'>
                                Total population : <strong>{{convertToUnit($totalPop,1)}}</strong><br/>
                                Affected people : <strong>{{convertToUnit($affectedPop,1)}}</strong><br/>
                                People in need : <strong>{{convertToUnit($pin,1)}}</strong><br/>
                                People targeted : <strong>{{convertToUnit($pt,1)}}</strong><br/>
                                People reached : <strong>{{convertToUnit($pr,1)}}</strong><br/><br/>
                            </div>
                        </div>
                        <div class="row">
                            <div class='col-12 d-flex justify-content-center'>
                                <img id="arrowDown_disclamerCL{{$data['zone']->zone_code}}" onclick="showDisclamer('disclamerCL{{$data['zone']->zone_code}}')" src="{{asset('bootstrap-icons/chevron-compact-down.svg')}}" fill="red" style="height:25px;" >
                                <img id="arrowUp_disclamerCL{{$data['zone']->zone_code}}" onclick="hideDisclamer('disclamerCL{{$data['zone']->zone_code}}')" src="{{asset('bootstrap-icons/chevron-compact-up.svg')}}" fill="red" style="height:25px;display:none;" >
                            </div>

                            <div class='col-12' style="display:none;" id="disclamerCL{{$data['zone']->zone_code}}">
                                @if (count($caseloadAsOfDate) > 0)
                                    <footer class="blockquote-footer"><cite title="Source Title"> 
                                        <?php
                                            for ($x = 0; $x < count($caseloadAsOfDate); $x++) {
                                                echo $caseloadAsOfDate[$x]." (".$caseloadAsOfCountries[$x].") ";
                                            }
                                        ?>
                                    </cite></footer>
                                @endif
                            </div>
                        </div>
                        

                        
                    </div>

                    <div class='col carte bg-white shadow-sm p-3 ml-3 mr-3 bg-white rounded'>
                        <div class="row border-bottom pb-2">
                            <div class="col-12">
                                <img src="{{asset('images/Internally-displaced.png')}}" style="height:25px;" class="d-inline">
                                <span class="position-absolute" style="right:10px;top:-10px;" >
                                    <i class="bi bi-download downloadImage" alt="Download Excel data" onclick="download('tableDispl{{$data['zone']->zone_code}}','Displacements_{{$data['zone']->zone_code}}')" data-toggle="tooltip" data-placement="top" title="Download Excel data"></i>
                                </span>
                            </div>
                            <div class="col-12 mt-1 font-weight-bolder">
                            Displacements 
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class='col'>
                                Internally displaced persons : <strong>{{convertToUnit($idps,1)}}</strong><br/>
                                Refugees : <strong>{{convertToUnit($refugees,1)}}</strong><br/>
                                Returnees : <strong>{{convertToUnit($returnees,1)}}</strong><br/><br/>
                            </div>
                        </div>
                        <div class="row">
                            <div class='col-12 d-flex justify-content-center'>
                                <img id="arrowDown_disclamerIDP{{$data['zone']->zone_code}}" onclick="showDisclamer('disclamerIDP{{$data['zone']->zone_code}}')" src="{{asset('bootstrap-icons/chevron-compact-down.svg')}}" fill="red" style="height:25px;" >
                                <img id="arrowUp_disclamerIDP{{$data['zone']->zone_code}}" onclick="hideDisclamer('disclamerIDP{{$data['zone']->zone_code}}')" src="{{asset('bootstrap-icons/chevron-compact-up.svg')}}" fill="red" style="height:25px;display:none;" >
                            </div>

                            <div class='col-12' style="display:none;" id="disclamerIDP{{$data['zone']->zone_code}}">
                                @if (count($idpAsOfDate) > 0)
                                    <footer class="blockquote-footer"><cite title="Source Title"> IDPs: 
                                        <?php
                                            for ($x = 0; $x < count($idpAsOfDate); $x++) {
                                                echo $idpAsOfDate[$x]." (".$idpAsOfCountries[$x].") ";
                                            }
                                        ?>
                                    </cite></footer>
                                @endif

                                @if (count($refAsOfDate) > 0)
                                    <footer class="blockquote-footer"><cite title="Source Title"> Refugees: 
                                        <?php
                                            for ($x = 0; $x < count($refAsOfDate); $x++) {
                                                echo $refAsOfDate[$x]." (".$refAsOfCountries[$x].") ";
                                            }
                                        ?>
                                    </cite></footer>
                                @endif
                                
                                @if (count($retAsOfDate) > 0)
                                    <footer class="blockquote-footer"><cite title="Source Title"> Returnees: 
                                        <?php
                                            for ($x = 0; $x < count($retAsOfDate); $x++) {
                                                echo $retAsOfDate[$x]." (".$retAsOfCountries[$x].") ";
                                            }
                                        ?>
                                    </cite></footer>
                                @endif
                                <footer class="blockquote-footer"><cite title="Source Title"> Sources : {{$disSources}} </cite></footer>
                            </div>
                        </div>

                        
                    
                    </div>

                    <div class='col carte bg-white shadow-sm p-3 ml-3 mr-3 bg-white rounded'>
                        <div class="row border-bottom pb-2">
                            <div class="col-12">
                                <img src="{{asset('images/Nutrition.png')}}" style="height:25px;" class="d-inline">
                                <span class="position-absolute" style="right:10px;top:-10px;" >
                                    <i class="bi bi-download downloadImage" alt="Download Excel data" onclick="download('tableNut{{$data['zone']->zone_code}}','Nutrition_{{$data['zone']->zone_code}}')" data-toggle="tooltip" data-placement="top" title="Download Excel data"></i>
                                </span>
                            </div>
                            <div class="col-12 mt-1 font-weight-bolder">
                            Nutrition 
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class='col'>
                                SAM : <strong>{{convertToUnit($sam,1)}}</strong><br/>
                                MAM : <strong>{{convertToUnit($mam,1)}}</strong><br/>
                                GAM : <strong>{{convertToUnit($gam,1)}}</strong><br/><br/>
                            </div>
                        </div>
                        <div class="row">
                            <div class='col-12 d-flex justify-content-center'>
                                <img id="arrowDown_disclamerNT{{$data['zone']->zone_code}}" onclick="showDisclamer('disclamerNT{{$data['zone']->zone_code}}')" src="{{asset('bootstrap-icons/chevron-compact-down.svg')}}" fill="red" style="height:25px;" >
                                <img id="arrowUp_disclamerNT{{$data['zone']->zone_code}}" onclick="hideDisclamer('disclamerNT{{$data['zone']->zone_code}}')" src="{{asset('bootstrap-icons/chevron-compact-up.svg')}}" fill="red" style="height:25px;display:none;" >
                            </div>

                            <div class='col-12' style="display:none;" id="disclamerNT{{$data['zone']->zone_code}}">
                                @if (count($nutritionAsOfDate) > 0)
                                    <footer class="blockquote-footer"><cite title="Source Title"> 
                                        <?php
                                            for ($x = 0; $x < count($nutritionAsOfDate); $x++) {
                                                echo $nutritionAsOfDate[$x]." (".$nutritionAsOfCountries[$x].") ";
                                            }
                                        ?>
                                    </cite></footer>
                                @endif
                            </div>
                        </div>

                        
                    </div>
                </div>

            @endforeach
        </div>

    </div>
</div>

<script>
$(document).ready(function(){
  
    $('[data-toggle="tooltip"]').tooltip();

    if($( document ).width()<500){
        $('.cartes').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1
        });
    }else{
        if($( document ).width()<1000){
            $('.cartes').slick({
            infinite: true,
            slidesToShow: 2,
            slidesToScroll: 2
            });
        }else{
            if($( document ).width()<1500){
                $('.cartes').slick({
                infinite: true,
                slidesToShow: 3,
                slidesToScroll: 3
                });
            }else{
                $('.cartes').slick({
                infinite: true,
                slidesToShow: 4,
                slidesToScroll: 4
                });
            }
        }
    }

    $(".cardList").show();

    $winWidth = $(window).width();

    $( window ).resize(function() {
        if($winWidth != $(window).width()){
            $(".cardList").hide();
            location.reload();
        }
        
    });
});

function showDisclamer(disclaimer){
    
    $("#arrowDown_"+disclaimer).hide();
    $("#arrowUp_"+disclaimer).show();
    $("#"+disclaimer).show();
}

function hideDisclamer(disclaimer){
    $("#arrowUp_"+disclaimer).hide();
    $("#arrowDown_"+disclaimer).show();
    $("#"+disclaimer).hide();
}

function download(tableName,typeData){
   /* $("#"+table).table2excel({
        exclude:".noExl",
        name:typeData,
        filename:typeData,//do not include extension
        fileext:".xls" // file extension
    });*/
    var table = TableExport(document.getElementById(tableName));

    
    var exportData = table.getExportData(); 
    console.log(exportData);
    var xlsxData = exportData[tableName].xlsx; 
    console.log(xlsxData);
    // Replace with the kind of file you want from the exportData
    //table.export2file(xlsxData.data, xlsxData.mimeType, xlsxData.filename, xlsxData.fileExtension, xlsxData.merges, xlsxData.RTL, xlsxData.sheetname)
    table.export2file(xlsxData.data, xlsxData.mimeType, typeData, xlsxData.fileExtension, xlsxData.merges, xlsxData.RTL, typeData)

}
	
</script>
    
@endsection