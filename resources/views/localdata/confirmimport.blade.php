
@extends('layout')
@section('title', 'Confirm data import')
@section('content')
    <div class="col">
        <div class="row">
            <div class="col">
                Are you sure you want to import <strong>{{ $elementName }} </strong> data? old data will be replaced.

                    @if (Session::has('msg'))
                        <div class="alert alert-danger" role="alert">
                            {!! Session::has('msg') ? Session::get("msg") : '' !!}
                        </div>
                    @endif

                <form action="/database/guide_import" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="import">Type "IMPORT" in all caps to confirm</label>
                        <input type="text" class="form-control w-25" id="import"  name="import" autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label for="formFile" class="form-label">Fichier</label>
                        <input class="form-control" name="importedfile" type="file" id="formFile">
                    </div>

                    @if ($elementName == "Caseloads" || $elementName == "Displacements")
                        <select class="form-select" name="zoneCode" aria-label="Default select example">
                            <option value="" selected>Selectionnez une zone</option>
                            @foreach ($zones as $zone)
                                <option value="{{$zone->zone_code}}">{{$zone->zone_name}}</option>
                            @endforeach
                        </select>
                    @endif

                    <input type="text" hidden name="element" value="{{ $element }}">
                    <button type="submit" class="btn btn-primary" style="background-color:#418fde;border:none;">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection