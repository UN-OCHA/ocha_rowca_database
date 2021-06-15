@extends('layout')
@section('title', 'Add Key figure category')
@section('content')
    <div class="row">
        <div class="col">
            <form action="/add/category" method="POST">
            @csrf
                <div class="form-group">
                    <label for="kfcateg_caption_fr">Titre FR</label>
                    <input type="text" class="form-control" id="kfcateg_caption_fr"  name="kfcateg_caption_fr" aria-describedby="kfcateg_caption_frHelp">
                    <small id="kfcateg_caption_frHelp" class="form-text text-muted">...</small>
                </div>
                <div class="form-group">
                    <label for="kfcateg_caption_en">Titre EN</label>
                    <input type="text" class="form-control" id="kfcateg_caption_en" name="kfcateg_caption_en" aria-describedby="kfcateg_caption_enHelp" >
                    <small id="kfcateg_caption_enHelp" class="form-text text-muted">...</small>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection