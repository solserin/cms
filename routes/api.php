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
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');

Route::get('cementerio/documento_titulo', 'CementerioController@documento_titulo');
/**rutas de modulo en proceso */
/**rutas publicas_ entran sin token */
Route::get('pagos/get_pagos/{id_pago?}/{paginated?}/{ver_subpagos?}', 'PagosController@get_pagos');
Route::get('cementerio/get_ventas/{id_venta?}/{paginated?}/', 'CementerioController@get_ventas');
/**fin de rutas de modulo en proceso */

Route::get('pagos/calcular_adeudo/{referencia}/{fecha_pago}/{multipago?}', 'PagosController@calcular_adeudo');

/**RUTAS PARA EL SISTEMA DE LOGUEADO*/
Route::middleware(['auth:api'])->group(function () {
    Route::post('cementerio/control_ventas/{tipo_servicio}', 'CementerioController@control_ventas'); //agregar,modificar
    Route::post('logout_usuario', 'Usuarios\UsuariosController@logout_usuario');

    /**RUTA PARA OBTENER LOS PUESTOS DISPONIBLEN EN LA EMPRESA */
    /**RUTA PARA OBTENER LOS PERMISOS DEL USUARIO */
    Route::get('get_permisos', 'Usuarios\UsuariosController@get_permisos');
    /**RUTA PARA OBTENER EL PERFIL DEL USUARIO */
    Route::get('get_perfil', 'Usuarios\UsuariosController@get_perfil');
    /**RUTA PARA OBTENER LOS DATOS DEL USUARIO */
    Route::get('get_usuarioById', 'Usuarios\UsuariosController@get_usuarioById');


    /**LAS RUTAS QUE ESTAN CONTROLADAS CON EL MIDDLEWARE "permiso" RECIBEN 2 PARAMETROS
     * EL PRIMERO ES EL NUMERO DEL MODULO
     * //4 permisos (1-Agregar, 2-Editar, 3-Eliminar y 4-Consultar)
     * EL SEGUNDO EL NUMERO DE PERMISO
     */
    Route::get('get_puestos', 'Usuarios\UsuariosController@get_puestos');
    Route::get('get_usuarios', 'Usuarios\UsuariosController@index');
    Route::post('add_usuario', 'Usuarios\UsuariosController@add_usuario');
    Route::post('update_usuario', 'Usuarios\UsuariosController@update_usuario');
    Route::post('delete_usuario', 'Usuarios\UsuariosController@delete_usuario');
    Route::post('activate_usuario', 'Usuarios\UsuariosController@activate_usuario');
    Route::get('usuarios_pdfs', 'Usuarios\UsuariosController@pdfs');
    Route::post('/usuarios/actualizar_perfil', 'Usuarios\UsuariosController@actualizar_perfil');
    /**verificar el password del usuario recibe el request del token y el password */
    Route::post('verificar_password', 'Usuarios\UsuariosController@verificar_password');


    /**RUTAS PARA ROLES */
    Route::get('get_roles', 'Usuarios\RolesController@index');
    Route::get('get_roles_lista', 'Usuarios\RolesController@get_roles');
    Route::get('get_rol_id', 'Usuarios\RolesController@get_rol_id');
    Route::get('get_modulos_permisos', 'Usuarios\RolesController@get_modulos_permisos');
    Route::get('get_modulos_urls_permisos', 'Usuarios\RolesController@get_modulos_urls_permisos');
    Route::get('get_modulos', 'Usuarios\RolesController@get_modulos');
    Route::get('get_rol_permisos', 'Usuarios\RolesController@get_rol_permisos');
    Route::post('add_roles', 'Usuarios\RolesController@add_roles');
    Route::post('update_rol', 'Usuarios\RolesController@update_rol');
    Route::post('delete_rol', 'Usuarios\RolesController@delete_rol');


    //Empresa
    Route::get('empresa/get_datos_empresa', 'EmpresaController@get_datos_empresa');
    Route::get('empresa/get_regimenes', 'EmpresaController@get_regimenes');
    Route::post('empresa/modificar_datos', 'EmpresaController@modificar_datos');
    Route::get('empresa/funeraria', 'EmpresaController@get');
    Route::post('empresa/funeraria', 'EmpresaController@save');
    Route::post('empresa/registro-publico', 'EmpresaController@saveRegistroPublico');
    Route::get('empresa/registro-publico', 'EmpresaController@getRegistroPublico');
    //Cementerio
    Route::get('empresa/cementerio', 'EmpresaController@getCementerio');
    Route::post('empresa/cementerio', 'EmpresaController@saveCementerio');
    //crematorio
    Route::get('empresa/crematorio', 'EmpresaController@getCrematorio');
    Route::post('empresa/crematorio', 'EmpresaController@saveCrematorio');
    //velatorio
    Route::get('empresa/velatorio', 'EmpresaController@getVelatorio');
    Route::post('empresa/velatorio', 'EmpresaController@saveVelatorio');
    //validate cer file
    Route::post('empresa/facturacion/validateCER', 'EmpresaController@validateCERFile');
    Route::post('empresa/facturacion/validateKEY', 'EmpresaController@validateKEYFile');
    Route::post('empresa/facturacion', 'EmpresaController@saveFacturacion');
    ///SAT
    Route::get('regimenes/', 'SATRegimenesController@getAll');
    Route::get('monedas/', 'SATMonedasController@getAll');

    /**CLIENTES */
    Route::get('/clientes/get_clientes', 'ClientesController@get_clientes');
    Route::get('/clientes/get_nacionalidades', 'ClientesController@get_nacionalidades');
    Route::post('/clientes/guardar_cliente', 'ClientesController@guardar_cliente');
    Route::post('/clientes/modificar_cliente', 'ClientesController@modificar_cliente');
    Route::get('/clientes/get_cliente_id', 'ClientesController@get_cliente_id');
    Route::post('/clientes/baja_cliente', 'ClientesController@baja_cliente');
    Route::post('/clientes/alta_cliente', 'ClientesController@alta_cliente');

    /**rutas del cementerio */
    Route::post('cementerio/registrar_precio_propiedad', 'CementerioController@registrar_precio_propiedad');
    Route::post('cementerio/update_precio_propiedad', 'CementerioController@update_precio_propiedad');
    Route::post('cementerio/enable_disable', 'CementerioController@enable_disable');
    Route::get('cementerio/get_financiamientos', 'CementerioController@get_financiamientos');
    Route::get('cementerio/get_tipo_propiedades', 'CementerioController@get_tipo_propiedades');
    Route::get('cementerio/get_precio_by_id', 'CementerioController@get_precio_by_id');
    Route::get('cementerio/lista_precios_pdf/{idioma?}', 'CementerioController@lista_precios_pdf');
    Route::get('cementerio/get_cementerio', 'CementerioController@get_cementerio');
    Route::get('cementerio/get_vendedores', 'CementerioController@get_vendedores');
    Route::get('titulos/{operacion_id?}', 'CementerioController@generarNumeroTitulo');
    Route::get('cementerio/documento_estado_de_cuenta_cementerio', 'CementerioController@documento_estado_de_cuenta_cementerio');
    Route::get('cementerio/acuse_cancelacion', 'CementerioController@acuse_cancelacion');


    Route::get('cementerio/documento_solicitud', 'CementerioController@documento_solicitud');
    Route::get('cementerio/documento_convenio', 'CementerioController@documento_convenio');


    Route::get('cementerio/referencias_de_pago/{id_pago?}', 'CementerioController@referencias_de_pago');
    Route::get('cementerio/reglamento_pago', 'CementerioController@reglamento_pago');




    /**rutas de pagos */
    Route::post('pagos/guardar_pago', 'PagosController@guardar_pago');

    Route::get('pagos/get_formas_pago_sat', 'PagosController@get_formas_pago_sat');
    Route::get('pagos/get_monedas_sat', 'PagosController@get_monedas_sat');
    Route::get('pagos/get_cobradores', 'PagosController@get_cobradores');
    Route::get('pagos/recibo_de_pago/{id_pago?}', 'PagosController@recibo_de_pago');
    Route::post('pagos/cancelar_pago', 'PagosController@cancelar_pago');
    /**fin de rutas de pagos */




    Route::get('generarNumeroTitulo', 'CementerioController@generarNumeroTitulo');

    Route::get('inventarios/cementerio/propiedadesById', 'CementerioController@propiedadesById');
    Route::get('inventarios/cementerio/get_propiedades_by_tipo', 'CementerioController@get_propiedades_by_tipo');
    Route::get('inventarios/cementerio/get_usuarios_para_vendedores', 'CementerioController@get_usuarios_para_vendedores');
    Route::get('inventarios/cementerio/tipoPropiedades', 'CementerioController@tipoPropiedades');
    Route::get('inventarios/cementerio/get_ventas_referencias_propiedades', 'CementerioController@get_ventas_referencias_propiedades');
    Route::get('inventarios/cementerio/get_columna_fila_terraza', 'CementerioController@get_columna_fila_terraza');
    Route::get('inventarios/cementerio/precios_tarifas', 'CementerioController@precios_tarifas');
    Route::post('inventarios/cementerio/actualizar_precios_tarifas', 'CementerioController@actualizar_precios_tarifas');
    Route::get('inventarios/cementerio/get_cementerio', 'CementerioController@get_cementerio');

    Route::get('inventarios/cementerio/get_sat_formas_pago', 'CementerioController@get_sat_formas_pago');
    Route::post('cementerio/cancelar_venta', 'CementerioController@cancelar_venta');
    Route::get('inventarios/cementerio/get_antiguedades_venta', 'CementerioController@get_antiguedades_venta');










    /**fin de rutas del cementerio */
});


Route::get('inventarios/cementerio/documento_ubicacion_terreno', 'CementerioController@documento_ubicacion_terreno');

Route::get('pdfs', 'Usuarios\UsuariosController@pdfs');

//RUTA DEL LOGUIN
Route::post('login_usuario', 'Usuarios\UsuariosController@login_usuario');
//RUTA DEL REFRESH TOKEN
Route::post('refresh_token', 'Usuarios\UsuariosController@refresh_token');

//RECUPERAR CONTRASEÑA
Route::post('/password/email', 'Auth\Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Auth\Api\ResetPasswordController@reset');

Route::get('ubicacion_texto', 'CementerioController@ubicacion_texto'); //borrar



//Route::resource('usuarios', 'UsuariosController',['only'=>['index']]);


/**FIN DE RUTAS DEL SISTEMA DE LOGUEADO */