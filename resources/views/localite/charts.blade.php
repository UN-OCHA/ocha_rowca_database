@extends('layoutchart')
@section('title', $datas->local_name)
@section('content')

<style>
    
</style>

    <div class="col">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="/database">Database</a></li>
                        <li class="breadcrumb-item"><a href="/zones">Crisis zones</a></li>
                        <li class="breadcrumb-item"><a href="/zone/{{$zone->zone_id}}">{{$zone->zone_name}}</a></li>
                        <li class="breadcrumb-item"><a href="/localite/{{$datas->local_id}}">{{$datas->local_name}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View charts</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p>
                    <em>
                       View charts for {{ $datas->local_name }}<br/>
                       by <a href='#' onclick='showTable("bloc_displ")' >displacements</a>, <a href='#' onclick='showTable("bloc_caseload")'>caseloads</a>, <a href='#' onclick='showTable("bloc_ch")'>cadre harmonisé </a>, <a href='#' onclick='showTable("bloc_nutrition")'>nutrition</a>
                    </em>
                </p>
            </div> 
        </div>

        <div class="row table_heat mb-4" id="bloc_displ">
            <div class='col'>
                <h3>Displacements</h3>
                <div class="row">
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12" >
                        <div  id="pivot_displacement_idp"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_displacement_ref"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_displacement_ret"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row table_heat  mb-4" id="bloc_ch" >
            <div class='col'>
                <h3>Cadre harmonisé</h3>
                <div class="row mb-4">
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_curr_ph1"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_curr_ph2"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_curr_ph3"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_curr_ph35"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_curr_ph4"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_curr_ph5"></div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_proj_ph1"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_proj_ph2"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_proj_ph3"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_proj_ph35"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_proj_ph4"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_ch_proj_ph5"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row table_heat mb-4" id="bloc_caseload" >
            <div class='col'>
                <h3>Caseloads</h3>
                <div class="row">
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_pin"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_pt"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_pr"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row table_heat mb-4" id="bloc_nutrition" >
            <div class='col'>
                <h3>Nutrition</h3>
                <div class="row">
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_sam"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_mam"></div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div id="pivot_gam"></div>
                    </div>
                </div>
            </div>
        </div>





    </div>

    <script type="text/javascript">
        
        $(function(){

            var trends_by_years = {!! json_encode($trends_by_years) !!}
            data_idps = [];
            data_ref = [];
            data_ret = [];
            data_pin = [];
            data_pt = [];
            data_pr = [];
            data_ch_curr_ph1 = [];
            data_ch_curr_ph2 = [];
            data_ch_curr_ph3 = [];
            data_ch_curr_ph35 = [];
            data_ch_curr_ph4 = [];
            data_ch_curr_ph5 = [];
            data_ch_curr_ph6 = [];
            data_ch_proj_ph1 = [];
            data_ch_proj_ph2 = [];
            data_ch_proj_ph3 = [];
            data_ch_proj_ph35 = [];
            data_ch_proj_ph4 = [];
            data_ch_proj_ph5 = [];
            data_ch_proj_ph6 = [];
            data_sam = [];
            data_mam = [];
            data_gam = [];

            categ_idps = [];
            categ_ref = [];
            categ_ret = [];
            categ_pin = [];
            categ_pt = [];
            categ_pr = [];
            categ_ch_curr_ph1 = [];
            categ_ch_curr_ph2 = [];
            categ_ch_curr_ph3 = [];
            categ_ch_curr_ph35 = [];
            categ_ch_curr_ph4 = [];
            categ_ch_curr_ph5 = [];
            categ_ch_curr_ph6 = [];
            categ_ch_proj_ph1 = [];
            categ_ch_proj_ph2 = [];
            categ_ch_proj_ph3 = [];
            categ_ch_proj_ph35 = [];
            categ_ch_proj_ph4 = [];
            categ_ch_proj_ph5 = [];
            categ_ch_proj_ph6 = [];
            categ_sam = [];
            categ_mam = [];
            categ_gam = [];

            for (i = 0; i < trends_by_years.length; i++) {
                switch(trends_by_years[i].t_category) {
                    case "IDP":
                        data_idps.push(trends_by_years[i].t_value);
                        categ_idps.push(trends_by_years[i].t_year);
                        break;
                    case "Refugee":
                        data_ref.push(trends_by_years[i].t_value);
                        categ_ref.push(trends_by_years[i].t_year);
                        break;
                    case "Returnee":
                        data_ret.push(trends_by_years[i].t_value);
                        categ_ret.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Current_phase1":
                        data_ch_curr_ph1.push(trends_by_years[i].t_value);
                        categ_ch_curr_ph1.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Current_phase2":
                        data_ch_curr_ph2.push(trends_by_years[i].t_value);
                        categ_ch_curr_ph2.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Current_phase3":
                        data_ch_curr_ph3.push(trends_by_years[i].t_value);
                        categ_ch_curr_ph3.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Current_phase3_plus":
                        data_ch_curr_ph35.push(trends_by_years[i].t_value);
                        categ_ch_curr_ph35.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Current_phase4":
                        data_ch_curr_ph4.push(trends_by_years[i].t_value);
                        categ_ch_curr_ph4.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Current_phase5":
                        data_ch_curr_ph5.push(trends_by_years[i].t_value);
                        categ_ch_curr_ph5.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Projected_phase1":
                        data_ch_proj_ph1.push(trends_by_years[i].t_value);
                        categ_ch_proj_ph1.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Projected_phase2":
                        data_ch_proj_ph2.push(trends_by_years[i].t_value);
                        categ_ch_proj_ph2.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Projected_phase3":
                        data_ch_proj_ph3.push(trends_by_years[i].t_value);
                        categ_ch_proj_ph3.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Projected_phase3_plus":
                        data_ch_proj_ph35.push(trends_by_years[i].t_value);
                        categ_ch_proj_ph35.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Projected_phase4":
                        data_ch_proj_ph4.push(trends_by_years[i].t_value);
                        categ_ch_proj_ph4.push(trends_by_years[i].t_year);
                        break;
                    case "ch_Projected_phase5":
                        data_ch_proj_ph5.push(trends_by_years[i].t_value);
                        categ_ch_proj_ph5.push(trends_by_years[i].t_year);
                        break;
                    case "people_in_need":
                        data_pin.push(trends_by_years[i].t_value);
                        categ_pin.push(trends_by_years[i].t_year);
                        break;
                    case "people_targeted":
                        data_pt.push(trends_by_years[i].t_value);
                        categ_pt.push(trends_by_years[i].t_year);
                        break;
                    case "people_reached":
                        data_pr.push(trends_by_years[i].t_value);
                        categ_pr.push(trends_by_years[i].t_year);
                        break;
                    case "severe_acute_malnutrition":
                        data_sam.push(trends_by_years[i].t_value);
                        categ_sam.push(trends_by_years[i].t_year);
                        break;
                    case "moderate_acute_malnutrition":
                        data_mam.push(trends_by_years[i].t_value);
                        categ_mam.push(trends_by_years[i].t_year);
                        break;
                    case "global_acute_malnutrition":
                        data_gam.push(trends_by_years[i].t_value);
                        categ_gam.push(trends_by_years[i].t_year);
                        break;
                }
            }

            //CHART OPTIONS
            marker = {
                    size: 5,
                    colors: ['#fff'],
                    strokeColors: '#418fde',
                    strokeWidth: 2,
                    strokeOpacity: 0.9,
                    strokeDashArray: 0,
                    fillOpacity: 1,
                    discrete: [],
                    shape: "circle",
                    radius: 2,
                    offsetX: 0,
                    offsetY: 0,
                    onClick: undefined,
                    onDblClick: undefined,
                    showNullDataPoints: true,
                    hover: {
                    size: undefined,
                    sizeOffset: 3
                    }
                };

            datalabel =  {
                    enabled: true,
                    formatter: (value) => {return labelData(value) },
                    offsetY: -5,
                    offsetX: 0,
                    background: {
                        enabled: false,
                    },
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Helvetica, Arial, sans-serif',
                        fontWeight: 'normal',
                        colors: ['#000']
                    }
                }


            //DISPLACEMENT IDPS
            var options_idps = {
                grid: {
                    show: false,
                },
                stroke: {
                        width: 5
                    },
                title: {
                    text: 'Internally displaced persons',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10,
                    
                },
                series: [{
                    name: 'idps',
                    data: data_idps
                }],
                xaxis: {
                    categories: categ_idps
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }

            var chart_idps = new ApexCharts(document.querySelector("#pivot_displacement_idp"), options_idps);
            

            //DISPLACEMENT REFUGEES
            var options_ref = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'Refugees',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'refugees',
                    data: data_ref
                }],
                xaxis: {
                    categories: categ_ref
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisThousand(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ref = new ApexCharts(document.querySelector("#pivot_displacement_ref"), options_ref);

            //DISPLACEMENT REFUGEES
            var options_ret = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'Returnees',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'returnees',
                    data: data_ret
                }],
                xaxis: {
                    categories: categ_ret
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ret = new ApexCharts(document.querySelector("#pivot_displacement_ret"), options_ret);


            // CH CURR PH1
            var options_ch_curr_ph1 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 1 current',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'ch phase 1 current',
                    data: data_ch_curr_ph1
                }],
                xaxis: {
                    categories: categ_ch_curr_ph1
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_curr_ph1 = new ApexCharts(document.querySelector("#pivot_ch_curr_ph1"), options_ch_curr_ph1);

            //CH CURR ph2
            var options_ch_curr_ph2 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 2 current',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'ch phase 2 current',
                    data: data_ch_curr_ph2
                }],
                xaxis: {
                    categories: categ_ch_curr_ph2
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_curr_ph2 = new ApexCharts(document.querySelector("#pivot_ch_curr_ph2"), options_ch_curr_ph2);

            //CH CURR ph3
            var options_ch_curr_ph3 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 3 current',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'ch phase 3 current',
                    data: data_ch_curr_ph3
                }],
                xaxis: {
                    categories: categ_ch_curr_ph3
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_curr_ph3 = new ApexCharts(document.querySelector("#pivot_ch_curr_ph3"), options_ch_curr_ph3);


            //CH CURR ph35
            var options_ch_curr_ph35 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 3,5 current',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'ch phase 3,5 current',
                    data: data_ch_curr_ph35
                }],
                xaxis: {
                    categories: categ_ch_curr_ph35
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_curr_ph35 = new ApexCharts(document.querySelector("#pivot_ch_curr_ph35"), options_ch_curr_ph35);


            //CH CURR ph4
            var options_ch_curr_ph4 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 4 current',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'ch phase 4 current',
                    data: data_ch_curr_ph4
                }],
                xaxis: {
                    categories: categ_ch_curr_ph4
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_curr_ph4 = new ApexCharts(document.querySelector("#pivot_ch_curr_ph4"), options_ch_curr_ph4);



            //CH CURR ph5
            var options_ch_curr_ph5 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 5 current',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'ch phase 5 current',
                    data: data_ch_curr_ph5
                }],
                xaxis: {
                    categories: categ_ch_curr_ph5
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisThousand(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_curr_ph5 = new ApexCharts(document.querySelector("#pivot_ch_curr_ph5"), options_ch_curr_ph5);



            // CH proj PH1
            var options_ch_proj_ph1 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 1 projected',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'ch phase 1 projected',
                    data: data_ch_proj_ph1
                }],
                xaxis: {
                    categories: categ_ch_proj_ph1
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_proj_ph1 = new ApexCharts(document.querySelector("#pivot_ch_proj_ph1"), options_ch_proj_ph1);

            //CH proj ph2
            var options_ch_proj_ph2 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 2 projected',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'CH phase 2 projected',
                    data: data_ch_proj_ph2
                }],
                xaxis: {
                    categories: categ_ch_proj_ph2
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_proj_ph2 = new ApexCharts(document.querySelector("#pivot_ch_proj_ph2"), options_ch_proj_ph2);

            //CH proj ph3
            var options_ch_proj_ph3 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 3 projected',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'CH phase 3 projected',
                    data: data_ch_proj_ph3
                }],
                xaxis: {
                    categories: categ_ch_proj_ph3
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_proj_ph3 = new ApexCharts(document.querySelector("#pivot_ch_proj_ph3"), options_ch_proj_ph3);


            //CH proj ph35
            var options_ch_proj_ph35 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 3,5 projected',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'CH phase 3,5 projected',
                    data: data_ch_proj_ph35
                }],
                xaxis: {
                    categories: categ_ch_proj_ph35
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_proj_ph35 = new ApexCharts(document.querySelector("#pivot_ch_proj_ph35"), options_ch_proj_ph35);


            //CH proj ph4
            var options_ch_proj_ph4 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 4 projected',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'CH phase 4 projected',
                    data: data_ch_proj_ph4
                }],
                xaxis: {
                    categories: categ_ch_proj_ph4
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_proj_ph4 = new ApexCharts(document.querySelector("#pivot_ch_proj_ph4"), options_ch_proj_ph4);



            //CH proj ph5
            var options_ch_proj_ph5 = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'CH phase 5 projected',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'CH phase 5 projected',
                    data: data_ch_proj_ph5
                }],
                xaxis: {
                    categories: categ_ch_proj_ph5
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisThousand(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_ch_proj_ph5 = new ApexCharts(document.querySelector("#pivot_ch_proj_ph5"), options_ch_proj_ph5);


            //PIN
            var options_pin = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'People in need',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'People in need',
                    data: data_pin
                }],
                xaxis: {
                    categories: categ_pin
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_pin = new ApexCharts(document.querySelector("#pivot_pin"), options_pin);


            //PT
            var options_pt = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'People targeted',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'People targeted',
                    data: data_pt
                }],
                xaxis: {
                    categories: categ_pt
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_pt = new ApexCharts(document.querySelector("#pivot_pt"), options_pt);


            //PR
            var options_pr = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'People reached',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'People reached',
                    data: data_pr
                }],
                xaxis: {
                    categories: categ_pr
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_pr = new ApexCharts(document.querySelector("#pivot_pr"), options_pr);


            //mam
            var options_sam = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'Severe acute marnutrition',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'SAM',
                    data: data_sam
                }],
                xaxis: {
                    categories: categ_sam
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_sam = new ApexCharts(document.querySelector("#pivot_sam"), options_sam);


            //mam
            var options_mam = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'Moderate acute marnutrition',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'MAM',
                    data: data_mam
                }],
                xaxis: {
                    categories: categ_mam
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_mam = new ApexCharts(document.querySelector("#pivot_mam"), options_mam);


            //mam
            var options_gam = {
                grid: {
                    show: false,
                },
                title: {
                    text: 'Global acute marnutrition',
                    align: 'left',
                    margin: 10,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                    fontSize:  '14px',
                    fontWeight:  'bold',
                    fontFamily:  undefined,
                    color:  '#263238'
                    },
                },
                chart: {
                    type: 'line',
                    offsetY: 10
                },
                series: [{
                    name: 'GAM',
                    data: data_gam
                }],
                xaxis: {
                    categories: categ_gam
                },
                yaxis: {
                    show: false,
                    tickAmount: 5,
                    min:0,
                    labels: {
                        show: true,
                        formatter: (value) => {return legendYaxisMillion(value) },
                        offsetX: -10,
                    }
                },
                dataLabels: datalabel,
                markers: marker
            }
            var chart_gam = new ApexCharts(document.querySelector("#pivot_gam"), options_gam);


            chart_idps.render();
            chart_ref.render();
            chart_ret.render();

            chart_ch_curr_ph1.render();
            chart_ch_curr_ph2.render();
            chart_ch_curr_ph3.render();
            chart_ch_curr_ph35.render();
            chart_ch_curr_ph4.render();
            chart_ch_curr_ph5.render();

            chart_ch_proj_ph1.render();
            chart_ch_proj_ph2.render();
            chart_ch_proj_ph3.render();
            chart_ch_proj_ph35.render();
            chart_ch_proj_ph4.render();
            chart_ch_proj_ph5.render();

            chart_pin.render();
            chart_pt.render();
            chart_pr.render();

            chart_sam.render();
            chart_mam.render();
            chart_gam.render();

            //$(".apexcharts-toolbar").css("top", "-15px");


        });

        function showTable(idtable){
            $(".table_heat").hide();
            $("#"+idtable).show();

        }

        function labelData(val){
            result = "";
            if(val<1000){
                result = val;
            }else{
                if(val<1000000){
                    nb = val/1000;
                    result = nb.toFixed(0)+"K";;
                }else{
                    if(val<1000000000){
                        nb = val/1000000;
                        result = nb.toFixed(1)+"M";
                    }else{
                        nb = val/1000000000;
                        result = nb.toFixed(1)+"B";
                    }
                }
            }
            return result;
        }

        function legendYaxisMillion(val){
            result = "0";
            
            if(val==0){
                result = "0";
            }else{
                nb = val/1000000;
                result = nb.toFixed(1)+"M";
            }
            
            return result;
        }

        function legendYaxisThousand(val){
            result = "";
            

            if(val==0){
                result = "0";
            }else{
                nb = val/1000;
                result = nb.toFixed(0)+"K";
            }

            
            return result;
        }

        /*for (i = 0; i < displacements_by_regions.length; i++) {
                var keyNames = Object.keys(displacements_by_regions[i]);
                for (j = 0; j < keyNames.length; j++) {
                    var keyName = keyNames[j];
                    if(keyName=="dis_value"){
                        displacements_by_regions[i].dis_value = Number(displacements_by_regions[i][keyName])
                    }else{

                    }
                }
            }*/

    </script>

@endsection           


