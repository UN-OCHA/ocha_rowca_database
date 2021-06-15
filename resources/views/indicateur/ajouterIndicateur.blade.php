@extends('layout')
@section('title', 'Add indicator')
@section('content')
    <div class="row">
        <div class="col">
            <form action="/add/indicateur" method="POST">
            @csrf
                <div class="form-group">
                    <label for="kfindic_caption_fr">Titre FR</label>
                    <input type="text" class="form-control" id="kfindic_caption_fr"  name="kfindic_caption_fr" aria-describedby="kfindic_caption_frHelp">
                    <small id="kfindic_caption_frHelp" class="form-text text-muted">Mettre le titre du chiffre clé en francais.</small>
                </div>
                <div class="form-group">
                    <label for="kfindic_caption_en">Titre EN</label>
                    <input type="text" class="form-control" id="kfindic_caption_en" name="kfindic_caption_en" aria-describedby="kfindic_caption_enHelp" >
                    <small id="kfindic_caption_enHelp" class="form-text text-muted">Mettre le titre anglais du chiffre clé.</small>
                </div>
                <div class="form-group">
                    <label for="kfindic_source">Source</label>
                    <input type="text" class="form-control" id="kfindic_source" name="kfindic_source" aria-describedby="kfindic_sourceHelp">
                    <small id="kfindic_sourceHelp" class="form-text text-muted">Mettre la source du chiffre clé.</small>
                </div>
                <div class="form-group">
                    <label for="id_method">Methode de calcul</label>
                    <select class="form-control" id="id_method" name="id_method" >
                        @foreach ($methodecalculs as $methodecalcul)
                            <option value="{{ $methodecalcul->id_method }}" >{{ $methodecalcul->label_method }}</option>
                        @endforeach
                    </select>
                    <small id="id_methodHelp" class="form-text text-muted">Choisir une méthode de calcule des trends.</small>
                </div>
                <div class="form-group">
                    <label for="kfsubcategory_id">Sous catégorie</label>
                    <select class="form-control" id="kfsubcategory_id"  name="kfsubcategory_id">
                        @foreach ($subcategories as $subcategory)
                            <option value="{{ $subcategory->kfsubcategory_id }}" >{{ $subcategory->kfsubcategory_caption_fr }}</option>
                        @endforeach
                    </select>
                    <small id="kfsubcategory_idHelp" class="form-text text-muted">Choisir une sous catégorie.</small>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection