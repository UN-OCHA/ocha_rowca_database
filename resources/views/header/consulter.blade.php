
@extends('layout')
@section('title', 'View key figure category')
@section('content')
    <div class="col">
        <div class="card" style="width: 40rem;">
            <!--img src="..." class="card-img-top" alt="..."-->
            <div class="card-body">
                <h5 class="card-title">{{ $datas->kfcateg_caption_en }}</h5>
                <a href="/edit/category/{{ $datas->kfcateg_id }}" class="btn btn-primary">Edit</a>&nbsp
                <a href="#" class="btn btn-primary">View datas</a>&nbsp
                <a href="/delete/category/{{ $datas->kfcateg_id }}" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
@endsection           