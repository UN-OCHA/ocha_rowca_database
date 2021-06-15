<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\localDataController;
use App\Http\Controllers\zoneController;
use App\Http\Controllers\localiteController;
use App\Http\Controllers\robotController;
use App\Http\Controllers\outilController;
use App\Http\Controllers\migrationController;
use App\Http\Controllers\indicateurController;
use App\Http\Controllers\UploadFileController;
use App\Http\Controllers\kf_subcategoryController;
use App\Http\Controllers\kf_categController;
use App\Http\Controllers\headerController;



Route::get('/', function () {
    return view('welcome');
});


Route::get('/phpinfo', function () {
    return phpinfo();
});

Route::get('/migrate', [migrationController::class, 'getOrsDatas']);
Route::get('/indicateurs', [indicateurController::class, 'liste']);
Route::get('/indicateur/{id}', [indicateurController::class, 'show_view_consulter']);
Route::get('/edit/indicateur/{id}', [indicateurController::class, 'show_view_modifier']);
Route::get('/add/indicateur', [indicateurController::class, 'show_view_ajouter']);
Route::get('/delete/indicateur/{id}',  [indicateurController::class, 'show_view_delete']);

Route::get('/uploadfile',[UploadFileController::class, 'index']);
Route::get('/test',[UploadFileController::class, 'test']);

Route::get('/subcategories', [kf_subcategoryController::class, 'liste']);
Route::get('/subcategory/{id}', [kf_subcategoryController::class, 'show_view_consulter']);
Route::get('/edit/subcategory/{id}', [kf_subcategoryController::class, 'show_view_modifier']);
Route::get('/add/subcategory', [kf_subcategoryController::class, 'show_view_ajouter']);
Route::get('/delete/subcategory/{id}', [kf_subcategoryController::class, 'show_view_delete']);

Route::get('/categories', [kf_categController::class, 'liste']);
Route::get('/category/{id}', [kf_categController::class, 'show_view_consulter']);
Route::get('/edit/category/{id}', [kf_categController::class, 'show_view_modifier']);
Route::get('/add/category', [kf_categController::class, 'show_view_ajouter']);
Route::get('/delete/category/{id}', [kf_categController::class, 'show_view_delete']);

Route::get('/headers', [headerController::class, 'liste']);
Route::get('/header/{id}', [headerController::class, 'show_view_consulter']);
Route::get('/delete/header/{id}', [headerController::class, 'show_view_delete']);

//Route::get('/database','localDataController@show_view_database');
Route::get('/database',[localDataController::class, 'show_view_database']);
Route::get('/confirmimport/{element}',[localDataController::class, 'show_view_confirm_import']);
Route::get('/accessimport',[localDataController::class, 'show_view_access_import']);
Route::get('/accessmanage',[localDataController::class, 'show_view_access_manage']);
Route::get('/import',[localDataController::class, 'show_view_import']);
Route::get('/import/caseloads',[localDataController::class, 'import_caseloads']);
Route::get('/import/informSahel',[localDataController::class, 'import_inform_sahel']);
//Route::get('/import/idps',[localDataController::class, 'import_internally_displaced_person']);
Route::get('/import/nutrition',[localDataController::class, 'import_nutrition']);
Route::get('/import/ch',[localDataController::class, 'import_cadre_harmonise']);
//Route::get('/import/fs',[localDataController::class, 'import_food_security']);
Route::get('/import/disp',[localDataController::class, 'import_displacement']);

Route::get('/zones',[zoneController::class, 'liste']);
Route::get('/managezones',[zoneController::class, 'manageliste']);
Route::get('/analyserzone/{id}', [zoneController::class, 'show_view_analyser']);
Route::get('/zone/charts/{id}', [zoneController::class, 'show_view_charts']);
Route::get('/adavancedanalysis',  [zoneController::class, 'show_view_analyser_avance']);
Route::get('/zone/{id}', [zoneController::class, 'show_view_consulter']);
Route::get('/managezone/{id}', [zoneController::class, 'show_view_manage_consulter']);
Route::get('/edit/zone/{id}', [zoneController::class, 'show_view_modifier']);
Route::get('/add/zone',  [zoneController::class, 'show_view_ajouter']);
Route::get('/delete/zone/{id}', [zoneController::class, 'show_view_delete']);
Route::get('/filter/{category}/{items}', [zoneController::class, 'show_view_filter']);
Route::get('/filterV2/{category}/{items}/{periodFrom}/{periodTo}/{adminLevel}', [zoneController::class, 'show_view_filterV2']);


