@extends('layout')
@section('title', $datas->zone_name.' key figures')
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
    }

    

    //displacements
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


    }

    //cadre harmonise projected
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
    }


    //nutrition
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
    }
?>
    <div class="col">
        <div class="row">
            <div class="col">
                <p>
                    <em>
                        @foreach ($liste_localites as $localite)
                            <a href="/localite/{{ $localite->local_id }}">{{ $localite->local_name }}</a>, 
                        @endforeach
                    </em>
                </p>
                <p>
                    <em>
                        Analyze <a href="/analyserzone/{{ $datas->zone_id }}">simple</a>. <a href="/zone/charts/{{ $datas->zone_id }}">Charts</a> 
                    </em>
                </p>
            </div>
                    
        </div>
        <div class="row">
            <div class='col'>
            <h3>Cadre harmonisé</h3>

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
            <div class='col'>
                <h3>Caseloads</h3>
                Total population : <strong>{{convertToUnit($totalPop,1)}}</strong><br/>
                Affected people : <strong>{{convertToUnit($affectedPop,1)}}</strong><br/>
                People in need : <strong>{{convertToUnit($pin,1)}}</strong><br/>
                People targeted : <strong>{{convertToUnit($pt,1)}}</strong><br/>
                People reached : <strong>{{convertToUnit($pr,1)}}</strong><br/><br/>

                @if (count($caseloadAsOfDate) > 0)
                    <footer class="blockquote-footer"><cite title="Source Title"> 
                        <?php
                            for ($x = 0; $x < count($caseloadAsOfDate); $x++) {
                                echo $caseloadAsOfDate[$x]." (".$caseloadAsOfCountries[$x].") ";
                            }
                        ?>
                    </cite></footer>
                @endif

                <!--div class='row'>
                    <div class='col' style=" height:300px;" id='chartCaseload'>
                            
                    </div>
                </div-->
            </div>
            <div class='col'>
                <h3>Displacements</h3>
                Internally displaced persons : <strong>{{convertToUnit($idps,1)}}</strong><br/>
                Refugees : <strong>{{convertToUnit($refugees,1)}}</strong><br/>
                Returnees : <strong>{{convertToUnit($returnees,1)}}</strong><br/><br/>

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
            <div class='col'>
                <h3>Nutrition</h3>
                SAM : <strong>{{convertToUnit($sam,1)}}</strong><br/>
                MAM : <strong>{{convertToUnit($mam,1)}}</strong><br/>
                GAM : <strong>{{convertToUnit($gam,1)}}</strong><br/><br/>

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

@endsection           


