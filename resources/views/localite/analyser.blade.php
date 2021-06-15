@extends('layout')
@section('title', $datas->local_name)
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
    <div class="col">

        <div class="row">
            <div class="col">
                <p>
                    <em>
                       Analyse data for the {{ $datas->local_name }}<br/>
                       by <a href='#' onclick='showTable("bloc_displ")' >displacements</a>, <a href='#' onclick='showTable("bloc_caseload")'>caseloads</a>, <a href='#' onclick='showTable("bloc_ch_curr")'>cadre harmonisé current</a>, <a href='#' onclick='showTable("bloc_ch_proj")'>cadre harmonisé projected</a>, <a href='#' onclick='showTable("bloc_nutrition")'>nutrition</a>
                    </em>
                </p>
            </div> 
        </div>
        <div class="row table_heat" id="bloc_displ">
            <div class='col'>
                <h3>Displacements</h3>
                <table class="table " id="table_displ">
                    <thead>
                        <tr>
                        <th>locality</th>
                        <th>IDPs</th>
                        <th>Refugees</th>
                        <th>Returnees</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                            
                            

                            foreach ($keyfigure_displacements as $keyfigure_displacement){
                                $idp = 0;
                            $ref = 0;
                            $ret = 0;
                                switch ($keyfigure_displacement->dis_type) {
                                    case "IDP":
                                        $idp=$keyfigure_displacement->dis_value;
                                        break;
                                    case "Refugee":
                                        $ref=$keyfigure_displacement->dis_value;
                                        break;
                                    case "Returnee":
                                        $ret=$keyfigure_displacement->dis_value;
                                        break;
                                }

                                ?>
                                    <tr>
                                        <td>{{ $keyfigure_displacement->dis_admin1_name }}</td>
                                        <td>{{ number_format($idp,0,","," ") }}</td>
                                        <td>{{ number_format($ref,0,","," ") }}</td>
                                        <td>{{ number_format($ret,0,","," ") }}</td>
                                    </tr>
                                <?php
                            }
                        ?>

                        
                    </tbody>
                </table>
            </div>
        </div>


        <div class="row table_heat" id="bloc_ch_curr">
            <div class='col'>
                <h3>Cadre harmonisé current</h3>
                <table class="table " id="table_ch_curr">
                    <thead>
                        <tr>
                        <th>locality</th>
                        <th>Phase 1</th>
                        <th>Phase 2</th>
                        <th>Phase 3</th>
                        <th>Phase 3,5</th>
                        <th>Phase 4</th>
                        <th>Phase 5</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                            
                            $admin1List = array();
                            foreach ($keyfigure_cadre_harmonises_current as $ch_data){
                                array_push($admin1List,$ch_data->ch_admin1_name);
                            }
                            $admin1List = array_unique($admin1List);

                            foreach ($admin1List as $admin1){ 
                                $ch_phase1 = 0;
                                $ch_phase2 = 0;
                                $ch_phase3 = 0;
                                $ch_phase35 = 0;
                                $ch_phase4 = 0;
                                $ch_phase5 = 0;

                

                                foreach ($keyfigure_cadre_harmonises_current as $ch_data){
                                    if($ch_data->ch_admin1_name == $admin1){
                                        $ch_phase1 += $ch_data->ch_phase1;
                                        $ch_phase2 += $ch_data->ch_phase2;
                                        $ch_phase3 += $ch_data->ch_phase3;
                                        $ch_phase35 += $ch_data->ch_phase35;
                                        $ch_phase4 += $ch_data->ch_phase4;
                                        $ch_phase5 += $ch_data->ch_phase5;
                                    }
                                }

                                ?>

                                <tr>
                                    <td>{{ $admin1 }}</td>
                                    <td>{{ number_format($ch_phase1,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase2,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase3,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase35,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase4,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase5,0,","," ") }}</td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>



        <div class="row table_heat" id="bloc_ch_proj">
            <div class='col'>
                <h3>Cadre harmonisé projected</h3>
                <table class="table " id="table_ch_proj">
                    <thead>
                        <tr>
                        <th>locality</th>
                        <th>Phase 1</th>
                        <th>Phase 2</th>
                        <th>Phase 3</th>
                        <th>Phase 3,5</th>
                        <th>Phase 4</th>
                        <th>Phase 5</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                            
                            $admin1List = array();
                            foreach ($keyfigure_cadre_harmonises_projected as $ch_data){
                                array_push($admin1List,$ch_data->ch_admin1_name);
                            }
                            $admin1List = array_unique($admin1List);

                            foreach ($admin1List as $admin1){ 
                                $ch_phase1 = 0;
                                $ch_phase2 = 0;
                                $ch_phase3 = 0;
                                $ch_phase35 = 0;
                                $ch_phase4 = 0;
                                $ch_phase5 = 0;

                

                                foreach ($keyfigure_cadre_harmonises_projected as $ch_data){
                                    if($ch_data->ch_admin1_name == $admin1){
                                        $ch_phase1 += $ch_data->ch_phase1;
                                        $ch_phase2 += $ch_data->ch_phase2;
                                        $ch_phase3 += $ch_data->ch_phase3;
                                        $ch_phase35 += $ch_data->ch_phase35;
                                        $ch_phase4 += $ch_data->ch_phase4;
                                        $ch_phase5 += $ch_data->ch_phase5;
                                    }
                                }

                                ?>

                                <tr>
                                    <td>{{ $admin1 }}</td>
                                    <td>{{ number_format($ch_phase1,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase2,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase3,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase35,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase4,0,","," ") }}</td>
                                    <td>{{ number_format($ch_phase5,0,","," ") }}</td>
                                </tr>
                                <?php
                            }
                        ?>

                        
                    </tbody>
                </table>
            </div>
        </div>




        <div class="row table_heat" id="bloc_caseload">
            <div class='col'>
                <h3>Caseloads</h3>
                <table class="table " id="table_caseload">
                    <thead>
                        <tr>
                        <th>locality</th>
                        <th>People in need</th>
                        <th>People targeted</th>
                        <th>People reached</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                            

                            foreach ($keyfigure_caseloads as $caseload){
                                $pin = 0;
                                $pt = 0;
                                $pr = 0;
                            
                                $pin = $caseload->caseload_people_in_need;
                                $pt = $caseload->caseload_people_targeted;
                                $pr = $caseload->caseload_people_reached;
                                ?>
                                    <tr>
                                        <td>{{ $caseload->caseload_admin1_name }}</td>
                                        <td>{{ number_format($pin,0,","," ") }}</td>
                                        <td>{{ number_format($pt,0,","," ") }}</td>
                                        <td>{{ number_format($pr,0,","," ") }}</td>
                                    </tr>
                                <?php
                            }
                        ?>
                        

                        
                    </tbody>
                </table>
            </div>
        </div>




        <div class="row table_heat" id="bloc_nutrition">
            <div class='col'>
                <h3>Nutrition</h3>
                <table class="table " id="table_nutrition">
                    <thead>
                        <tr>
                        <th>locality</th>
                        <th>Severe acute malnutrition</th>
                        <th>Moderate acute malnutrition</th>
                        <th>Global acute malnutrition</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            

                            foreach ($keyfigure_nutritions as $nutrition){
                                $sam = 0;
                                $mam = 0;
                                $gam = 0;

                                $sam = $nutrition->nut_sam;
                                $mam = $nutrition->nut_mam;
                                $gam = $nutrition->nut_gam;
                                
                                ?>
                                    <tr>
                                        <td>{{ $nutrition->nut_admin1 }}</td>
                                        <td>{{ number_format($sam,0,","," ") }}</td>
                                        <td>{{ number_format($mam,0,","," ") }}</td>
                                        <td>{{ number_format($gam,0,","," ") }}</td>
                                    </tr>
                                <?php
                            }
                        ?>
                        

                        
                    </tbody>
                </table>
            </div>
        </div>




    </div>

    <script type="text/javascript">
        function clean_formatted_data(str) {
  return parseFloat(str.replace(/([%,$,\, ])+/g,''));
}

function col_to_array(tbl_col,target) {
  // Returns column `n` (zero indexed) in table id `target` as an array 
  
  var colArray = $('#'+target+' td:nth-child('+tbl_col+')').map(function(){
    return clean_formatted_data( $(this).text() );
  }).get();
  
  return colArray;
}

//------ new schtuff ------------------------//

function get_pos_of_max(col_data) { return $.inArray( Math.max.apply(Math,col_data), col_data ) }

function generate_opacities(col_data, max) {
  var opacity_array = [];
  var increment = max/(col_data.length);
  console.log(col_data);
  for(i=col_data.length; i >= 1; i--) {
    opacity_array.push(i*increment/100);
  }
  console.log(opacity_array);
  return opacity_array;
}

function showTable(idtable){
    $(".table_heat").hide();
    $("#"+idtable).show();
}

function process_col_best_performing(tbl_col, target) {
  var col_data = col_to_array(tbl_col,target);
  var opacity_array = generate_opacities(col_data, 100);
  var row_count = col_data.length; 
   
  for (var i=1; i <= row_count; i++) {    
    $('#'+target+' tr:nth-child('+(get_pos_of_max(col_data)+1)+') td:nth-child('+tbl_col+')').css('background','rgba(65,143,222,'+opacity_array[0]+')');
    col_data[get_pos_of_max(col_data)] = null;
    opacity_array.splice(0,3);
 
  }
}

process_col_best_performing(2,'table_displ');
process_col_best_performing(3,'table_displ');
process_col_best_performing(4,'table_displ');

process_col_best_performing(2,'table_ch_curr');
process_col_best_performing(3,'table_ch_curr');
process_col_best_performing(4,'table_ch_curr');
process_col_best_performing(5,'table_ch_curr');
process_col_best_performing(6,'table_ch_curr');
process_col_best_performing(7,'table_ch_curr');

process_col_best_performing(2,'table_ch_proj');
process_col_best_performing(3,'table_ch_proj');
process_col_best_performing(4,'table_ch_proj');
process_col_best_performing(5,'table_ch_proj');
process_col_best_performing(6,'table_ch_proj');
process_col_best_performing(7,'table_ch_proj');

process_col_best_performing(2,'table_caseload');
process_col_best_performing(3,'table_caseload');
process_col_best_performing(4,'table_caseload');

process_col_best_performing(2,'table_nutrition');
process_col_best_performing(3,'table_nutrition');
process_col_best_performing(4,'table_nutrition');
    </script>

@endsection           


