/*=========================================================================================
  File Name: router.js
  Description: Routes for vue-router. Lazy loading is enabled.
  Object Strucutre:
                    path => router path
                    name => router name
                    component(lazy loading) => component to load
                    meta : {
                      rule => which user can have access (ACL)
                      breadcrumb => Add breadcrumb to specific page
                      pageTitle => Display title besides breadcrumb
                    }
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


import Vue from 'vue'
import Router from 'vue-router'
import store from '../src/store/store'


Vue.use(Router)

const router = new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    scrollBehavior() {
        return {
            x: 0,
            y: 0
        }
    },
    routes: [

        {
            // =============================================================================
            // MAIN LAYOUT ROUTES
            // =============================================================================
            path: '',
            component: () => import('./layouts/main/Main.vue'),
            children: [
                // =============================================================================
                // Theme Routes
                // =============================================================================
                {
                    path: '/',
                    redirect: '/dashboard/analytics',
                    meta: {
                        authRequired: true
                    }
                },
                {
                    path: '/dashboard/analytics',
                    name: 'dashboard-analytics',
                    component: () => import('./views/HomeInfo.vue'),
                    meta: {
                        rule: 'editor',
                        authRequired: true
                    }
                },
                /**RUTAS PARA EL MODULO DE USUARIOS */
                {
                    path: '/configuracion/usuarios',
                    name: 'Lista de Usuarios',
                    component: () => import('@/views/pages/configuracion/usuarios/UsuariosList.vue'),
                    meta: {
                        breadcrumb: [{
                                title: 'Home',
                                url: '/'
                            },
                            {
                                title: 'Usuarios'
                            },
                            {
                                title: 'Lista',
                                active: true
                            },
                        ],
                        pageTitle: 'Usuarios',
                        rule: 'editor'
                    },
                },
                /**FIN DE RUTAS PARA EL MODULO DE USUARIOS */
                /**RUTAS PARA EL MODULO DE EMPRESA */
                {
                    path: '/configuracion/empresa',
                    name: 'Informacion de empresa',
                    component: () => import('@/views/pages/empresa/configuracion/empresa.vue'),
                    //component: () => import('@/views/pages/empresa/Empresa.vue'),
                    meta: {
                        breadcrumb: [{
                                title: 'Home',
                                url: '/'
                            },
                            {
                                title: 'Configuración',
                                active: true
                            },
                        ],
                        pageTitle: 'Empresa',
                        rule: 'editor'
                    },
                },
                /**FIN DE RUTAS PARA EL MODULO DE EMPRESAS */
                /**RUTAS PARA EL MODULO DE PROVEEDORES */
                {
                    path: '/inventarios/funeraria/proveedores',
                    name: 'inventario-proveedores',
                    component: () => import('@/views/pages/proveedores/Proveedores.vue'),
                    meta: {
                        breadcrumb: [{
                                title: 'Home',
                                url: '/'
                            },
                            {
                                title: 'Inventario'
                            },
                            {
                                title: 'Proveedores',
                                active: true
                            },
                        ],
                        pageTitle: 'Proveedores',
                        rule: 'editor'
                    },
                },
                /**FIN DE RUTAS PARA EL MODULO DE USUARIOS */
                /**RUTAS PARA EL MODULO DE inventarios */
                {
                    path: '/inventarios/cementerio/distribucion',
                    name: 'inventario_cementerio',
                    component: () => import('@/views/pages/inventarios/cementerio/CementerioList.vue'),
                    meta: {
                        breadcrumb: [{
                                title: 'Home',
                                url: '/'
                            },
                            {
                                title: 'Inventario del cementerio'
                            },
                            {
                                title: 'Distribución',
                                active: true
                            },
                        ],
                        pageTitle: 'Cementerio',
                        rule: 'editor'
                    },
                },
                {
                    path: '/inventarios/cementerio/ventas',
                    name: 'inventario_cementerio_ventas',
                    component: () => import('@/views/pages/inventarios/cementerio/ventas/UsuariosList.vue'),
                    meta: {
                        breadcrumb: [{
                                title: 'Home',
                                url: '/'
                            },
                            {
                                title: 'Inventario del cementerio'
                            },
                            {
                                title: 'Ventas',
                                active: true
                            },
                        ],
                        pageTitle: 'Cementerio',
                        rule: 'editor'
                    },
                },


                {
                    path: '/inventarios/funeraria/articulos-servicios',
                    name: 'inventarioFuneraria',
                    component: () => import('@/views/pages/inventarios/articulos/Articulos.vue'),
                    meta: {
                        breadcrumb: [{
                                title: 'Home',
                                url: '/'
                            },
                            {
                                title: 'Inventario de la funeraria'
                            },
                            {
                                title: 'Inventario',
                                active: true
                            },
                        ],
                        pageTitle: 'Inventario de la funeraria',
                        rule: 'editor'
                    },
                },
                // =============================================================================
                // Application Routes
                // =============================================================================



                // =============================================================================
                // Pages Routes
                // =============================================================================
                {
                    path: '/pages/profile',
                    name: 'page-profile',
                    component: () => import('@/views/pages/Profile.vue'),
                    meta: {
                        authRequired: true,
                        breadcrumb: [{
                                title: 'Home',
                                url: '/'
                            },
                            {
                                title: 'Usuario'
                            },
                            {
                                title: 'Cambiar mi Perfíl',
                                active: true
                            },
                        ],
                        pageTitle: 'Perfíl',
                        rule: 'editor'
                    },
                },
                {
                    path: '/pages/user-settings',
                    name: 'page-user-settings',
                    component: () => import('@/views/pages/user-settings/UserSettings.vue'),
                    meta: {
                        breadcrumb: [{
                                title: 'Home',
                                url: '/'
                            },
                            {
                                title: 'Pages'
                            },
                            {
                                title: 'User Settings',
                                active: true
                            },
                        ],
                        pageTitle: 'Settings',
                        rule: 'editor'
                    },
                },
                {
                    path: '/pages/faq',
                    name: 'page-faq',
                    component: () => import('@/views/pages/Faq.vue'),
                    meta: {
                        breadcrumb: [{
                                title: 'Home',
                                url: '/'
                            },
                            {
                                title: 'Pages'
                            },
                            {
                                title: 'FAQ',
                                active: true
                            },
                        ],
                        pageTitle: 'FAQ',
                        rule: 'editor'
                    },
                },

            ],
        },
        // =============================================================================
        // FULL PAGE LAYOUTS
        // =============================================================================
        {
            path: '',
            component: () => import('@/layouts/full-page/FullPage.vue'),
            children: [
                // =============================================================================
                // PAGES
                // =============================================================================
                {
                    path: '/pages/login',
                    name: 'page-login',
                    component: () => import('@/views/pages/login/Login.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/register',
                    name: 'page-register',
                    component: () => import('@/views/pages/register/Register.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/forgot-password',
                    name: 'page-forgot-password',
                    component: () => import('@/views/pages/ForgotPassword.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/reset-password/:token',
                    name: 'page-reset-password',
                    component: () => import('@/views/pages/ResetPassword.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/lock-screen',
                    name: 'page-lock-screen',
                    component: () => import('@/views/pages/LockScreen.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/comingsoon',
                    name: 'page-coming-soon',
                    component: () => import('@/views/pages/ComingSoon.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/error-404',
                    name: 'page-error-404',
                    component: () => import('@/views/pages/Error404.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/error-500',
                    name: 'page-error-500',
                    component: () => import('@/views/pages/Error500.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/not-authorized',
                    name: 'page-not-authorized',
                    component: () => import('@/views/pages/NotAuthorized.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/maintenance',
                    name: 'page-maintenance',
                    component: () => import('@/views/pages/Maintenance.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
            ]
        },
        // Redirect to 404 page, if no match found
        {
            path: '*',
            redirect: '/pages/error-404'
        }
    ],
})

router.afterEach(() => {
    // Remove initial loading
    const appLoading = document.getElementById('loading-bg')
    if (appLoading) {
        appLoading.style.display = "none";
    }
})

router.beforeEach((to, from, next) => {
    /**validando paginas especiales */
    if (
        (
            to.path === "/pages/login" ||
            to.path === "/pages/forgot-password" ||
            to.path === "/callback"
        )
    ) {
        if (localStorage.getItem('accessToken')) {
            return next('/')
        }
    }

    //validar si ya esta logueado y redigir al dashboard

    // If auth required, check login. If login fails redirect to login page
    //router.push({ path: '/pages/login', query: { to: to.path } }).catch(err=>{})
    /** verificar que este logueado */
    if (to.matched.some(record => record.meta.authRequired)) {
        if (localStorage.getItem('accessToken')) {
            return next()
        } else
            return next('/pages/login')
    } else {
        return next()
    }
    //
    //router.push({ path: '/pages/login' }).catch(err=>{})
    // Specify the current path as the customState parameter, meaning it
    // will be returned to the application after auth
    // auth.login({ target: to.path })

});

export default router
