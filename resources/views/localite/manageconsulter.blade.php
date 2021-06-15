@extends('layout')
@section('title', $datas->local_name)
@section('content')
    <div class="col">
        <div class="row">
            <div class="col">
                <h3>{{ $datas->local_name }}</h3>
                <p>{{ $datas->local_pcode }} (admin {{ $datas->local_admin_level }})</p>
                <p>{{ $datas->local_country }} (admin {{ $datas->local_country }})</p>
                <p><em><a href="/edit/localite/{{ $datas->local_id }}">Edit</a> or <a href="/delete/localite/{{ $datas->local_id }}">Delete</a> the locality</em></p>
            </div>
        </div>
    </div>
@endsection           