@extends('layout')
@section('title', 'Edit key figure category')
@section('content')
    <div class="col">
        <form action="/update/zone" method="POST">
        @csrf
            <div class="form-group">
                <label for="zone_code">Code</label>
                <input type="text" class="form-control" id="zone_code"  name="zone_code" aria-describedby="zone_codeHelp" value="{{ $datas->zone_code }}">
                <small id="zone_codeHelp" class="form-text text-muted">...</small>
            </div>
            <div class="form-group">
                <label for="zone_name">Name</label>
                <input type="text" class="form-control" id="zone_name" name="zone_name" aria-describedby="zone_nameHelp"  value="{{ $datas->zone_name }}">
                <small id="zone_nameHelp" class="form-text text-muted">...</small>
            </div>
            <input type="text" hidden name="zone_id" value="{{ $datas->zone_id }}">
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection  