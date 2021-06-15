@extends('layout')
@section('title', 'Headers')
@section('content')
<div class='col'>
<div class="row">
    </div>
    <div class="row">
    @if (Session::has('msg'))
                <div class="alert alert-danger" role="alert">
                {!! Session::has('msg') ? Session::get("msg") : '' !!}
            </div>
            @endif
    
        <form action="/massdelete/category" method="POST">
        @csrf
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th scope="col">Delete?</th>
                        <th scope="col">Category</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $data)
                    <tr>
                        <td><input type="checkbox" name="checkbox{{ $data->kfcateg_id }}" id="checkbox{{ $data->kfcateg_id }}"></td>
                        <td><a href='/category/{{ $data->kfcateg_id }}'>{{ $data->header_name }}</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="form-group">
                <label for="delete">Type exactly <strong>DELETE multiples</strong> to confirm</label>
                <input type="text" class="form-control" id="delete"  name="delete" >
            </div>
            <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
    </div>
</div>
    
@endsection