@extends('layout')
@section('title', 'Key figure sub categories')
@section('content')
<div class='col'>
<div class="row">
        <a href="/add/subcategory" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Add</a>
    </div>
    <div class="row">
    @if (Session::has('msg'))
                <div class="alert alert-danger" role="alert">
                {!! Session::has('msg') ? Session::get("msg") : '' !!}
            </div>
            @endif
    
        <form action="/massdelete/subcategory" method="POST">
        @csrf
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th scope="col">Delete?</th>
                        <th scope="col">Sub category</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $data)
                    <tr>
                        <td><input type="checkbox" name="checkbox{{ $data->kfsubcategory_id }}" id="checkbox{{ $data->kfsubcategory_id }}"></td>
                        <td><a href='/subcategory/{{ $data->kfsubcategory_id }}'>{{ $data->kfsubcategory_caption_en }}</a></td>
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