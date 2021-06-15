@extends('layout')
@section('title', 'Add crisis zone')
@section('content')
    <div class="row">
        <div class="col">
            <form action="/add/zone" method="POST">
            @csrf
                <div class="form-group">
                    <label for="zone_code">Code</label>
                    <input type="text" class="form-control" id="zone_code"  name="zone_code" aria-describedby="zone_codeHelp">
                    <small id="zone_codeHelp" class="form-text text-muted">...</small>
                </div>
                <div class="form-group">
                    <label for="zone_name">Name</label>
                    <input type="text" class="form-control" id="zone_name" name="zone_name" aria-describedby="zone_nameHelp" >
                    <small id="zone_nameHelp" class="form-text text-muted">...</small>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection