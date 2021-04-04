<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});


Route::group([
    'middleware' => 'api',
    'middleware' => 'auth:api',
    'prefix'=>'tuteur'
], function ($router) {
    Route::post('addDisponibilite', 'TuteurController@addDisponibilite');
    Route::post('editPeriode/{PeriodeID}', 'TuteurController@editPeriode');
    Route::get('getPeriodes', 'TuteurController@getPeriodes');
    Route::get('getPeriodesWithDates', 'TuteurController@getPeriodesWithDates');
    Route::get('deletePeriode/{PeriodeID}', 'TuteurController@deletePeriode'); 

    

    Route::get('getDatesByPeriodeID/{PeriodeID}', 'TuteurController@getDatesByPeriodeID');
    Route::get('getSessionsByDateID/{DateID}', 'TuteurController@getSessionsByDateID');
    Route::get('getDateByID/{DateID}', 'TuteurController@getDateByID'); 
    Route::post('addSession', 'TuteurController@addSession'); 
    Route::post('editSession', 'TuteurController@editSession'); 
    Route::get('deleteSession/{SessionID}', 'TuteurController@deleteSession'); 
    Route::get('getPeriodeWithDates/{PeriodeID}', 'TuteurController@getPeriodeWithDates'); 
    

    Route::get('getSessionByIDTest/{SessionID}', 'TuteurController@getSessionByIDTest'); 
    Route::get('getTuteurMesSessionsReserve', 'TuteurController@getTuteurMesSessionsReserve'); 

    

    
});


Route::group([
    'middleware' => 'api',
    'middleware' => 'auth:api',
    'prefix'=>'client'
], function ($router) {
    Route::post('getTuteursByPeriodeRange', 'ClientController@getTuteursByPeriodeRange');
    Route::post('getSessionsByRangeAndByTuteurID', 'ClientController@getSessionsByRangeAndByTuteurID');
    Route::get('reserver/{SessionID}', 'ClientController@reserver');
    Route::get('annulerReservation/{SessionID}', 'ClientController@annulerReservation');
    Route::get('getMesSessionsReserve', 'ClientController@getMesSessionsReserve');
    
});





Route::group([
    'middleware' => 'api',
    'middleware' => 'auth:api'
], function ($router) {
    Route::get('test', 'TestController@test');
});


/*
Route::group([
   // 'middleware' => 'api',
], function ($router) {
    Route::get('test', 'AuthController@test');
});
*/

Route::group([
     'middleware' => 'api',
 ], function ($router) {
     Route::get('inscription', 'TestController@registerTemporelle');
 });


