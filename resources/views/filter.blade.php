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
  
        
   



        
    
        <style>
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
                font-size: 25px;
            }
            .keyfigure-selected{
                color:#E56A54;
                font-size: 25px;
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
                background-color:#F2F2F2;
                border:1px solid;
                border-color:#F2F2F2;
                padding-bottom:10px;
            }

            .cards:hover{
                background-color:#F2F2F2;
                border:1px solid;
                border-color:#E6E6E6;
                cursor: pointer;
                margin-bottom:10px;
            }

            .bloc-data{
                background-color:#F2F2F2;
                border:1px solid;
                border-color:#F2F2F2;
            }
        </style>
    
    </head>
    <body>
    <div class="container-fluid">
            <div class="row text-white" style="background-color:#418fde;">
                <div class="col">
                    <h1 class="display-6">
                        <img src="{{asset('images/logoOchaBlanc.png')}}" style="height:50px;"  alt="logo ocha"/> @yield('title')
                    </h1>

                    <?php 
                        if (request()->session()->get('authenticated')!=null) {
                            ?>
                            <a class="nav-link active"  style="background-color:#418fde;border:none;" href="/logout">Logout</a>
                            <?php 
                        }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    @yield('content')
                </div>
            </div>
        </div>
    </body>
</html>