Route::get('/localites', [localiteController::class, 'liste']);
Route::get('/localite/{id}', [localiteController::class, 'show_view_consulter']);
Route::get('/localite/charts/{id}', [localiteController::class, 'show_view_charts']);
Route::get('/analyserlocalite/{id}', [localiteController::class, 'show_view_analyser']);
Route::get('/managelocalite/{id}', [localiteController::class, 'show_view_manage_consulter']);
Route::get('/edit/localite/{id}', [localiteController::class, 'show_view_modifier']);
Route::get('/add/localite/{id}', [localiteController::class, 'show_view_ajouter']);
Route::get('/delete/localite/{id}', [localiteController::class, 'show_view_delete']);



//LOGIN
Route::get('/logout', function () {
    request()->session()->flush();
    return redirect("/database");
});


//ROBOTS
Route::get('/getAPIDatas',[robotController::class, 'getAPIDatas']);
Route::get('/delete/all',[outilController::class, 'deleteall']);



//POSTS
Route::post('/uploadfile',[UploadFileController::class, 'showUploadFile']);
Route::post('/importData',[UploadFileController::class, 'importData']);
Route::post('/listIndcators',[QueryDatabaseController::class, 'getFKIndicatorList']);
Route::post('/getTypeHeaders',[QueryDatabaseController::class, 'getTypeHeaders']);
Route::post('/getHeaders',[QueryDatabaseController::class, 'getHeaders']);
Route::post('/getDisaggregations',[QueryDatabaseController::class, 'getDisaggregations']);

Route::post('/update/indicateur',[indicateurController::class, 'update']);
Route::post('/add/indicateur',[indicateurController::class, 'add']);
Route::post('/delete/indicateur',[indicateurController::class, 'delete']);
Route::post('/massdelete/indicateur',[indicateurController::class, 'massdelete']);

Route::post('/update/subcategory',[kf_subcategoryController::class, 'update']);
Route::post('/add/subcategory',[kf_subcategoryController::class, 'add']);
Route::post('/delete/subcategory',[kf_subcategoryController::class, 'delete']);
Route::post('/massdelete/subcategory',[kf_subcategoryController::class, 'massdelete']);

Route::post('/update/category',[kf_categController::class, 'update']);
Route::post('/add/category',[kf_categController::class, 'add']);
Route::post('/delete/category',[kf_categController::class, 'delete']);
Route::post('/massdelete/category',[kf_categController::class, 'massdelete']);
Route::post('/massdelete/subcategory',[kf_categController::class, 'massdelete']);




Route::post('/update/zone',[zoneController::class, 'update']);
Route::post('/add/zone',[zoneController::class, 'add']);
Route::post('/delete/zone',[zoneController::class, 'delete']);
Route::post('/massdelete/zone',[zoneController::class, 'massdelete']);

Route::post('/update/localite',[localiteController::class, 'update']);
Route::post('/add/localite',[localiteController::class, 'add']);
Route::post('/delete/localite',[localiteController::class, 'delete']);
Route::post('/massdelete/localite',[localiteController::class, 'massdelete']);





Route::post('/delete/header',[headerController::class, 'massdelete']);
Route::post('/massdelete/header',[headerController::class, 'massdelete']);

Route::post('/database/guide_import',[localDataController::class, 'guide_import']);
Route::post('/accessimport',[localDataController::class, 'verifyaccessimport']);
Route::post('/accessmanage',[localDataController::class, 'verifyaccessmanage']);