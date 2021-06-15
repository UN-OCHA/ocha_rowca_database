
@extends('layout')
@section('title', 'Confirm indicator delete')
@section('content')
    <div class="col">
    Veux tu vraiment supprimer l'indicateur <strong>{{ $indicateur->kfindic_caption_fr }}</strong>?

            @if (Session::has('msg'))
            <div class="alert alert-danger" role="alert">
            {!! Session::has('msg') ? Session::get("msg") : '' !!}
        </div>
        @endif

    
        <form action="/delete/indicateur" method="POST">
        @csrf
            <div class="form-group">
                <label for="delete">Type "DELETE" in all caps to confirm</label>
                <input type="text" class="form-control" id="delete"  name="delete" aria-describedby="emailHelp">

            </div>
            <input type="text" hidden name="kfind_id" value="{{ $indicateur->kfind_id }}">
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection