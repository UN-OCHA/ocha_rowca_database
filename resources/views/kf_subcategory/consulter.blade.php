
@extends('layout')
@section('title', 'View key figure sub category')
@section('content')
    <div class="col">
        <div class="card" style="width: 40rem;">
            <!--img src="..." class="card-img-top" alt="..."-->
            <div class="card-body">
                <h5 class="card-title">{{ $datas->kfsubcategory_caption_en }}</h5>
                <p class="card-text">
                    Category : {{ $datas->kfcateg_caption_en }}
                </p>
                <a href="/edit/subcategory/{{ $datas->kfsubcategory_id }}" class="btn btn-primary">Edit</a>&nbsp
                <a href="#" class="btn btn-primary">View datas</a>&nbsp
                <a href="/delete/subcategory/{{ $datas->kfsubcategory_id }}" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
@endsection           