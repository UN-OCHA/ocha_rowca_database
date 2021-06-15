<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="{{ csrf_token() }}">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }


            #dropHere{
            border: 3px dashed #BBBBBB;
            line-height:50px;
            text-align: center;
            }

            .resultLog{
                background-color: #f1f1f1;
                border-radius: 5px;
                padding: 5px;
                border: 1px #d8d8d8 solid;
            }

            .titreBlocLog{
                font-weight: bold;
            }
        </style>

        <script lang="javascript" src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
        <script lang="javascript" src="{{asset('js/xlsx.full.min.js')}}"></script>
        <script lang="javascript" src="{{asset('bootstrap/js/bootstrap.min.js')}}"></script>
    </head>
    <body>
        <div class="container-fluid">
            <h1 class="display-4">OCHA Rowca Database</h1>
            <blockquote class="blockquote">
                <p class="mb-0">Upload Excel sheets and select which data to import.</p>
            </blockquote>
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-upload-tab" data-toggle="pill" href="#pills-upload" role="tab" aria-controls="pills-upload" aria-selected="true">Upload</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" id="pills-select-tab" data-toggle="pill" href="#pills-select" role="tab" aria-controls="pills-select" aria-selected="false">Select data</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" id="pills-import-tab" data-toggle="pill" href="#pills-import" role="tab" aria-controls="pills-import" aria-selected="false">Import</a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-upload" role="tabpanel" aria-labelledby="pills-upload-tab">
                    <div class="flex-center position-ref full-height" id='dropHere'>
                    Drop here
                    </div>
                    <div id='filesInfo'>
                      
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-select" role="tabpanel" aria-labelledby="pills-select-tab">
                    <div class="row">
                    <div class="col-2">
                        <div class="nav flex-column nav-pills" id="sheet-pills-tab" role="tablist" aria-orientation="vertical">
                            feuilles
                        </div>
                    </div>
                    <div class="col-10">
                        <div class="tab-content" id="sheet-pills-tabContent">
                        </div>
                    </div>
                    </div>
                
                </div>
                <div class="tab-pane fade" id="pills-import" role="tabpanel" aria-labelledby="pills-import-tab">
                    
                    <div class='col-12' >
                    <button type="button" onclick='launchImport()' class="btn btn-secondary btn-lg btn-block">Launch</button>
                    </div>
                    <div class='col-12' id='importLog'>
                   
                    </div>
                </div>
            </div>
        </div>


        <script>

            //RECUPERATION DES DONNEES DE LA BASE
            var indicators = null;
            var db_typeHeaders = null;
            var db_headears = null;
            var db_disaggregations = null;
            var workbook = null;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            
            jQuery.ajax({ url: "{{ url('/listIndcators') }}",async : false, method: 'post',data: {},success: function(result){indicators = JSON.parse(result);}});
            jQuery.ajax({ url: "{{ url('/getTypeHeaders') }}",async : false, method: 'post',data: {},success: function(result){db_typeHeaders = JSON.parse(result); console.log(db_typeHeaders);}});
            jQuery.ajax({ url: "{{ url('/getHeaders') }}",async : false, method: 'post',data: {},success: function(result){db_headears = JSON.parse(result);}});
            jQuery.ajax({ url: "{{ url('/getDisaggregations') }}",async : false, method: 'post',data: {},success: function(result){console.log(result);db_disaggregations = JSON.parse(result);}});
            //FIN

           
            

            //ACTIVATION DU DROP
            var drop_dom_element = document.getElementById("dropHere");
            var rABS = true; // true: readAsBinaryString ; false: readAsArrayBuffer
            function handleDrop(e) {
                e.stopPropagation(); e.preventDefault();
                var files = e.dataTransfer.files, f = files[0];
                var reader = new FileReader();
                reader.onload = function(e) {
                    var data = e.target.result;
                    if(!rABS) data = new Uint8Array(data);
                    var workbook = XLSX.read(data, {type: rABS ? 'binary' : 'array'});
                    
                    //console.log(workbook);
                    /* DO SOMETHING WITH workbook HERE */
                };
                if(rABS) reader.readAsBinaryString(f); else reader.readAsArrayBuffer(f);
            }


            //EVENEMENTS DU DROP
            $(document).on('dragenter', '#dropHere', function() {
                        $(this).css('border', '3px dashed red');
                        return false;
            });
            $(document).on('dragover', '#dropHere', function(e){
                e.preventDefault();
                e.stopPropagation();
                $(this).css('border', '3px dashed red');
                return false;
            });
            $(document).on('dragleave', '#dropHere', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).css('border', '3px dashed #BBBBBB');
                        return false;
            });
            $(document).on('drop', '#dropHere', function(e) {
                if(e.originalEvent.dataTransfer){
                    if(e.originalEvent.dataTransfer.files.length) {
                        // Stop the propagation of the event
                        e.preventDefault();
                        e.stopPropagation();

                        $(this).css('border', '3px dashed green');
                        // Main function to upload
                        //upload(e.originalEvent.dataTransfer.files);

                        var files = e.originalEvent.dataTransfer.files;
                        var f = files[0];
                        var reader = new FileReader();

                        reader.onload = function(e) {
                            var data = e.target.result;
                            if(!rABS) data = new Uint8Array(data);
                            workbook = XLSX.read(data, {type: rABS ? 'binary' : 'array'});
                            
                            $( "#pills-select-tab" ).removeClass("disabled");
                            $( "#pills-import-tab" ).removeClass("disabled");
                            
                            
                            
                            //INFORMATIONS DU FICHIER
                            $( "#dropHere" ).hide();
                            $( "#filesInfo" ).show();
                            var user = workbook.Props.LastAuthor;
                            var date = workbook.Props.ModifiedDate;
                            $( "#filesInfo" ).html("<p class='font-weight-normal'>Informations sur le fichier</p><p><em>Dernier modification : "+user+" le "+date+"</em></br><em>Feuilles : test</em></p>");
                        
                        
                            var idSheet = 0;
                            var sheetActive = "show active";
                            var buttonSheetActive = "active";
                            var ariaSelected = true;
                            $.each(workbook.SheetNames, function(i, sheetname){
                                if(idSheet!=0){
                                    sheetActive ="";  
                                    buttonSheetActive = "";
                                    ariaSelected = false;                                          
                                }
                                
                                $("#sheet-pills-tab").append("<a class='nav-link "+buttonSheetActive+"' id='v-pills-sheet"+idSheet+"-tab' data-toggle='pill' href='#v-pills-sheet"+idSheet+"' role='tab' aria-controls='v-pills-sheet"+idSheet+"' aria-selected='"+ariaSelected+"'>"+sheetname+"</a>");
                                $("#sheet-pills-tab").append("<input type='text' hidden id='sheet"+idSheet+"' value='"+sheetname+"'/>");
                                $("#sheet-pills-tabContent").append("<div class='tab-pane fade "+sheetActive+"' id='v-pills-sheet"+idSheet+"' role='tabpanel' aria-labelledby='v-pills-sheet"+idSheet+"-tab'><form><div class='form-check btn btn-danger btn-lg btn-block' id='blocCheckSheet"+idSheet+"'><input type='checkbox' class='form-check-input checkImportSheet' id='checkImportSheet"+idSheet+"' onclick='checkImportSheet(this,\""+idSheet+"\")'><label class='form-check-label' for='exampleCheck1'>Importer cette feuille</label></div></form><table class='table' ><thead class='sthead-dark'><tr><th scope='col'>?</th><th scope='col'>Header</th><th scope='col'>Type</th><th scope='col'>Correspond to</th><th scope='col'>Is disaggregation of</th></tr></thead><tbody></tbody></table></div>");
                                
                                ws = workbook.Sheets[sheetname];
                                headers = XLSX.utils.sheet_to_json(ws, {header:1})[0];
            
                                var j;
                                for (j = 0; j < headers.length; j++) {
                                    content = "<tr id='line"+idSheet+j+"'>";
                                    content += "<td><div class='form-check'><input type='checkbox' onclick='checkImport(this,\""+idSheet+j+"\")' class='form-check-input checkImportColomn"+idSheet+"' id='checkImportColomn"+idSheet+j+"'></div></form></td>";
                                    content += "<td>"+headers[j]+"<input type='text' hidden id='HeaderName"+idSheet+j+"' value='"+headers[j]+"'/></td>";
                                    content += "<td><select style='display:none;' class='form-control btn-danger' id='headerType"+idSheet+j+"' onchange='loadHeaderOptions(this,\""+idSheet+j+"\")'>";
                                    
                                    content += "</select>";
                                    content += "</td>";
                                    content += "<td><select style='display:none;' class='form-control btn-danger' onchange='loadDisaggregations(\""+idSheet+j+"\")' id='HeaderIndicator"+idSheet+j+"'></select></td>";
                                    content += "<td><select style='display:none;' class='form-control btn-danger' onchange='checkDisaggregationTargetValue(\""+idSheet+j+"\")' id='disaggregationTarget"+idSheet+j+"'></select></td>";
                                    content += "</tr>";

                                    $("#v-pills-sheet"+idSheet+" table tbody").append(content);
                                    var k;

                                        //console.log(db_headears);
                                    //AFFICHAGE DES HEADERS
                                    defaultExist = false;
                                    optionValues = "<option value=''>Select one</option>";
                                    for (k = 0; k < db_typeHeaders.length; k++) {
                                        selected = '';
                                        for (n = 0; n < db_headears.length; n++) {
                                            console.log(db_headears[n].header_name.trim()+" : "+headers[j]+" : "+db_typeHeaders[k].type_header_code.trim());
                                            if(
                                                (db_headears[n].header_name.trim() == headers[j])&&
                                                (db_headears[n].type_header_code == db_typeHeaders[k].type_header_code)){
                                                selected = 'selected';
                                                defaultExist =true;
                                            }
                                        }
                                        optionValues += "<option "+selected+" value='"+db_typeHeaders[k].type_header_code.trim()+"'>"+db_typeHeaders[k].type_header_name.trim()+"</option>";
                                    }
                                    $("#headerType"+idSheet+j).html(optionValues);
                                    if(defaultExist){
                                        $("#headerType"+idSheet+j).show();
                                        $("#headerType"+idSheet+j).removeClass('btn-danger');
                                        $("#line"+idSheet+j).addClass('table-success');
                                        $("#checkImportColomn"+idSheet+j).prop("checked", true);
                                    }


                                    //AFFICHAGE DES INDICATEURS
                                    defaultExist = false;
                                    optionValues = "<option value=''>Select one</option>";
                                    for (k = 0; k < indicators.length; k++) {
                                        selected = '';
                                        for (n = 0; n < db_headears.length; n++) {
                                            if(
                                                (db_headears[n].header_name.trim() == headers[j])&&
                                                (db_headears[n].kfind_id == indicators[k].kfind_id)){
                                                selected = 'selected';
                                                defaultExist = true;
                                            }
                                        }
                                        optionValues += "<option "+selected+" value='"+indicators[k].kfind_id+"'>"+indicators[k].kfindic_caption_fr+"</option>";
                                    }
                                    $("#HeaderIndicator"+idSheet+j).html(optionValues);
                                    if(defaultExist){
                                        $("#HeaderIndicator"+idSheet+j).show();
                                        $("#HeaderIndicator"+idSheet+j).removeClass('btn-danger');
                                    }


                                    //AFFICHAGE DES DISAGGREGATIONS TARGET
                                    optionValues = "<option value=''>Select one</option>";
                                    for (k = 0; k < headers.length; k++) {
                                        selected = '';
                                        optionValues += "<option "+selected+" value='"+headers[k]+"'>"+headers[k]+"</option>";
                                    }
                                    $("#disaggregationTarget"+idSheet+j).html(optionValues);
                                }

                                idSheet++;
                            });
                            /*
                            //RECHERCHE DES HEADERS DANS LES INDICATEURS
                            var jqxhr = $.post( "example.php", function() {
                            alert( "success" );
                            })
                            .done(function() {
                                alert( "second success" );
                            })
                            .fail(function() {
                                alert( "error" );
                            })
                            .always(function() {
                                alert( "finished" );
                            });
                            */
                            /* DO SOMETHING WITH workbook HERE */
                        };
                        if(rABS) reader.readAsBinaryString(f); else reader.readAsArrayBuffer(f);
                    }  
                }
                else {
                        $(this).css('border', '3px dashed #BBBBBB');
                }
                return false;
            });
            
            function checkImport(checkbox,index){
                //var ckbox = $('#checkImportColomn'+index);
                
                console.log("show:"+"#headerType"+index);
                if ($(checkbox).is(':checked')) {
                    $("#headerType"+index).show();
                    $("#line"+index).addClass("table-success");

                } else {
                    $("#headerType"+index).hide();
                    $("#HeaderIndicator"+index).hide();
                    $("#disaggregationTarget"+index).hide();
                    $("#line"+index).removeClass("table-success");
                }
        
                $("#headerType"+index+"").val('');
                $("#HeaderIndicator"+index+"").val('');
                $("#disaggregationTarget"+index+"").val('');
            }

            function checkImportSheet(checkbox,index){
                
                if ($(checkbox).is(':checked')) {
                    $("#blocCheckSheet"+index).removeClass("btn-danger");
                    $("#blocCheckSheet"+index).addClass("btn-success");
                } else {
                    $("#blocCheckSheet"+index).removeClass("btn-success");
                    $("#blocCheckSheet"+index).addClass("btn-danger");
                }
            }
            
            function loadHeaderOptions(SelectOption, index){
                optionValues = "<option value=''>Select one</option>";
                $("#headerType"+index).removeClass("btn-danger");
                $("#HeaderIndicator"+index).addClass("btn-danger");
                $("#disaggregationTarget"+index+"").hide();
                switch(SelectOption.value){
                    case 'location':
                        optionValues+="<option value='pcodeAdmin0'>Pcode iso3 Admin0</option>";
                        optionValues+="<option value='pcodeAdmin1'>Pcode iso3 Admin1</option>";
                        optionValues+="<option value='pcodeAdmin2'>Pcode iso3 Admin2</option>";
                        optionValues+="<option value='pcodeAdmin3'>Pcode iso3 Admin3</option>";
                        optionValues+="<option value='pcodeAdmin4'>Pcode iso3 Admin4</option>";
                        optionValues+="<option value='pcodeAdmin5'>Pcode iso3 Admin5</option>";
                        optionValues+="<option value='labelAdmin0'>Label Admin0</option>";
                        optionValues+="<option value='labelAdmin1'>Label Admin1</option>";
                        optionValues+="<option value='labelAdmin2'>Label Admin2</option>";
                        optionValues+="<option value='labelAdmin3'>Label Admin3</option>";
                        optionValues+="<option value='labelAdmin4'>Label Admin4</option>";
                        optionValues+="<option value='labelAdmin5'>Label Admin5</option>";
                    break;
                    case 'date':
                        optionValues+="<option value='AAAA'>Année</option>";
                        optionValues+="<option value='AAAA-MM-DD'>Année-Mois-Jour</option>";
                        optionValues+="<option value='DD-MM-AAAA'>Jour-Mois-Année</option>";
                    break;
                    case 'keyFigure':
                        console.log(indicators);
                        for (k = 0; k < indicators.length; k++) {
                            optionValues+="<option value='"+indicators[k].kfind_id+"'>"+indicators[k].kfindic_caption_fr+"</option>";
                        }
                    case 'disaggregation':
                        for (k = 0; k < db_disaggregations.length; k++) {
                            optionValues+="<option value='"+db_disaggregations[k].id_disaggregation+"'>"+db_disaggregations[k].label_disaggregation+"</option>";
                        }
                    break;
                }

                if(SelectOption.value==''){
                    $("#headerType"+index).addClass("btn-danger");
                    $("#HeaderIndicator"+index).hide();
                }else{
                    $("#HeaderIndicator"+index).html(optionValues);
                    $("#HeaderIndicator"+index).show();
                }
            }
            
            function checkDisaggregationTargetValue(index){
                if($("#disaggregationTarget"+index).val()==''){
                    $("#disaggregationTarget"+index).addClass("btn-danger");
                }else{
                    $("#disaggregationTarget"+index).removeClass("btn-danger");
                }
            }
            
            function loadDisaggregations(index){
                $("#disaggregationTarget"+index+"").val('');
                $("#disaggregationTarget"+index).addClass("btn-danger");

                if($("#headerType"+index).val()=="disaggregation"){
                    if($("#HeaderIndicator"+index).val()==''){
                        $("#disaggregationTarget"+index+"").hide();
                        $("#HeaderIndicator"+index).addClass("btn-danger");
                    }else{
                        $("#HeaderIndicator"+index).removeClass("btn-danger");
                        $("#disaggregationTarget"+index+"").show();
                    }
                }else{
                    if($("#HeaderIndicator"+index).val()==''){
                        $("#HeaderIndicator"+index).addClass("btn-danger");
                    }else{
                        $("#HeaderIndicator"+index).removeClass("btn-danger");
                    }
                    $("#disaggregationTarget"+index).hide();
                }
            }

            function upload(files) {
                        var f = files[0] ;
                        console.log(f);
                        // Only process image files.
                        /*
                        if (!f.type.match('image/jpeg')) {
                                alert('The file must be a jpeg image') ;
                                return false ;
                        }*/
                        var reader = new FileReader();
            
                        // When the image is loaded,
                        // run handleReaderLoad function
                        //reader.onload = handleReaderLoad;
            
                        // Read in the image file as a data URL.
                        reader.readAsDataURL(f);            
            }

            function launchImport(fileInfo){
                idSheet = 0;
                ImportSheets = [];
                error = false;
                error_message = "</br>";

                $( ".checkImportSheet" ).each(function(l, sheet) {
                    if($(sheet).is(':checked')){
                        feuille = $("#sheet"+idSheet+"").val();
                        sheetData = null;
                        importInfo = [];
                        sheetInfo = [];
                        locationExist = false;
                        KeyFigureExist = false;

                        ws = workbook.Sheets[feuille];
                        sheetData = XLSX.utils.sheet_to_json(ws, {header:1});

                        idColumn = 0;
                        $( ".checkImportColomn"+ idSheet+"" ).each(function(m, column) {
                            if($(column).is(':checked')){
                                headerName = $("#HeaderName"+idSheet+idColumn+"").val();
                                headerIndex = idColumn;

                                headerType = $("#headerType"+idSheet+idColumn+"").val();
                                if(headerType==''){
                                    error = true;
                                    error_message += "<div class='alert alert-danger' role='alert'>Select a <span class='font-weight-bold'>type</span> for <span class='font-weight-bold'>\""+headerName+"\"</span> on the sheet <span class='font-weight-bold'>\""+feuille+"\"</span></div>";
                                }else{
                                    switch(headerType){
                                        case"location":
                                            locationExist = true;
                                        break;
                                        case"keyFigure":
                                            KeyFigureExist = true;
                                        break;
                                    }
                                }

                                HeaderIndicator = $("#HeaderIndicator"+idSheet+idColumn+"").val();
                                if(HeaderIndicator==''){
                                    error = true;
                                    error_message += "<div class='alert alert-danger' role='alert'>Select a <span class='font-weight-bold'>correspondence</span> for <span class='font-weight-bold'>\""+headerName+"\"</span> on the sheet <span class='font-weight-bold'>\""+feuille+"\"</span></div>";
                                }

                                disaggregationTarget = $("#disaggregationTarget"+idSheet+idColumn+"").val();
                                if(headerType=='disaggregation'){
                                    if(disaggregationTarget==''){
                                        error = true;
                                        error_message += "<div class='alert alert-danger' role='alert'>Select a <span class='font-weight-bold'>disaggregation</span> for<span class='font-weight-bold'> \""+headerName+"\"</span> on the sheet <span class='font-weight-bold'>\""+feuille+"\"</span></div>";
                                    }
                                }
                                

                                headerInfo = [];
                                headerInfo.push({headerName,headerName});
                                headerInfo.push({headerIndex,headerIndex});
                                headerInfo.push({headerType, headerType});
                                headerInfo.push({HeaderIndicator,HeaderIndicator});
                                headerInfo.push({disaggregationTarget,disaggregationTarget});
                                importInfo.push({headerInfo,headerInfo});
                            }
                            idColumn++;
                        });


                        if(locationExist==false){
                            error = true;
                            error_message += "<div class='alert alert-danger' role='alert'>Select at least one <span class='font-weight-bold'>location</span> on the sheet <span class='font-weight-bold'>\""+feuille+"\"</span></div>";
                        }
                        
                        if(KeyFigureExist==false){
                            error = true;
                            error_message += "<div class='alert alert-danger' role='alert'>Select at least one <span class='font-weight-bold'>Key figure</span> on the sheet <span class='font-weight-bold'>\""+feuille+"\"</span></div>";
                        }

                        sheetInfo.push({feuille, feuille});
                        sheetInfo.push({importInfo,importInfo});
                        sheetInfo.push({sheetData,sheetData});
                        ImportSheets.push({sheetInfo,sheetInfo});
                    }
                    //console.log(ImportSheets);
                    idSheet++;
                });

                console.log("prépartation import");


                if(ImportSheets.length != 0){
                    if(ImportSheets[0]['sheetInfo'][1]['importInfo']. length != 0){
                        if(error){
                            $("#importLog").html(error_message);
                        }else{
                            jQuery.ajax({ url: "{{ url('/importData') }}",async : false, method: 'post',data: {import:ImportSheets,fileInfo:fileInfo },success: function(result){
                                console.log(result);
                                log = "</br>";
                                if(result[0]['importLog']){
                                    log+="<div class='alert alert-success' role='alert'>Data imported successfully</div>";
                                }else{
                                    log+="<div class='alert alert-danger' role='alert'>An error accured, contact the Administrator</div>";
                                }

                                log+="<div class='resultLog'>"+result[0]['importLog']+"</div>";
                                $("#importLog").html(log);
                            }}).fail(function(jqXHR, textStatus) {
                                error = "</br><div class='alert alert-danger' role='alert'>An error occured, contact the administrator</div></br>";
                                error += "<samp>";
                                error += "Status code : <span class='text-danger'> "+jqXHR.status+"</span></br>";
                                if(jqXHR.responseJSON != undefined){
                                    error += "Exception : "+jqXHR.responseJSON.exception+"</br>";
                                    error += "File : "+jqXHR.responseJSON.file+"</br>";
                                    error += "Message : <p <span class='text-danger'>"+jqXHR.responseJSON.message+"</p></br></br>";
                                }
                                if(jqXHR.responseText != undefined){
                                    error += "responseText : "+jqXHR.responseText+"</br>";
                                }
                                
                                
                                
                                $("#importLog").html(error);
                            });
                        }
                        
                    }else{
                        $("#importLog").html("</br><div class='alert alert-danger' role='alert'>You haven't checked a field! Please check one or multiple fields on a sheet to import and try again</div>");
                    }
                    
                }else{
                    $("#importLog").html("</br><div class='alert alert-danger' role='alert'>No sheet selected! Please choose a sheet to import and try again</div>");
                }
            }

            function handleReaderLoad(evt) {
                var pic = {};
                pic.file = evt.target.result.split(',')[1];

                var str = jQuery.param(pic);

                $.ajax({
                        type: 'POST',
                        url: 'url_to_php_script.php',
                        data: str,
                        success: function(data) {
                                    do_something(data) ;
                        }
                });
            }
            //drop_dom_element.addEventListener('drop', handleDrop, false);
        </script>
    </body>
</html>
