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
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-2">
                    <div class="row">
                        <div class="col">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <?php 
                                    if (request()->session()->get('authenticated')!=null) {
                                        ?>
                                        <a class="nav-link active"  style="background-color:#418fde;border:none;" href="/logout">Logout</a>
                                        <?php 
                                    }
                                ?>
                            </li>
                        </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col">
                        <h1 class="display-4">@yield('title')</h1>
                        </div>
                    </div>
                    <div class="row">
                            @yield('content')
                    </div>
                </div>
            </div>
            
            
        </div>

    </body>
</html>
