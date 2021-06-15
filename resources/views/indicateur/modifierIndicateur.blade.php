@extends('layout')
@section('title', 'Edit indicator')
@section('content')
    <div class="col">
        <form action="/indicateur/update" method="POST">
        @csrf
            <div class="form-group">
                <label for="kfindic_caption_fr">Titre FR</label>
                <input type="text" class="form-control" id="kfindic_caption_fr"  name="kfindic_caption_fr" aria-describedby="emailHelp" value="{{ $indicateur->kfindic_caption_fr }}">
                <small id="kfindic_caption_frHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
                <label for="kfindic_caption_en">Titre EN</label>
                <input type="text" class="form-control" id="kfindic_caption_en" name="kfindic_caption_en" aria-describedby="emailHelp"  value="{{ $indicateur->kfindic_caption_en }}">
                <small id="kfindic_caption_enHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
                <label for="kfindic_source">Source</label>
                <input type="text" class="form-control" id="kfindic_source" name="kfindic_source" aria-describedby="emailHelp"  value="{{ $indicateur->kfindic_source }}">
                <small id="kfindic_sourceHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
                <label for="id_method">Methode de calcul</label>
                <select class="form-control" id="id_method" name="id_method" >
                    @foreach ($methodecalculs as $methodecalcul)
                        <?php  
                            $selected="";
                            if($methodecalcul->label_method == $indicateur->label_method){
                                $selected="selected";
                            }
                        ?>
                        <option value="{{ $methodecalcul->id_method }}" {{$selected}}>{{ $methodecalcul->label_method }}</option>
                    @endforeach
                </select>
                <small id="id_methodHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
                <label for="kfsubcategory_id">Sous catégorie</label>
                <select class="form-control" id="kfsubcategory_id"  name="kfsubcategory_id">
                    @foreach ($subcategories as $subcategory)
                        <?php  
                            $selected="";
                            if($subcategory->kfsubcategory_caption_fr == $indicateur->kfsubcategory_caption_fr){
                                $selected="selected";
                            }
                        ?>
                        <option value="{{ $subcategory->kfsubcategory_id }}" {{$selected}}>{{ $subcategory->kfsubcategory_caption_fr }}</option>
                    @endforeach
                </select>
                <small id="kfsubcategory_idHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <input type="text" hidden name="kfind_id" value="{{ $indicateur->kfind_id }}">
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection  