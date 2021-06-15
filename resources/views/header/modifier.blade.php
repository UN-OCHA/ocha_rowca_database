@extends('layout')
@section('title', 'Edit key figure category')
@section('content')
    <div class="col">
        <form action="/update/category" method="POST">
        @csrf
            <div class="form-group">
                <label for="kfcateg_caption_fr">Title FR</label>
                <input type="text" class="form-control" id="kfcateg_caption_fr"  name="kfcateg_caption_fr" aria-describedby="kfcateg_caption_frHelp" value="{{ $datas->kfcateg_caption_fr }}">
                <small id="kfcateg_caption_frHelp" class="form-text text-muted">...</small>
            </div>
            <div class="form-group">
                <label for="kfcateg_caption_en">Title EN</label>
                <input type="text" class="form-control" id="kfcateg_caption_en" name="kfcateg_caption_en" aria-describedby="kfcateg_caption_enHelp"  value="{{ $datas->kfcateg_caption_en }}">
                <small id="kfcateg_caption_enHelp" class="form-text text-muted">...</small>
            </div>
            <input type="text" hidden name="kfcateg_id" value="{{ $datas->kfcateg_id }}">
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection  