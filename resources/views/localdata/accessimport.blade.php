@extends('layout')
@section('title', 'Authentification screen')
@section('content')
    <div class="col">
        <div class="row">
            <div class="col-12">
                <img src="{{asset('images/shield.png')}}" class="rounded mx-auto d-block" alt="logo ocha"/>
            </div>
            <div class="col-12">
                @if (Session::has('msg'))
                        <div class="alert alert-danger" role="alert">
                        {!! Session::has('msg') ? Session::get("msg") : '' !!}
                    </div>
                @endif
                <form action="/accessimport" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputPassword1">Enter password for access</label>
                        <input type="password" name="password" class="form-control w-25" id="exampleInputPassword1" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary" style="background-color:#418fde;border:none;">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection