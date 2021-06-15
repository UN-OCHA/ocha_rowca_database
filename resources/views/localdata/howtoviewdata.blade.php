@extends('layout')
@section('title', 'How to view data')
@section('content')
    <div class="col">
        <div class="row">
            <div class="card float-left m-1 shadow-sm mb-5 bg-white rounded" style="width: 15rem;">
                <div class="card-body">
                    <h5 class="card-title">View data</h5>
                    <p class="card-text">Consult data here, in Excel or Power BI</p>
                    <a href="#" class="btn btn-primary" style="background-color:#418fde;border:none;">...</a>
                </div>
            </div>
            <div class="card float-left m-1 shadow-sm mb-5 bg-white rounded" style="width: 15rem;">
                <div class="card-body">
                    <h5 class="card-title">Import data</h5>
                    <p class="card-text">For restricted users</p>
                    <a href="/accessimport" class="btn btn-primary"  style="background-color:#418fde;border:none;">Launch import</a>
                </div>
            </div>
        </div>
    </div>
@endsection