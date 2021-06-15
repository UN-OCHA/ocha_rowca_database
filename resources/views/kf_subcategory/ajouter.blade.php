@extends('layout')
@section('title', 'Add Key figure sub category')
@section('content')
    <div class="row">
        <div class="col">
            <form action="/add/subcategory" method="POST">
            @csrf
                <div class="form-group">
                    <label for="kfsubcategory_caption_fr">Titre FR</label>
                    <input type="text" class="form-control" id="kfsubcategory_caption_fr"  name="kfsubcategory_caption_fr" aria-describedby="kfsubcategory_caption_frHelp">
                    <small id="kfsubcategory_caption_frHelp" class="form-text text-muted">...</small>
                </div>
                <div class="form-group">
                    <label for="kfsubcategory_caption_en">Titre EN</label>
                    <input type="text" class="form-control" id="kfsubcategory_caption_en" name="kfsubcategory_caption_en" aria-describedby="kfsubcategory_caption_enHelp" >
                    <small id="kfsubcategory_caption_enHelp" class="form-text text-muted">...</small>
                </div>
                <div class="form-group">
                    <label for="kfcateg_id">Key figure category</label>
                    <select class="form-control" id="kfcateg_id" name="kfcateg_id" >
                        @foreach ($kf_categs as $kf_categ)
                            <option value="{{ $kf_categ->kfcateg_id }}" >{{ $kf_categ->kfcateg_caption_en }}</option>
                        @endforeach
                    </select>
                    <small id="kfcateg_idHelp" class="form-text text-muted">...</small>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection