
@extends('layout')
@section('title', 'Confirm Key figure category deletion')
@section('content')
    <div class="col">
    Do you really want to delete <strong>{{ $datas->kfcateg_caption_en }}</strong>?

            @if (Session::has('msg'))
            <div class="alert alert-danger" role="alert">
            {!! Session::has('msg') ? Session::get("msg") : '' !!}
        </div>
        @endif

    
        <form action="/delete/category" method="POST">
        @csrf
            <div class="form-group">
                <label for="delete">Type "DELETE" in all caps to confirm</label>
                <input type="text" class="form-control" id="delete"  name="delete" aria-describedby="emailHelp">

            </div>
            <input type="text" hidden name="kfcateg_id" value="{{ $datas->kfcateg_id }}">
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection