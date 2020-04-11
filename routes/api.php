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


/**RUTAS PARA EL SISTEMA DE LOGUEADO*/
Route::middleware(['auth:api'])->group(function () {
    Route::post('logout_usuario', 'Usuarios\UsuariosController@logout_usuario');

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
    Route::get('get_usuarios', 'Usuarios\UsuariosController@index');
    Route::post('add_usuario', 'Usuarios\UsuariosController@add_usuario')->middleware('permiso:1,1');
    Route::post('update_usuario', 'Usuarios\UsuariosController@update_usuario')->middleware('permiso:1,2');
    Route::post('delete_usuario', 'Usuarios\UsuariosController@delete_usuario')->middleware('permiso:1,3');
    Route::post('activate_usuario', 'Usuarios\UsuariosController@activate_usuario')->middleware('permiso:1,3');
    Route::get('usuarios_pdfs', 'Usuarios\UsuariosController@pdfs')->middleware('permiso:1,4');
    Route::post('/usuarios/actualizar_perfil', 'Usuarios\UsuariosController@actualizar_perfil');

    /**verificar el password del usuario recibe el request del token y el password */
    Route::post('verificar_password', 'Usuarios\UsuariosController@verificar_password');


    /**RUTAS PARA ROLES */
    Route::get('get_roles', 'Usuarios\RolesController@index');
    Route::get('get_modulos', 'Usuarios\RolesController@get_modulos');
    Route::get('get_rol_permisos', 'Usuarios\RolesController@get_rol_permisos');
    Route::post('add_rol', 'Usuarios\RolesController@add_rol')->middleware('permiso:1,1');
    Route::post('update_rol', 'Usuarios\RolesController@update_rol')->middleware('permiso:1,2');
    Route::post('delete_rol', 'Usuarios\RolesController@delete_rol')->middleware('permiso:1,3');

    //municipios y estados
    Route::get('estados/', 'EstadosController@getEstados');
    Route::get('municipios/{estadoId}', 'MunicipiosController@getMunicipios');
    Route::get('localidades/{municipioId}', 'LocalidadesController@getLocalidades');

    //Empresa
    Route::get('empresa/get_datos_empresa', 'EmpresaController@get_datos_empresa')->middleware('permiso:2,4');

    Route::get('empresa/get_regimenes', 'EmpresaController@get_regimenes')->middleware('permiso:2,4');
    Route::post('empresa/modificar_datos', 'EmpresaController@modificar_datos');




    Route::get('empresa/funeraria', 'EmpresaController@get')->middleware('permiso:2,4');
    Route::post('empresa/funeraria', 'EmpresaController@save')->middleware('permiso:2,2');
    Route::post('empresa/registro-publico', 'EmpresaController@saveRegistroPublico')->middleware('permiso:2,2');
    Route::get('empresa/registro-publico', 'EmpresaController@getRegistroPublico')->middleware('permiso:2,4');
    //Cementerio
    Route::get('empresa/cementerio', 'EmpresaController@getCementerio')->middleware('permiso:2,4');
    Route::post('empresa/cementerio', 'EmpresaController@saveCementerio')->middleware('permiso:2,2');
    //crematorio
    Route::get('empresa/crematorio', 'EmpresaController@getCrematorio')->middleware('permiso:2,4');
    Route::post('empresa/crematorio', 'EmpresaController@saveCrematorio')->middleware('permiso:2,2');
    //velatorio
    Route::get('empresa/velatorio', 'EmpresaController@getVelatorio')->middleware('permiso:2,4');
    Route::post('empresa/velatorio', 'EmpresaController@saveVelatorio')->middleware('permiso:2,2');
    //validate cer file
    Route::post('empresa/facturacion/validateCER', 'EmpresaController@validateCERFile');
    Route::post('empresa/facturacion/validateKEY', 'EmpresaController@validateKEYFile');
    Route::post('empresa/facturacion', 'EmpresaController@saveFacturacion')->middleware('permiso:2,2');

    ///SAT
    Route::get('regimenes/', 'SATRegimenesController@getAll');
    Route::get('monedas/', 'SATMonedasController@getAll');

    //Proveedores

    Route::post('empresa/inventario/proveedores', 'ProveedoresController@create')->middleware('permiso:5,1');
    Route::put('empresa/inventario/proveedores/{id}', 'ProveedoresController@save')->middleware('permiso:5,2');
    Route::get('empresa/inventario/proveedores', 'ProveedoresController@getAll')->middleware('permiso:5,4');
    Route::get('empresa/inventario/proveedores-pdf', 'ProveedoresController@getPDF')->middleware('permiso:5,4');
    Route::get('empresa/inventario/proveedores-active', 'ProveedoresController@getActive');
    Route::get('empresa/inventario/proveedor-pdf/{id}', 'ProveedoresController@proveedorPDF')->middleware('permiso:5,4');

    /**rutas del cementerio */
    Route::get('inventarios/cementerio/get_cementerio', 'CementerioController@get_cementerio');

    Route::get('inventarios/cementerio/propiedadesById', 'CementerioController@propiedadesById');
    Route::get('inventarios/cementerio/get_propiedades_by_tipo', 'CementerioController@get_propiedades_by_tipo');
    Route::get('inventarios/cementerio/get_usuarios_para_vendedores', 'CementerioController@get_usuarios_para_vendedores');
    Route::get('inventarios/cementerio/tipoPropiedades', 'CementerioController@tipoPropiedades');
    Route::get('inventarios/cementerio/get_ventas_referencias_propiedades', 'CementerioController@get_ventas_referencias_propiedades');
    Route::get('inventarios/cementerio/get_columna_fila_terraza', 'CementerioController@get_columna_fila_terraza');
    Route::get('inventarios/cementerio/precios_tarifas', 'CementerioController@precios_tarifas');
    Route::post('inventarios/cementerio/actualizar_precios_tarifas', 'CementerioController@actualizar_precios_tarifas')->middleware('permiso:5,2');
    Route::get('inventarios/cementerio/get_cementerio', 'CementerioController@get_cementerio');
    Route::get('inventarios/cementerio/get_vendedores', 'CementerioController@get_vendedores');
    Route::get('inventarios/cementerio/get_sat_formas_pago', 'CementerioController@get_sat_formas_pago');
    Route::post('inventarios/cementerio/guardar_venta', 'CementerioController@guardar_venta');
    Route::get('inventarios/cementerio/get_antiguedades_venta', 'CementerioController@get_antiguedades_venta');
    /**fin de rutas del cementerio */

    //Routes for LOV
    Route::get('tipos-productos', 'TiposProductoController@getAll');
    Route::get('grupos-profeco', 'GruposProfecoController@getAll');
    Route::get('almacenes', 'AlmacenesController@getAll');
    Route::get('categorias', 'CategoriasController@getAll');
    Route::get('categorias/{idCategoria}/familias', 'CategoriasController@getFamilias');
    Route::get('impuestos', 'SATImpuestosController@getImpuestos');
    Route::get('retenciones', 'SATImpuestosController@getRetenciones');
    Route::get('productos-servicios', 'SATProductosServiciosController@getAll');
    Route::get('unidades', 'UnidadesController@getAll');

    //Articulos
    Route::post('empresa/inventario/articulos', 'ArticulosController@create');
    Route::put('empresa/inventario/articulos/{id}', 'ArticulosController@save');
    Route::get('empresa/inventario/articulos/{id}', 'ArticulosController@getArticulo');
    Route::get('empresa/inventario/articulos', 'ArticulosController@getAll');
    Route::get('empresa/inventario/articulos-pdf', 'ArticulosController@getPDF');
    Route::get('empresa/inventario/articulos-pdf/{id}', 'ArticulosController@articuloPDF');

    Route::get('metodos-pago/', 'MetodosPagoController@getAll');
    Route::post('compras/', 'ComprasController@save');
});

Route::get('pdfs', 'Usuarios\UsuariosController@pdfs');

//RUTA DEL LOGUIN
Route::post('login_usuario', 'Usuarios\UsuariosController@login_usuario');
//RUTA DEL REFRESH TOKEN
Route::post('refresh_token', 'Usuarios\UsuariosController@refresh_token');

//RECUPERAR CONTRASEÑA
Route::post('/password/email', 'Auth\Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Auth\Api\ResetPasswordController@reset');






//Route::resource('usuarios', 'UsuariosController',['only'=>['index']]);


/**FIN DE RUTAS DEL SISTEMA DE LOGUEADO */