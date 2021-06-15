@extends('layoutHome')
@section('title', 'Welcome to OCHA ROWCA IMU')
@section('content')
    <div class="col">
        <div class="row">
            <div class="col">
            <br/>
                <figure class="text-center text-white">
                    <blockquote class="blockquote">
                        <p>OCHA ROWCA</p>
                        
                        <h1 class="display-3">
                            West and Central Africa Data repository<br/>
                            Explore, Find, Analyze and Download<br/>
                            latest Key figures, trends and<br/>
                            among crisis and countries in the region
                        </h1>
                    </blockquote>
                    <br/>
                    <figcaption class="blockquote-footer text-white">
                    The Development of this tool comes with the Objective of allowing non-Technical users <br/>
                    to have access to basic figures and data related to humanitarian activities <br/>
                    in the Central and West African Region (WCA) though a single link. <br/>
                    Data is available on tabular , chart and map format and can be exported and shared internally.<br/>
                    All data is curated by OCHA ROWCA IMU ensuring robustness of datasets (Pcode alignment, naming convention). <br/>
                    When data can be automatically extracted though, API have been used (under development).
                    </figcaption>
                    <br/>
                    <figcaption class="blockquote-footer text-white">
                    Feedback  and customization needs ? contact as at <strong>colombor@un.org</strong>
                    </figcaption>
                    <a href="/database" class="btn btn-primary" style="background-color:#E56A54;border:none;">Start</a>
                </figure>
            </div>
        </div>
    </div>
@endsection

