@extends('layout')
@section('title', 'Add location')
@section('content')
    <div class="row">
        <div class="col">
            <form action="/add/localite" method="POST">
            @csrf
                
                <div class="form-group">
                    <label for="local_name">Name</label>
                    <input type="text" class="form-control" id="local_name" name="local_name" aria-describedby="local_nameHelp" autocomplete="off" >
                    <small id="local_nameHelp" class="form-text text-muted">...</small>
                </div>
                <div class="form-group">
                    <label for="local_pcode">PCode</label>
                    <input type="text" class="form-control" id="local_pcode"  name="local_pcode" aria-describedby="local_pcodeHelp" autocomplete="off">
                    <small id="local_pcodeHelp" class="form-text text-muted">...</small>
                </div>
                <div class="form-group">
                    <label for="local_admin_level">Admin level</label>
                    <select class="form-control" id="local_admin_level" name="local_admin_level">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    <small id="local_admin_leveleHelp" class="form-text text-muted">...</small>
                </div>
                <input type="text" class="form-control" id="zone_id"  name="zone_id" value="{{$zone_id}}">
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection