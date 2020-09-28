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
/**rutas de modulo en proceso */


/**rutas publicas_ entran sin token */
Route::get('funeraria/get_planes/{solo_a_futuro?}/{id_plan?}', 'FunerariaController@get_planes');
/**fin de rutas de modulo en proceso */
Route::get('funeraria/documento_estado_de_cuenta_planes', 'FunerariaController@documento_estado_de_cuenta_planes');

Route::get('proveedores/get_proveedores/{id_provedor?}/{paginated?}', 'ProveedoresController@get_proveedores');

/**inventario */
Route::get('inventario/get_tipo_articulos', 'InventarioController@get_tipo_articulos');
Route::get('inventario/get_categorias', 'InventarioController@get_categorias');
Route::get('inventario/get_unidades', 'InventarioController@get_unidades');
Route::get('inventario/get_sat_unidades', 'InventarioController@get_sat_unidades');
Route::get('inventario/get_inventario/{id_articulo?}/{paginated?}/{id_departamento?}/{id_categoria?}/{tipo_articulo?}/{solo_inventariable?}', 'InventarioController@get_articulos');



/**rutas de servicios funerarios */
Route::get('funeraria/get_personal_recoger', 'FunerariaController@get_personal_recoger');
Route::get('funeraria/get_solicitudes_servicios/{id_servicio?}/{paginated?}', 'FunerariaController@get_solicitudes_servicios');
Route::get('funeraria/get_hoja_solicitud', 'FunerariaController@get_hoja_solicitud');
Route::get('funeraria/hoja_preautorizacion', 'FunerariaController@hoja_preautorizacion');
Route::get('funeraria/certificado_defuncion', 'FunerariaController@certificado_defuncion');
Route::get('funeraria/instrucciones_servicio_funerario', 'FunerariaController@instrucciones_servicio_funerario');
Route::get('funeraria/contancia_de_embalsamiento', 'FunerariaController@contancia_de_embalsamiento');
Route::get('funeraria/material_velacion_rentado', 'FunerariaController@material_velacion_rentado');
Route::get('funeraria/entrega_acta_defuncion', 'FunerariaController@entrega_acta_defuncion');
Route::get('funeraria/entrega_cenizas', 'FunerariaController@entrega_cenizas');
Route::get('funeraria/orden_servicio', 'FunerariaController@orden_servicio');
Route::get('funeraria/get_estados_civiles', 'FunerariaController@get_estados_civiles');
Route::get('funeraria/get_escolaridades', 'FunerariaController@get_escolaridades');
Route::get('funeraria/get_afiliaciones', 'FunerariaController@get_afiliaciones');
Route::get('funeraria/get_sitios_muerte', 'FunerariaController@get_sitios_muerte');
Route::get('funeraria/get_titulos', 'FunerariaController@get_titulos');
Route::get('funeraria/get_estados_afectado', 'FunerariaController@get_estados_afectado');
Route::get('funeraria/get_lugares_velacion', 'FunerariaController@get_lugares_velacion');
Route::get('funeraria/get_lugares_inhumacion', 'FunerariaController@get_lugares_inhumacion');
Route::get('funeraria/get_material_velacion/{id_articulo?}/{paginated?}/{id_departamento?}/{id_categoria?}/{tipo_articulo?}/{solo_inventariable?}', 'InventarioController@get_articulos');
Route::get('funeraria/get_tipos_contratante', 'FunerariaController@get_tipos_contratante');
Route::get('cementerio/get_ventas/{id_venta?}/{paginated?}/', 'CementerioController@get_ventas');
Route::get('funeraria/get_ventas/{id_venta?}/{paginated?}/', 'FunerariaController@get_ventas');
Route::get('funeraria/get_inventario/{id_articulo?}/{paginated?}/{solo_existencias?}/{con_material_velacion?}', 'FunerariaController@get_inventario');
Route::get('funeraria/get_categorias_servicio', 'FunerariaController@get_categorias_servicio');


Route::get('inventario/get_ajuste_pdf', 'InventarioController@get_ajuste_pdf');

