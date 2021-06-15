@extends('layout')
@section('title', 'Import screen')
@section('content')
    <div class="col">
        <div class="row">
            <div class="col d-flex justify-content-center">
                <img src="{{asset('images/import.png')}}" style="height:500px;"  alt="logo ocha"/>
            </div>
            <div class="col pt-5 me-2">
                @if (Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Well done!</strong> Successfull import.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Danger!</strong> {{session('error')}}.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <p class="h1 mb-3">Importation des données</p>
                <form action="/database/guide_import" method="POST" enctype="multipart/form-data" class=" g-3 needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Data category</label>
                        <select class="form-select" id="dataCategory" name="dataCategory" onChange="validateForm()" aria-label="Default select example" required>
                            <option value="" selected>Selectionnez une catégorie</option>
                            <option value="caseloads">Caseloads</option>
                            <option value="informSahel">Inform sahel</option>
                            <option value="disp">Displacements</option>
                            <option value="nutrition">Nutrition</option>
                            <option value="ch">Cadre harmonisé</option>
                        </select>
                    </div>

                    <div class="mb-3" style="display:none;" id="rowCrise">
                        <label for="exampleInputEmail1" class="form-label">Crise</label>
                        <select class="form-select" name="zoneCode"  id="crise" onChange="validateForm()"  aria-label="Default select example">
                            <option value="" selected>Select a crisis</option>
                            @foreach ($zones as $zone)
                                <option value="{{$zone->zone_code}}">{{$zone->zone_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="formFile" class="form-label">File</label>
                        <input class="form-control" name="importedfile" type="file" id="formFile" required>
                    </div>

                    <button type="submit" id="submitButton" class="btn btn-primary" style="background-color:#418fde;border:none;display:none;s">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script>
    function validateForm() {
        if ($("#dataCategory").val() == "caseloads" || $("#dataCategory").val() == "disp") {
            $("#rowCrise").show();
            if ($("#crise").val() != "") {
                $("#submitButton").show();
            } else {
               $("#submitButton").hide();
            }
        } else {
            $("#rowCrise").hide();
            if ($("#dataCategory").val() != "") {
                $("#submitButton").show();
            }else{
                $("#submitButton").hide();
            }
        }
    }

    (function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
        })
    })()
    </script>
@endsection