<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ URL::asset('bootstrap-5.0.0/css/bootstrap.min.css') }}" >
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('bootstrap-icons/font/bootstrap-icons.css') }}"/>
        
        <script src="{{ URL::asset('js/jquery-3.6.0.min.js') }}" ></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" ></script>
        <script src="{{ URL::asset('bootstrap-5.0.0/js/bootstrap.min.js') }}" ></script>
        <script src="{{ URL::asset('d3/d3.min.js') }}" ></script>
        <script src="{{ URL::asset('leaflet/leaflet.js') }}" ></script>
        <script src="{{ URL::asset('js/html2canvas.js') }}" ></script>
        <script src="{{ URL::asset('js/dom-to-image.min.js') }}" ></script>
        <script src="{{ URL::asset('js/FileSaver.min.js') }}" ></script>
        <script src="https://d3js.org/d3-array.v2.min.js"></script>
        <script src="https://unpkg.com/topojson-client@3"></script>
        <script src="https://cdn.jsdelivr.net/npm/@turf/turf@5/turf.min.js"></script>
  
        <script src="{{ URL::asset('js/Sheetjs/xlsx.core.min.js') }}"></script>
        <script src="{{ URL::asset('js/tableExport/js/tableexport.js') }}"></script>
        <script src="{{ URL::asset('js/exportToExcel.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <!-- html to powerpoint -->
        <script src="{{ URL::asset('pptxgen/jszip.min.js') }}"></script>
        <script src="{{ URL::asset('pptxgen/pptxgen.min.js') }}"></script>  
   

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
   integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
   crossorigin=""/>

        
    
        <style>
            body{
                background-color:#e6f3ff;
            }

            .downloadImage{
                cursor: pointer;
                color:#418fde;
                font-size: 25px;
            }
            .downloadImage:hover{
                color:#ccc;
            }

            .keyfigure{
                color:#418fde;
                font-size: 20px;
            }
            .keyfigure-selected{
                color:#E56A54;
                font-size: 20px;
            }

            .labelkeyfigure{
                color:#999999;
                font-size: 12px;
            }

            .cards{
                background-color:#ffffff;
                border:1px solid;
                border-color:#ffffff;
                margin-bottom:10px;
            }

            .cards-selected{
                background-color:#fff;
                border-bottom:7px solid;
                border-color:#E56A54;
            }

            .cards:hover{
                background-color:#F2F2F2;
                border:1px solid;
                border-color:#E6E6E6;
                cursor: pointer;
                margin-bottom:10px;
            }

            .white-blocs{
                background-color:#ffffff;
                border:1px solid;
                border-color:#ffffff;
            }

            .bloc-data{
            }

            .legend {
                line-height: 18px;
                color: #555;
                background-color:#fff;
                padding:5px;
            }
            .legend i {
                width: 18px;
                height: 18px;
                float: left;
                margin-right: 8px;
                opacity: 0.7;
            }
            .labelCarte{
                font-size: 14px;
            }
            .exportImage{
                height: 40px;
                padding: 6px;
                border-radius: 20px;
                background-color: #fff;
            }
            .exportImage:hover{
                height: 40px;
                background-color: #418fde;
                padding: 6px;
                border-radius: 20px;
                cursor:pointer;
            }

            .disclaimer {
                font-size: 10px;
                color: #000;
                font-style: italic;
                margin-bottom:8px;
            }

            .loading {
                text-align: center;
                width: 99%;
                height: 2000px;
                background-color: #fff;
                opacity: 0.7;
                padding-top: 50px;
                position: absolute;
                z-index: 2000;
            }
        </style>
    
    </head>
    <body >
        <div class="container-fluid">
            <div class="row " style="background-color:#418fde;">
                <ul class="nav justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/database">Filter</a>
                    </li>
                
                    @if (request()->session()->get('authenticated')!=null)
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/import">Import</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/managezones">Manage crisis zones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/logout">Logout</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/accessimport">login</a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="row">
                <div class="col">
                    @yield('content')
                </div>
            </div>
        </div>
        
    </body>
</html>
