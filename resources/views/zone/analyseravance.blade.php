@extends('layoutanalyser')
@section('title', 'Advanced Analysis')
@section('content')


    <div class="col">
        <div class="row">
            <div class="col">
                <p>
                    <em>
                       Show by <a href='#' onclick='showTable("bloc_displ")' >displacements</a>, <a href='#' onclick='showTable("bloc_caseload")'>caseloads</a>, <a href='#' onclick='showTable("bloc_ch")'>cadre harmonisé </a>, <a href='#' onclick='showTable("bloc_nutrition")'>nutrition</a>
                    </em>
                </p>
            </div> 
        </div>

        <div class="row table_heat" id="bloc_displ">
            <div class='col'>
                <h3>Displacements</h3>
                <div class="row">
                    <div class='col' id="pivot_displacement">
                    </div>
                </div>
            </div>
        </div>
        <div class="row table_heat" id="bloc_ch" style="display:none;">
            <div class='col'>
                <h3>Cadre harmonisé</h3>
                <div id="pivot_ch_curr"></div>
            </div>
        </div>
        <div class="row table_heat" id="bloc_caseload"  style="display:none;">
            <div class='col'>
                <h3>Caseloads</h3>
                <div id="pivot_caseloads"></div>
            </div>
        </div>
        <div class="row table_heat" id="bloc_nutrition"  style="display:none;">
            <div class='col'>
                <h3>Nutrition</h3>
                <div id="pivot_nutrition"></div>
            </div>
        </div>





    </div>

    <script type="text/javascript">
        $(function(){
            var displacements_by_regions = {!! json_encode($displacements_by_regions) !!}
            var cadre_harmonises_by_regions = {!! json_encode($cadre_harmonises_by_regions) !!}
            var caseloads_by_regions = {!! json_encode($caseloads_by_regions) !!}
            var nutrition_by_regions = {!! json_encode($nutrition_by_regions) !!}


            var derivers = $.pivotUtilities.derivers;
            var renderers = $.extend($.pivotUtilities.renderers,
            $.pivotUtilities.c3_renderers);

     
            $("#pivot_displacement").pivotUI(
                displacements_by_regions,
                {
                    renderers: renderers,
                    cols: ["dis_date"], rows: ["zone_name"],
                    rendererName: "Table",
                    vals: ["dis_value"],
                    aggregatorName: "Sum",
                    rowOrder: "key_a_to_z", colOrder: "key_a_to_z",

                }
            );

            $("#bloc_ch").pivotUI(
                cadre_harmonises_by_regions,
                {
                    renderers: renderers,
                    cols: ["ch_exercise_year","ch_situation","ch_exercise_month"], rows: ["zone_name"],
                    rendererName: "Table",
                    vals: ["ch_phase35"],
                    aggregatorName: "Sum",
                    rowOrder: "key_a_to_z", colOrder: "key_a_to_z",
                }
            );

            $("#bloc_caseload").pivotUI(
                caseloads_by_regions,
                {
                    renderers: renderers,
                    cols: ["caseload_date"], rows: ["zone_name"],
                    rendererName: "Table",
                    vals: ["caseload_people_in_need"],
                    aggregatorName: "Sum",
                    rowOrder: "key_a_to_z", colOrder: "key_a_to_z",
                }
            );

            $("#bloc_nutrition").pivotUI(
                nutrition_by_regions,
                {
                    renderers: renderers,
                    cols: ["nut_date"], rows: ["zone_name"],
                    rendererName: "Table",
                    vals: ["nut_sam"],
                    aggregatorName: "Sum",
                    rowOrder: "key_a_to_z", colOrder: "key_a_to_z",
                }
            );
        });

        function showTable(idtable){
            $(".table_heat").hide();
            $("#"+idtable).show();
        }

    </script>

@endsection           


