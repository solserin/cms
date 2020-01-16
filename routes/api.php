<?php

use Illuminate\Http\Request;

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

/**ruta para obtener tokens */
Route::post('oauth/token','\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');


/**RUTAS PARA EL SISTEMA DE LOGUEADO*/
Route::middleware(['auth:api'])->group(function () {
    Route::get('/user',function(Request $request){
        return $request->user();
    });
    Route::post('logout_usuario','UsuariosController@logout_usuario');
});



Route::post('login_usuario','UsuariosController@login_usuario');
Route::resource('usuarios', 'UsuariosController',['only'=>['index']]);


/**FIN DE RUTAS DEL SISTEMA DE LOGUEADO */
