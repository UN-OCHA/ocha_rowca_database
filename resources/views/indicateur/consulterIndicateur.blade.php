
@extends('layout')
@section('title', 'View Indicator')
@section('content')
    <div class="col">
        <div class="card" style="width: 40rem;">
            <!--img src="..." class="card-img-top" alt="..."-->
            <div class="card-body">
                <h5 class="card-title">{{ $indicateur->kfindic_caption_fr }}</h5>
                <p class="card-text">
                    Méthode de calcul : {{ $indicateur->label_method }}<br/>
                    Sous catégorie : {{ $indicateur->kfsubcategory_caption_fr }}<br/>
                    Catégorie : {{ $indicateur->kfcateg_caption_fr }}
                </p>
                <a href="/edit/indicateur/{{ $indicateur->kfind_id }}" class="btn btn-primary">Modifier</a>&nbsp
                <a href="#" class="btn btn-primary">Voir les données</a>&nbsp
                <a href="/delete/indicateur/{{ $indicateur->kfind_id }}" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
@endsection           