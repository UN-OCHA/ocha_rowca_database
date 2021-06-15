
@extends('layout')
@section('title', 'Confirm locality '.$datas->local_name.' deletion')
@section('content')
    <div class="col">

        <div class="row">
            <div class="col">
                Do you really want to delete <strong>{{ $datas->local_name }}</strong>?

                @if (Session::has('msg'))
                <div class="alert alert-danger" role="alert">
                {!! Session::has('msg') ? Session::get("msg") : '' !!}
                </div>
                @endif


                <form action="/delete/localite" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="delete">Type "DELETE" in all caps to confirm</label>
                        <input type="text" class="form-control" id="delete"  name="delete" aria-describedby="emailHelp" autocomplete="off">

                    </div>
                    <input type="text" hidden name="local_id" value="{{ $datas->local_id }}">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>


   
@endsection