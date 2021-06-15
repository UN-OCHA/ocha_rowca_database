@extends('layout')
@section('title', 'Edit key figure sub category')
@section('content')
    <div class="col">
        <form action="/update/subcategory" method="POST">
        @csrf
            <div class="form-group">
                <label for="kfsubcategory_caption_fr">Title FR</label>
                <input type="text" class="form-control" id="kfsubcategory_caption_fr"  name="kfsubcategory_caption_fr" aria-describedby="kfsubcategory_caption_frHelp" value="{{ $datas->kfsubcategory_caption_fr }}">
                <small id="kfsubcategory_caption_frHelp" class="form-text text-muted">...</small>
            </div>
            <div class="form-group">
                <label for="kfsubcategory_caption_en">Title EN</label>
                <input type="text" class="form-control" id="kfsubcategory_caption_en" name="kfsubcategory_caption_en" aria-describedby="kfsubcategory_caption_enHelp"  value="{{ $datas->kfsubcategory_caption_en }}">
                <small id="kfsubcategory_caption_enHelp" class="form-text text-muted">...</small>
            </div>
            <div class="form-group">
                <label for="kfsubcategory_id">Category</label>
                <select class="form-control" id="kfcateg_id"  name="kfcateg_id">
                    @foreach ($kf_categs as $kf_categ)
                        <?php  
                            $selected="";
                            if($kf_categ->kfcateg_caption_en == $datas->kfcateg_caption_en){
                                $selected="selected";
                            }
                        ?>
                        <option value="{{ $kf_categ->kfcateg_id }}" {{$selected}}>{{ $kf_categ->kfcateg_caption_en }}</option>
                    @endforeach
                </select>
                <small id="kfsubcategory_idHelp" class="form-text text-muted">...</small>
            </div>
            <input type="text" hidden name="kfsubcategory_id" value="{{ $datas->kfsubcategory_id }}">
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection  