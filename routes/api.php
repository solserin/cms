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

Route::get('get_puestos', 'Usuarios\UsuariosController@get_puestos');
/**ruta para obtener tokens */
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');


/**rutas de modulo en proceso */

Route::get('cementerio/lista_precios_pdf/{id_tipo?}', 'CementerioController@lista_precios_pdf');
/**fin de rutas de modulo en proceso */



/**RUTAS PARA EL SISTEMA DE LOGUEADO*/
Route::middleware(['auth:api'])->group(function () {
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

    //municipios y estados
    Route::get('estados/', 'EstadosController@getEstados');
    Route::get('municipios/{estadoId}', 'MunicipiosController@getMunicipios');
    Route::get('localidades/{municipioId}', 'LocalidadesController@getLocalidades');

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
    //Proveedores

    Route::post('empresa/inventario/proveedores', 'ProveedoresController@create');
    Route::put('empresa/inventario/proveedores/{id}', 'ProveedoresController@save');
    Route::get('empresa/inventario/proveedores', 'ProveedoresController@getAll');
    Route::get('empresa/inventario/proveedores-pdf', 'ProveedoresController@getPDF');
    Route::get('empresa/inventario/proveedores-active', 'ProveedoresController@getActive');
    Route::get('empresa/inventario/proveedor-pdf/{id}', 'ProveedoresController@proveedorPDF');

    /**rutas del cementerio */
    Route::post('cementerio/registrar_precio_propiedad', 'CementerioController@registrar_precio_propiedad');
    Route::post('cementerio/update_precio_propiedad', 'CementerioController@update_precio_propiedad');
    Route::post('cementerio/enable_disable', 'CementerioController@enable_disable');
    Route::get('cementerio/get_financiamientos', 'CementerioController@get_financiamientos');
    Route::get('cementerio/get_tipo_propiedades', 'CementerioController@get_tipo_propiedades');
    Route::get('cementerio/get_precio_by_id', 'CementerioController@get_precio_by_id');


    Route::get('generarNumeroTitulo', 'CementerioController@generarNumeroTitulo');
    Route::get('inventarios/cementerio/get_cementerio', 'CementerioController@get_cementerio');
    Route::get('inventarios/cementerio/propiedadesById', 'CementerioController@propiedadesById');
    Route::get('inventarios/cementerio/get_propiedades_by_tipo', 'CementerioController@get_propiedades_by_tipo');
    Route::get('inventarios/cementerio/get_usuarios_para_vendedores', 'CementerioController@get_usuarios_para_vendedores');
    Route::get('inventarios/cementerio/tipoPropiedades', 'CementerioController@tipoPropiedades');
    Route::get('inventarios/cementerio/get_ventas_referencias_propiedades', 'CementerioController@get_ventas_referencias_propiedades');
    Route::get('inventarios/cementerio/get_columna_fila_terraza', 'CementerioController@get_columna_fila_terraza');
    Route::get('inventarios/cementerio/precios_tarifas', 'CementerioController@precios_tarifas');
    Route::post('inventarios/cementerio/actualizar_precios_tarifas', 'CementerioController@actualizar_precios_tarifas');
    Route::get('inventarios/cementerio/get_cementerio', 'CementerioController@get_cementerio');
    Route::get('inventarios/cementerio/get_vendedores', 'CementerioController@get_vendedores');
    Route::get('inventarios/cementerio/get_sat_formas_pago', 'CementerioController@get_sat_formas_pago');
    Route::post('inventarios/cementerio/guardar_venta', 'CementerioController@guardar_venta');
    Route::post('inventarios/cementerio/modificar_venta', 'CementerioController@modificar_venta');
    Route::post('inventarios/cementerio/cancelar_venta', 'CementerioController@cancelar_venta');
    Route::get('inventarios/cementerio/get_antiguedades_venta', 'CementerioController@get_antiguedades_venta');
    Route::get('inventarios/cementerio/get_ventas', 'CementerioController@get_ventas');




    Route::get('inventarios/cementerio/documento_estado_de_cuenta_cementerio', 'CementerioController@documento_estado_de_cuenta_cementerio');
    Route::get('inventarios/cementerio/get_venta_id/{id_venta?}', 'CementerioController@get_venta_id');
    Route::get('inventarios/cementerio/referencias_de_pago/{id_pago?}', 'CementerioController@referencias_de_pago');
    Route::get('inventarios/cementerio/documento_convenio', 'CementerioController@documento_convenio');
    Route::get('inventarios/cementerio/acuse_cancelacion', 'CementerioController@acuse_cancelacion');
    Route::get('inventarios/cementerio/documento_solicitud', 'CementerioController@documento_solicitud');
    Route::get('inventarios/cementerio/documento_titulo', 'CementerioController@documento_titulo');

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