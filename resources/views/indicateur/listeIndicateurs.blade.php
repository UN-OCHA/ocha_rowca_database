@extends('layout')
@section('title', 'Indicators')
@section('content')
<div class='col'>
<div class="row">
        <a href="/add/indicateur" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Add</a>
    </div>
    <div class="row">
    @if (Session::has('msg'))
                <div class="alert alert-danger" role="alert">
                {!! Session::has('msg') ? Session::get("msg") : '' !!}
            </div>
            @endif
    
        <form action="/massdelete/indicateur" method="POST">
        @csrf
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th scope="col">Delete?</th>
                        <th scope="col">Chiffre clé</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($indicateurs as $indicateur)
                    <tr>
                        <td><input type="checkbox" name="checkbox{{ $indicateur->kfind_id }}" id="checkbox{{ $indicateur->kfind_id }}"></td>
                        <td><a href='/indicateur/{{ $indicateur->kfind_id }}'>{{ $indicateur->kfindic_caption_fr }}</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="form-group">
                <label for="delete">Type exactly <strong>DELETE multiples</strong> to confirm</label>
                <input type="text" class="form-control" id="delete"  name="delete" aria-describedby="emailHelp">
            </div>
            <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
    </div>s
</div>
    
@endsection