Route::get('inventario/get_ajustes/{id_ajuste?}/{paginated?}', 'InventarioController@get_ajustes');
/**servicios accedidos desde el backend */
Route::middleware(['client'])->group(function () {
    Route::get('pagos/get_pagos_backend/{id_pago?}/{paginated?}/{ver_subpagos?}', 'PagosController@get_pagos');
});
/**fin de servicios accedidos desde el backend */
/**RUTAS PARA EL SISTEMA DE LOGUEADO*/
Route::middleware(['auth:api'])->group(function () {
    Route::get('pagos/get_pagos/{id_pago?}/{paginated?}/{ver_subpagos?}', 'PagosController@get_pagos');


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
    Route::post('cementerio/control_ventas/{tipo_servicio}', 'CementerioController@control_ventas'); //agregar,modificar
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
    Route::get('cementerio/documento_titulo', 'CementerioController@documento_titulo');
    Route::get('cementerio/referencias_de_pago/{id_pago?}', 'CementerioController@referencias_de_pago');
    Route::get('cementerio/reglamento_pago', 'CementerioController@reglamento_pago');

    /**rutas de funeraria ventas planes */
    Route::post('funeraria/control_ventas/{tipo_servicio}', 'FunerariaController@control_ventas'); //agregar,modificar
    Route::post('funeraria/control_planes/{tipo_servicio?}', 'FunerariaController@control_planes');
    Route::post('funeraria/enable_disable_planes', 'FunerariaController@enable_disable_planes');
    Route::post('funeraria/registrar_precio', 'FunerariaController@registrar_precio');
    Route::get('funeraria/get_precio_by_id', 'FunerariaController@get_precio_by_id');
    Route::post('funeraria/update_precio', 'FunerariaController@update_precio');
    Route::post('funeraria/enable_disable', 'FunerariaController@enable_disable');
    Route::get('funeraria/pdf_plan_funerario/{idioma?}', 'FunerariaController@pdf_plan_funerario');
    Route::get('funeraria/planes_funerarios/{idioma?}', 'FunerariaController@planes_funerarios');
    Route::post('funeraria/cancelar_venta', 'FunerariaController@cancelar_venta');
    Route::post('funeraria/cancelar_solicitud', 'FunerariaController@cancelar_solicitud');
    Route::get('funeraria/documento_solicitud', 'FunerariaController@documento_solicitud');
    Route::get('funeraria/documento_convenio', 'FunerariaController@documento_convenio');
    Route::get('funeraria/documento_finiquitado', 'FunerariaController@documento_finiquitado');
    Route::get('funeraria/servicio_funerario/acuse_cancelacion', 'FunerariaController@servicio_acuse_cancelacion');
    Route::get('funeraria/referencias_de_pago', 'FunerariaController@referencias_de_pago');
    Route::get('funeraria/reglamento_pago', 'FunerariaController@reglamento_pago');
    Route::get('funeraria/acuse_cancelacion', 'FunerariaController@acuse_cancelacion');

    /**rutas de pagos */
    Route::get('pagos/calcular_adeudo/{referencia}/{fecha_pago}/{multipago?}', 'PagosController@calcular_adeudo');
    Route::post('pagos/guardar_pago', 'PagosController@guardar_pago');
    Route::get('pagos/get_formas_pago_sat', 'PagosController@get_formas_pago_sat');
    Route::get('pagos/get_monedas_sat', 'PagosController@get_monedas_sat');
    Route::get('pagos/get_cobradores', 'PagosController@get_cobradores');
    Route::get('pagos/recibo_de_pago/{id_pago?}', 'PagosController@recibo_de_pago');
    Route::post('pagos/cancelar_pago', 'PagosController@cancelar_pago');
    /**fin de rutas de pagos */

    /**proveedores */
    Route::post('/proveedores/guardar_proveedor', 'ProveedoresController@guardar_proveedor');
    Route::post('/proveedores/modificar_proveedor', 'ProveedoresController@modificar_proveedor')->middleware('permiso:10,28');
    Route::post('/proveedores/delete_proveedor', 'ProveedoresController@delete_proveedor');
    Route::post('/proveedores/alta_proveedor', 'ProveedoresController@alta_proveedor');


    /**invnetario */
    Route::post('inventario/control_articulos/{tipo_servicio?}', 'InventarioController@control_articulos');
    Route::post('inventario/enable_disable/{tipo}', 'InventarioController@enable_disable');
    Route::post('inventario/ajustar_inventario', 'InventarioController@ajustar_inventario');

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
    Route::get('inventario/get_inventario_pdf', 'InventarioController@get_inventario_pdf');

    Route::get('inventario/get_inventario_conteo_pdf', 'InventarioController@get_inventario_conteo_pdf');

    /**rutas de servicios funerarios */
    Route::post('funeraria/control_solicitud/{tipo_servicio}', 'FunerariaController@control_solicitud');

    Route::post('funeraria/control_contratos/{tipo_servicio}', 'FunerariaController@control_contratos');






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