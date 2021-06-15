@extends('filter')
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


<div class='col-12'>
    <div class="row">
        <div class="col" >
            <ul class="nav nav-pills  justify-content-center">
                @foreach ($datas as $data)
                    <li class="nav-item">
                        <a class="nav-link crisisTitle" href="#" id='crisisTitle_{{$data["zone"]->zone_code}}' onclick='showBloc("{{$data["zone"]->zone_code}}")'>{{$data["zone"]->zone_name}}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div> 
</div> 
<div class='col-12' style='top:100px;'>
    <div class="row justify-content-center" id='loading'>
        <div class="col d-flex justify-content-center">
            <img src="{{asset('images/loading.svg')}}" style="height:50px;width:50px;"  alt="loading"/>
        </div>
    </div>
    
        @foreach ($datas as $data)
        <div class="row bloc_crise" id='bloc_{{$data["zone"]->zone_code}}' style='display:none;'>
            <div class="col" >


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

                $chTable = array();
                $chCountries = array();
                
            ?>
            
            <div class="row">
                <?php
                    foreach ($keyfigure_cadre_harmonises_projected as $keyfigure_cadre_harmonise){
                        $chPhase5_projected += $keyfigure_cadre_harmonise->ch_phase5;
                        $chPhase4_projected += $keyfigure_cadre_harmonise->ch_phase4;
                        $chPhase3plus_projected += $keyfigure_cadre_harmonise->ch_phase35;
                        $chPhase3_projected += $keyfigure_cadre_harmonise->ch_phase3;
                        $chPhase2_projected += $keyfigure_cadre_harmonise->ch_phase2;
                        $chPhase1_projected += $keyfigure_cadre_harmonise->ch_phase1;

                        //Alimentation du tableau pour le detail par pays
                        $search = array_search($keyfigure_cadre_harmonise->ch_country.$keyfigure_cadre_harmonise->ch_situation,$chCountries);
                        if($search ===false){
                            array_push($chCountries,$keyfigure_cadre_harmonise->ch_country.$keyfigure_cadre_harmonise->ch_situation);
                            array_push(
                                $chTable,
                                [
                                    $keyfigure_cadre_harmonise->ch_country,
                                    $keyfigure_cadre_harmonise->ch_phase1,
                                    $keyfigure_cadre_harmonise->ch_phase2,
                                    $keyfigure_cadre_harmonise->ch_phase3,
                                    $keyfigure_cadre_harmonise->ch_phase4,
                                    $keyfigure_cadre_harmonise->ch_phase5,
                                    $keyfigure_cadre_harmonise->ch_phase35,
                                    $keyfigure_cadre_harmonise->ch_exercise_month,
                                    $keyfigure_cadre_harmonise->ch_exercise_year,
                                    $keyfigure_cadre_harmonise->ch_situation
                                ]
                            );
                        }else{
                            $tempArray = array();
                            array_push($tempArray,
                                $chTable[$search][0],
                                ($chTable[$search][1] + $keyfigure_cadre_harmonise->ch_phase1),
                                ($chTable[$search][2] + $keyfigure_cadre_harmonise->ch_phase2),
                                ($chTable[$search][3] + $keyfigure_cadre_harmonise->ch_phase3),
                                ($chTable[$search][4] + $keyfigure_cadre_harmonise->ch_phase4),
                                ($chTable[$search][5] + $keyfigure_cadre_harmonise->ch_phase5),
                                ($chTable[$search][6] + $keyfigure_cadre_harmonise->ch_phase35),
                                $chTable[$search][7],
                                $chTable[$search][8],
                                $chTable[$search][9]
                            );
                            $chTable[$search] = $tempArray;
                        }
                        //FIN Alimentation du tableau pour le detail par pays

                        //AS OF DATES
                            $index = count($chProjectedAsOfDate);
                            $search = array_search($keyfigure_cadre_harmonise->ch_exercise_month." ".$keyfigure_cadre_harmonise->ch_exercise_year,$chProjectedAsOfDate);
                            $isnew = false;
                            if($search ===false){
                                array_push($chProjectedAsOfDate,$keyfigure_cadre_harmonise->ch_exercise_month." ".$keyfigure_cadre_harmonise->ch_exercise_year);
                                array_push($chProjectedAsOfCountries,$keyfigure_cadre_harmonise->local_name);
                            }else{
                                $index = $search;
                                if(stripos($chProjectedAsOfCountries[$index],$keyfigure_cadre_harmonise->local_name)===false){
                                    $chProjectedAsOfCountries[$index]=$chProjectedAsOfCountries[$index].', '.$keyfigure_cadre_harmonise->local_name;
                                }
                            }
                        //AS OF DATES END
                    }

                    //cadre harmonise current
                    foreach ($keyfigure_cadre_harmonises_current as $keyfigure_cadre_harmonise){
                        $chPhase5_current += $keyfigure_cadre_harmonise->ch_phase5;
                        $chPhase4_current += $keyfigure_cadre_harmonise->ch_phase4;
                        $chPhase3plus_current += $keyfigure_cadre_harmonise->ch_phase35;
                        $chPhase3_current += $keyfigure_cadre_harmonise->ch_phase3;
                        $chPhase2_current += $keyfigure_cadre_harmonise->ch_phase2;
                        $chPhase1_current += $keyfigure_cadre_harmonise->ch_phase1;

                        //Alimentation du tableau pour le detail par pays
                        $search = array_search($keyfigure_cadre_harmonise->ch_country.$keyfigure_cadre_harmonise->ch_situation,$chCountries);
                        if($search ===false){
                            array_push($chCountries,$keyfigure_cadre_harmonise->ch_country.$keyfigure_cadre_harmonise->ch_situation);
                            array_push(
                                $chTable,
                                [
                                    $keyfigure_cadre_harmonise->ch_country,
                                    $keyfigure_cadre_harmonise->ch_phase1,
                                    $keyfigure_cadre_harmonise->ch_phase2,
                                    $keyfigure_cadre_harmonise->ch_phase3,
                                    $keyfigure_cadre_harmonise->ch_phase4,
                                    $keyfigure_cadre_harmonise->ch_phase5,
                                    $keyfigure_cadre_harmonise->ch_phase35,
                                    $keyfigure_cadre_harmonise->ch_exercise_month,
                                    $keyfigure_cadre_harmonise->ch_exercise_year,
                                    $keyfigure_cadre_harmonise->ch_situation
                                ]
                            );
                        }else{
                            $tempArray = array();
                            array_push($tempArray,
                                $chTable[$search][0],
                                ($chTable[$search][1] + $keyfigure_cadre_harmonise->ch_phase1),
                                ($chTable[$search][2] + $keyfigure_cadre_harmonise->ch_phase2),
                                ($chTable[$search][3] + $keyfigure_cadre_harmonise->ch_phase3),
                                ($chTable[$search][4] + $keyfigure_cadre_harmonise->ch_phase4),
                                ($chTable[$search][5] + $keyfigure_cadre_harmonise->ch_phase5),
                                ($chTable[$search][6] + $keyfigure_cadre_harmonise->ch_phase35),
                                $chTable[$search][7],
                                $chTable[$search][8],
                                $chTable[$search][9]
                            );
                            $chTable[$search] = $tempArray;
                        }
                        //fin Alimentation du tableau pour le detail par pays

                        //AS OF DATES
                            $index = count($chCurrentAsOfDate);
                            $search = array_search($keyfigure_cadre_harmonise->ch_exercise_month." ".$keyfigure_cadre_harmonise->ch_exercise_year,$chCurrentAsOfDate);
                            $isnew = false;
                            if($search ===false){
                                array_push($chCurrentAsOfDate,$keyfigure_cadre_harmonise->ch_exercise_month." ".$keyfigure_cadre_harmonise->ch_exercise_year);
                                array_push($chCurrentAsOfCountries,$keyfigure_cadre_harmonise->local_name);
                            }else{
                                $index = $search;
                                if(stripos($chCurrentAsOfCountries[$index],$keyfigure_cadre_harmonise->local_name)===false){
                                    $chCurrentAsOfCountries[$index]=$chCurrentAsOfCountries[$index].', '.$keyfigure_cadre_harmonise->local_name;
                                }
                            }
                        //AS OF DATES END
                    }
                ?>
                <div class="col">
                    <div class="row "><h5>Cadre harmonisé </h5></div>
                    <div class="row">
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary">
                            Phase 1<br/>
                            Current : {{convertToUnit($chPhase1_current,1)}}<br/>
                            Projected : {{convertToUnit($chPhase1_projected,1)}}
                        </div>
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary" >
                            Phase 2<br/>
                            Current : {{convertToUnit($chPhase2_current,1)}}<br/>
                            Projected : {{convertToUnit($chPhase2_projected,1)}}
                        </div>
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary">
                            Phase 3<br/>
                            Current : {{convertToUnit($chPhase3_current,1)}}<br/>
                            Projected : {{convertToUnit($chPhase3_projected,1)}}
                        </div>
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary">
                            Phase 3+<br/>
                            Current : {{convertToUnit($chPhase3plus_current,1)}}<br/>
                            Projected : {{convertToUnit($chPhase3plus_projected,1)}}
                        </div>
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary">
                            Phase 4<br/>
                            Current : {{convertToUnit($chPhase4_current,1)}}<br/>
                            Projected : {{convertToUnit($chPhase4_projected,1)}}
                        </div>
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondarys">
                            Phase 5<br/>
                            Current : {{convertToUnit($chPhase5_current,1)}}<br/>
                            Projected : {{convertToUnit($chPhase5_projected,1)}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <figcaption class="blockquote-footer">
                            <cite title="Source Title">
                            @if (count($chCurrentAsOfDate) > 0)
                                Current: 
                                    <?php
                                        for ($x = 0; $x < count($chCurrentAsOfDate); $x++) {
                                            echo $chCurrentAsOfDate[$x]." (".$chCurrentAsOfCountries[$x].") ";
                                        }
                                    ?>
                            @endif
                            @if (count($chProjectedAsOfDate) > 0)
                                    , Projected: 
                                    <?php
                                        for ($x = 0; $x < count($chProjectedAsOfDate); $x++) {
                                            echo $chProjectedAsOfDate[$x]." (".$chProjectedAsOfCountries[$x].") ";
                                        }
                                    ?>
                                
                            @endif
                            </figcaption>
                        </div>
                    </div>
                    
                    <div class='col-12 d-flex justify-content-center border-bottom'>
                        <i id="arrowDown_tableCH{{$data['zone']->zone_code}}" class="bi bi-chevron-compact-down" style="font-size: 2rem; color: #418fde;" onclick="showTable('tableCH{{$data['zone']->zone_code}}')"></i>
                        <i id="arrowUp_tableCH{{$data['zone']->zone_code}}" class="bi bi-chevron-compact-up" style="font-size: 2rem; color: #418fde;display:none;" onclick="hideTable('tableCH{{$data['zone']->zone_code}}')"></i>
                    </div>

                    <div class="row" id="tableCH{{$data['zone']->zone_code}}" style="display:none;">
                        <div class="col">
                            <?php
                                //cadre harmonise projected and current
                                echo "<table class='table table-dark table-hover' id='tableCH".$data["zone"]->zone_code."'>";
                                    echo "<tr>";
                                        echo "<th>Country</th>";
                                        echo "<th style='text-align: right'>Phase1</th>";
                                        echo "<th style='text-align: right'>Phase2</th>";
                                        echo "<th style='text-align: right'>Phase3</th>";
                                        echo "<th style='text-align: right'>Phase4</th>";
                                        echo "<th style='text-align: right'>Phase5</th>";
                                        echo "<th style='text-align: right'>Phase35</th>";
                                        echo "<th>Exercise month</th>";
                                        echo "<th>Exercise year</th>";
                                        echo "<th>Situation</th>";
                                    echo "</tr>";

                                    for ($i=0; $i < count($chTable); $i++) { 
                                        //creation of the table
                                        echo "<tr>";
                                            echo "<td>".$chTable[$i][0]."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($chTable[$i][1])),0,","," ")."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($chTable[$i][2])),0,","," ")."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($chTable[$i][3])),0,","," ")."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($chTable[$i][4])),0,","," ")."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($chTable[$i][5])),0,","," ")."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($chTable[$i][6])),0,","," ")."</td>";
                                            echo "<td>".$chTable[$i][7]."</td>";
                                            echo "<td>".$chTable[$i][8]."</td>";
                                            echo "<td>".$chTable[$i][9]."</td>";
                                        echo "</tr>";
                                    }
                                echo "</table>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php
                    //caseloads
                    $caseLoadTable = array();
                    $caseLoadCountries = array();


                    foreach ($keyfigure_caseloads as $keyfigure_caseload){
                        $totalPop += $keyfigure_caseload["caseload_total_population"];
                        $affectedPop += $keyfigure_caseload["caseload_people_affected"];
                        $pin += $keyfigure_caseload["caseload_people_in_need"];
                        $pt += $keyfigure_caseload["caseload_people_targeted"];
                        $pr += $keyfigure_caseload["caseload_people_reached"];
                        
                        //Alimentation du tableau pour le detail par pays
                        $search = array_search($keyfigure_caseload["caseload_country"],$caseLoadCountries);
                        if($search ===false){
                            array_push($caseLoadCountries, $keyfigure_caseload["caseload_country"]);
                            array_push(
                                $caseLoadTable,
                                [
                                    $keyfigure_caseload["caseload_country"],
                                    $keyfigure_caseload["caseload_total_population"],
                                    $keyfigure_caseload["caseload_people_affected"],
                                    $keyfigure_caseload["caseload_people_in_need"],
                                    $keyfigure_caseload["caseload_people_targeted"],
                                    $keyfigure_caseload["caseload_people_reached"]
                                ]
                            );
                        }else{
                            $tempArray = array();
                            array_push($tempArray,
                                $caseLoadTable[$search][0],
                                ($caseLoadTable[$search][1] + $keyfigure_caseload["caseload_total_population"]),
                                ($caseLoadTable[$search][2] + $keyfigure_caseload["caseload_people_affected"]),
                                ($caseLoadTable[$search][3] + $keyfigure_caseload["caseload_people_in_need"]),
                                ($caseLoadTable[$search][4] + $keyfigure_caseload["caseload_people_targeted"]),
                                ($caseLoadTable[$search][5] + $keyfigure_caseload["caseload_people_reached"])
                            );
                            $caseLoadTable[$search] = $tempArray;
                        }
                        //fin Alimentation du tableau pour le detail par pays

                        //AS OF DATES
                        $index = count($caseloadAsOfDate);
                        $search = array_search($keyfigure_caseload->caseload_date,$caseloadAsOfDate);
                        
                        if($search ===false){
                            array_push($caseloadAsOfDate,$keyfigure_caseload->caseload_date);
                            array_push($caseloadAsOfCountries,$keyfigure_caseload->local_name);
                        }else{
                            $index = $search;
                            if(stripos($caseloadAsOfCountries[$index],$keyfigure_caseload->local_name)===false){
                                $caseloadAsOfCountries[$index]=$caseloadAsOfCountries[$index].', '.$keyfigure_caseload->local_name;
                            }
                        }
                        //AS OF DATES END
                    }
                    
                ?>
                <div class="col">
                    <div class="row"><h5>Caseloads</h5></div>
                    <div class="row">
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary " >
                            {{convertToUnit($totalPop,1)}}<br/>
                            Total population
                        </div>
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary" >
                            {{convertToUnit($affectedPop,1)}}<br/>
                            Affected people
                        </div>
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary">
                            {{convertToUnit($pin,1)}}<br/>
                            People in need
                        </div>
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary">
                            {{convertToUnit($pt,1)}}<br/>
                            People targeted
                        </div>
                        <div class="col shadow-sm p-3 ml-3 m-3 rounded text-white bg-secondary">
                            {{convertToUnit($pr,1)}}<br/>
                            People reached
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            @if (count($caseloadAsOfDate) > 0)
                                <footer class="blockquote-footer"><cite title="Source Title"> As of 
                                    <?php
                                        for ($x = 0; $x < count($caseloadAsOfDate); $x++) {
                                            echo $caseloadAsOfDate[$x]." (".$caseloadAsOfCountries[$x].") ";
                                        }
                                    ?>
                                </cite></footer>
                            @endif
                        </div>
                    </div>

                    <div class='col-12 d-flex justify-content-center border-bottom'>
                        <i id="arrowDown_tableCaseload{{$data['zone']->zone_code}}" class="bi bi-chevron-compact-down" style="font-size: 2rem; color: #418fde;" onclick="showTable('tableCaseload{{$data['zone']->zone_code}}')"></i>
                        <i id="arrowUp_tableCaseload{{$data['zone']->zone_code}}" class="bi bi-chevron-compact-up" style="font-size: 2rem; color: #418fde;display:none;" onclick="hideTable('tableCaseload{{$data['zone']->zone_code}}')" ></i>
                    </div>

                    <div class="row" id="tableCaseload{{$data['zone']->zone_code}}" style="display:none;">
                        <div class="col">
                            <?php
                                //caseloads
                                echo "<table class='table table-dark table-hover' id='tableCaseload".$data["zone"]->zone_code."' >";
                                    echo "<tr>";
                                        echo "<th>Country</th>";
                                        echo "<th style='text-align: right'>Total population</th>";
                                        echo "<th style='text-align: right'>People affected</th>";
                                        echo "<th style='text-align: right'>People in need</th>";
                                        echo "<th style='text-align: right'>People targeted</th>";
                                        echo "<th style='text-align: right'>People reached</th>";
                                    echo "</tr>";

                                for ($i=0; $i < count($caseLoadTable); $i++) { 
                                    //creation of the table
                                    echo "<tr>";
                                        echo "<td>".$caseLoadTable[$i][0]."</td>";
                                        echo "<td style='text-align: right'>".number_format(round(floatval($caseLoadTable[$i][1])),0,","," ")."</td>";
                                        echo "<td style='text-align: right'>".number_format(round(floatval($caseLoadTable[$i][2])),0,","," ")."</td>";
                                        echo "<td style='text-align: right'>".number_format(round(floatval($caseLoadTable[$i][3])),0,","," ")."</td>";
                                        echo "<td style='text-align: right'>".number_format(round(floatval($caseLoadTable[$i][4])),0,","," ")."</td>";
                                        echo "<td style='text-align: right'>".number_format(round(floatval($caseLoadTable[$i][5])),0,","," ")."</td>";
                                    echo "</tr>";
                                }
                                
                                echo "</table>";
                                
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php
                    //displacements
                    $disTable = array();
                    $disCountries = array();

                    foreach ($keyfigure_displacements as $keyfigure_displacement){
                        switch ($keyfigure_displacement->dis_type){
                            case "IDP":
                                $idps += $keyfigure_displacement->dis_value;
                            break;
                            case "Returnee":
                                $returnees += $keyfigure_displacement->dis_value;
                            break;
                            case "Refugee":
                                $refugees += $keyfigure_displacement->dis_value;
                            break;
                        }

                        //Alimentation du tableau pour le detail par pays
                        $search = array_search($keyfigure_displacement->dis_country,$disCountries);
                        if($search ===false){
                            array_push($disCountries, $keyfigure_displacement->dis_country);
                            
                            switch ($keyfigure_displacement->dis_type){
                                case "IDP":
                                    array_push(
                                        $disTable,
                                        [
                                            $keyfigure_displacement->dis_country,
                                            $keyfigure_displacement->dis_value,
                                            0,
                                            0
                                        ]
                                    );

                                    //AS OF DATES
                                    $index = count($idpAsOfDate);
                                    $search = array_search($keyfigure_displacement->dis_date,$idpAsOfDate);
                                    $isnew = false;
                                    if($search ===false){
                                        array_push($idpAsOfDate,$keyfigure_displacement->dis_date);
                                        array_push($idpAsOfCountries,$keyfigure_displacement->local_name);
                                    }else{
                                        $index = $search;
                                        if(stripos($idpAsOfCountries[$index],$keyfigure_displacement->local_name)===false){
                                            $idpAsOfCountries[$index]=$idpAsOfCountries[$index].', '.$keyfigure_displacement->local_name;
                                        }
                                    }
                                    //AS OF DATES END

                                break;
                                case "Returnee":
                                    array_push(
                                        $disTable,
                                        [
                                            $keyfigure_displacement->dis_country,
                                            0,
                                            $keyfigure_displacement->dis_value,
                                            0
                                        ]
                                    );

                                    //AS OF DATES
                                    $index = count($retAsOfDate);
                                    $search = array_search($keyfigure_displacement->dis_date,$retAsOfDate);
                                    $isnew = false;
                                    if($search ===false){
                                        array_push($retAsOfDate,$keyfigure_displacement->dis_date);
                                        array_push($retAsOfCountries,$keyfigure_displacement->local_name);
                                    }else{
                                        $index = $search;
                                        if(stripos($retAsOfCountries[$index],$keyfigure_displacement->local_name)===false){
                                            $retAsOfCountries[$index]=$retAsOfCountries[$index].', '.$keyfigure_displacement->local_name;
                                        }
                                    }
                                    //AS OF DATES END


                                break;
                                case "Refugee":
                                    array_push(
                                        $disTable,
                                        [
                                            $keyfigure_displacement->dis_country,
                                            0,
                                            0,
                                            $keyfigure_displacement->dis_value
                                        ]
                                    );

                                    //AS OF DATES
                                    $index = count($refAsOfDate);
                                    $search = array_search($keyfigure_displacement->dis_date,$refAsOfDate);
                                    $isnew = false;
                                    if($search ===false){
                                        array_push($refAsOfDate,$keyfigure_displacement->dis_date);
                                        array_push($refAsOfCountries,$keyfigure_displacement->local_name);
                                    }else{
                                        $index = $search;
                                        if(stripos($refAsOfCountries[$index],$keyfigure_displacement->local_name)===false){
                                            $refAsOfCountries[$index]=$refAsOfCountries[$index].', '.$keyfigure_displacement->local_name;
                                        }
                                    }
                                    //AS OF DATES END

                                break;
                            }
                            
                        }else{
                            $tempArray = array();
                            
                            switch ($keyfigure_displacement->dis_type){
                                case "IDP":
                                    array_push($tempArray,
                                        $disTable[$search][0],
                                        ($disTable[$search][1] + $keyfigure_displacement->dis_value),
                                        $disTable[$search][2],
                                        $disTable[$search][3],
                                    );

                                    //AS OF DATES
                                    $index = count($idpAsOfDate);
                                    $search = array_search($keyfigure_displacement->dis_date,$idpAsOfDate);
                                    $isnew = false;
                                    if($search ===false){
                                        array_push($idpAsOfDate,$keyfigure_displacement->dis_date);
                                        array_push($idpAsOfCountries,$keyfigure_displacement->local_name);
                                    }else{
                                        $index = $search;
                                        if(stripos($idpAsOfCountries[$index],$keyfigure_displacement->local_name)===false){
                                            $idpAsOfCountries[$index]=$idpAsOfCountries[$index].', '.$keyfigure_displacement->local_name;
                                        }
                                    }
                                    //AS OF DATES END

                                break;
                                case "Returnee":
                                    array_push($tempArray,
                                        $disTable[$search][0],
                                        $disTable[$search][1],
                                        ($disTable[$search][2] + $keyfigure_displacement->dis_value),
                                        $disTable[$search][3],
                                    );

                                    //AS OF DATES
                                    $index = count($retAsOfDate);
                                    $search = array_search($keyfigure_displacement->dis_date,$retAsOfDate);
                                    $isnew = false;
                                    if($search ===false){
                                        array_push($retAsOfDate,$keyfigure_displacement->dis_date);
                                        array_push($retAsOfCountries,$keyfigure_displacement->local_name);
                                    }else{
                                        $index = $search;
                                        if(stripos($retAsOfCountries[$index],$keyfigure_displacement->local_name)===false){
                                            $retAsOfCountries[$index]=$retAsOfCountries[$index].', '.$keyfigure_displacement->local_name;
                                        }
                                    }
                                    //AS OF DATES END

                                break;
                                case "Refugee":
                                    array_push($tempArray,
                                        $disTable[$search][0],
                                        $disTable[$search][1],
                                        $disTable[$search][2],
                                        ($disTable[$search][3] + $keyfigure_displacement->dis_value)
                                    );

                                    //AS OF DATES
                                    $index = count($refAsOfDate);
                                    $search = array_search($keyfigure_displacement->dis_date,$refAsOfDate);
                                    $isnew = false;
                                    if($search ===false){
                                        array_push($refAsOfDate,$keyfigure_displacement->dis_date);
                                        array_push($refAsOfCountries,$keyfigure_displacement->local_name);
                                    }else{
                                        $index = $search;
                                        if(stripos($refAsOfCountries[$index],$keyfigure_displacement->local_name)===false){
                                            $refAsOfCountries[$index]=$refAsOfCountries[$index].', '.$keyfigure_displacement->local_name;
                                        }
                                    }
                                    //AS OF DATES END

                                break;
                            }

                            
                            $disTable[$search] = $tempArray;
                        }
                        //fin Alimentation du tableau pour le detail par pays

                        //GESTION DES SOURCES
                        if(stripos($disSources,$keyfigure_displacement->dis_source)===false){
                            $disSources=$disSources.', '.$keyfigure_displacement->dis_source;
                        }


                        


                    }
                ?>
                <div class="col">
                    <div class="row"><h5>Displacements</h5></div>
                    <div class="row">
                        <div class="col shadow-sm p-3  m-3 rounded text-white  bg-secondary">
                            {{convertToUnit($idps,1)}}<br/>
                            Internally displaced persons
                        </div>
                        <div class="col shadow-sm p-3 m-3 rounded text-white  bg-secondary">
                            {{convertToUnit($refugees,1)}}<br/>
                            Refugees
                        </div>
                        <div class="col shadow-sm p-3  m-3 rounded text-white  bg-secondary" >
                            {{convertToUnit($returnees,1)}}<br/>
                            Returnees
                        </div>
                    </div>
                    <div class="row">
                        <div class='col'>
                            <footer class="blockquote-footer"><cite title="Source Title">
                                @if (count($idpAsOfDate) > 0)
                                    IDPs: 
                                        <?php
                                            for ($x = 0; $x < count($idpAsOfDate); $x++) {
                                                echo $idpAsOfDate[$x]." (".$idpAsOfCountries[$x].") ";
                                            }
                                        ?>
                                    
                                @endif

                                @if (count($refAsOfDate) > 0)
                                        , Refugees: 
                                        <?php
                                            for ($x = 0; $x < count($refAsOfDate); $x++) {
                                                echo $refAsOfDate[$x]." (".$refAsOfCountries[$x].") ";
                                            }
                                        ?>
                                @endif
                                
                                @if (count($retAsOfDate) > 0)
                                        , Returnees: 
                                        <?php
                                            for ($x = 0; $x < count($retAsOfDate); $x++) {
                                                echo $retAsOfDate[$x]." (".$retAsOfCountries[$x].") ";
                                            }
                                        ?>
                                    
                                @endif
                            </cite></footer>
                            <footer class="blockquote-footer"><cite title="Source Title"> Sources : {{$disSources}} </cite></footer>
                        </div>
                    </div>

                    <div class='col-12 d-flex justify-content-center border-bottom'>
                        <i id="arrowDown_tableDis{{$data['zone']->zone_code}}" class="bi bi-chevron-compact-down" style="font-size: 2rem; color: #418fde;" onclick="showTable('tableDis{{$data['zone']->zone_code}}')"></i>
                        <i id="arrowUp_tableDis{{$data['zone']->zone_code}}" class="bi bi-chevron-compact-up" style="font-size: 2rem; color: #418fde;display:none;" onclick="hideTable('tableDis{{$data['zone']->zone_code}}')"></i>
                    </div>

                    <div class="row" id="tableDis{{$data['zone']->zone_code}}" style="display:none;">
                        <div class="col">
                            <?php
                                //displacements

                                    echo "<table class='table table-dark table-hover' id='tableDispl".$data["zone"]->zone_code."' >";
                                    echo "<tr>";
                                        echo "<th>Country</th>";
                                        echo "<th style='text-align: right'>Internally displaced persons</th>";
                                        echo "<th style='text-align: right'>Refugees</th>";
                                        echo "<th style='text-align: right'>Returnees</th>";
                                    echo "</tr>";
                                    
                                    for ($i=0; $i < count($disTable); $i++) { 
                                        //creation of the table
                                        echo "<tr>";
                                            echo "<td>".$disTable[$i][0]."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($disTable[$i][1])),0,","," ")."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($disTable[$i][2])),0,","," ")."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($disTable[$i][3])),0,","," ")."</td>";
                                        echo "</tr>";
                                    }

                                    echo "</table>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php
                    $nutTable = array();
                    $nutCountries = array();

                    foreach ($keyfigure_nutritions as $keyfigure_nutrition){
                        $sam += $keyfigure_nutrition->nut_sam;
                        $mam += $keyfigure_nutrition->nut_mam;
                        $gam += $keyfigure_nutrition->nut_gam;

                        //Alimentation du tableau pour le detail par pays
                        $search = array_search($keyfigure_nutrition->nut_country,$nutCountries);
                        if($search ===false){
                            array_push($nutCountries, $keyfigure_nutrition->nut_country);
                            array_push(
                                $nutTable,
                                [
                                    $keyfigure_nutrition->nut_country,
                                    $keyfigure_nutrition->nut_sam,
                                    $keyfigure_nutrition->nut_mam,
                                    $keyfigure_nutrition->nut_gam
                                ]
                            );
                        }else{
                            $tempArray = array();
                            array_push($tempArray,
                                $nutTable[$search][0],
                                ($nutTable[$search][1] + $keyfigure_nutrition->nut_sam),
                                ($nutTable[$search][2] + $keyfigure_nutrition->nut_mam),
                                ($nutTable[$search][3] + $keyfigure_nutrition->nut_gam),
                            );
                            $nutTable[$search] = $tempArray;
                        }
                        //fin Alimentation du tableau pour le detail par pays

                        //AS OF DATES
                        $index = count($nutritionAsOfDate);
                        $search = array_search($keyfigure_nutrition->nut_date,$nutritionAsOfDate);
                        $isnew = false;
                        if($search ===false){
                            array_push($nutritionAsOfDate,$keyfigure_nutrition->nut_date);
                            array_push($nutritionAsOfCountries,$keyfigure_nutrition->local_name);
                        }else{
                            $index = $search;
                            if(stripos($nutritionAsOfCountries[$index],$keyfigure_nutrition->local_name)===false){
                                $nutritionAsOfCountries[$index]=$nutritionAsOfCountries[$index].', '.$keyfigure_nutrition->local_name;
                            }
                        }
                        //AS OF DATES END
                        
                    }
                ?>
                <div class="col">

                    <div class="row"><h5>Nutrition</h5></div>
                    <div class="row">
                        <div class="col shadow-sm p-3 m-3 rounded text-white  bg-secondary" >
                            {{convertToUnit($sam,1)}}<br/>
                            Severe Acute Malnutrition
                        </div>
                        <div class="col shadow-sm p-3 m-3 rounded text-white  bg-secondary" >
                            {{convertToUnit($mam,1)}}<br/>
                            Moderate Acute Malnutrition
                        </div>
                        <div class="col shadow-sm p-3 m-3 rounded text-white  bg-secondary" >
                            {{convertToUnit($gam,1)}}<br/>
                            Global Acute Malnutrition
                        </div>
                    </div>
                    <div class="row">
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

                    <div class='col-12 d-flex justify-content-center border-bottom'>
                        <i id="arrowDown_tableNutrition{{$data['zone']->zone_code}}" class="bi bi-chevron-compact-down" style="font-size: 2rem; color: #418fde;" onclick="showTable('tableNutrition{{$data['zone']->zone_code}}')"></i>
                        <i id="arrowUp_tableNutrition{{$data['zone']->zone_code}}" class="bi bi-chevron-compact-up" style="font-size: 2rem; color: #418fde;display:none;" onclick="hideTable('tableNutrition{{$data['zone']->zone_code}}')"></i>
                    </div>

                    <div class="row" id="tableNutrition{{$data['zone']->zone_code}}" style="display:none;">
                        <div class="col">
                            <?php
                                //nutrition
                                echo "<table class='table table-dark table-hover' id='tableNut".$data["zone"]->zone_code."' >";
                                    echo "<tr>";
                                        echo "<th>Country</th>";
                                        echo "<th style='text-align: right'>Severe Acute Malnutrition</th>";
                                        echo "<th style='text-align: right'>Moderate Acute Malnutrition</th>";
                                        echo "<th style='text-align: right'>Global Acute Malnutrition</th>";
                                    echo "</tr>";

                                    for ($i=0; $i < count($nutTable); $i++) { 
                                        //creation of the table
                                        echo "<tr>";
                                            echo "<td>".$nutTable[$i][0]."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($nutTable[$i][1])),0,","," ")."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($nutTable[$i][2])),0,","," ")."</td>";
                                            echo "<td style='text-align: right'>".number_format(round(floatval($nutTable[$i][3])),0,","," ")."</td>";
                                        echo "</tr>";
                                    }

                                echo "</table>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>


            </div>
        </div>
        @endforeach
        
</div>

<script>
$(document).ready(function(){
  
    $('[data-toggle="tooltip"]').tooltip();
    $('#loading').hide();
    datas = {!! json_encode($datas) !!};
    
    showBloc(datas[0].zone.zone_code);
    
});

function showTable(table){
    $("#arrowDown_"+table).hide();
    $("#arrowUp_"+table).show();
    $("#"+table).slideToggle();
}

function hideTable(table){
    $("#arrowUp_"+table).hide();
    $("#arrowDown_"+table).show();
    $("#"+table).slideToggle();
}

function showBloc(bloc){
    $(".crisisTitle").removeClass("btn-primary");
    $("#crisisTitle_"+bloc).addClass("btn-primary");
    $(".bloc_crise").hide();
    $("#bloc_"+bloc).show();
}

</script>
    
@endsection