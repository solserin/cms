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
    Route::post('logout_usuario','UsuariosController@logout_usuario');

    /**RUTA PARA OBTENER LOS PERMISOS DEL USUARIO */
    Route::get('get_permisos','UsuariosController@get_permisos');

    /**RUTA PARA OBTENER EL PERFIL DEL USUARIO */
    Route::get('get_perfil','UsuariosController@get_perfil');



    

    /**LAS RUTAS QUE ESTAN CONTROLADAS CON EL MIDDLEWARE "permiso" RECIBEN 2 PARAMETROS
     * EL PRIMERO ES EL NUMERO DEL MODULO
     * EL SEGUNDO EL NUMERO DE PERMISO
     */
    Route::get('usuarios', 'UsuariosController@index')->middleware('permiso:1,1');
});



//RUTA DEL LOGUIN
Route::post('login_usuario','UsuariosController@login_usuario');
//RUTA DEL REFRESH TOKEN
Route::post('refresh_token','UsuariosController@refresh_token');

//RECUPERAR CONTRASEÑA
Route::post('/password/email', 'Auth\Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Auth\Api\ResetPasswordController@reset');






//Route::resource('usuarios', 'UsuariosController',['only'=>['index']]);


/**FIN DE RUTAS DEL SISTEMA DE LOGUEADO */