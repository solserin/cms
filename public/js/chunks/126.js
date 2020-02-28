(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[126],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _store_modulos_usuarios_storeUsuarios_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/store/modulos/usuarios/storeUsuarios.js */ "./resources/js/src/store/modulos/usuarios/storeUsuarios.js");
/* harmony import */ var _VariablesGlobales__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../VariablesGlobales */ "./resources/js/src/VariablesGlobales.js");
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vue-select */ "./node_modules/vue-select/dist/vue-select.js");
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(vue_select__WEBPACK_IMPORTED_MODULE_2__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
// Store usuarios

/**VARIABLES GLOBALES */



/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    "v-select": vue_select__WEBPACK_IMPORTED_MODULE_2___default.a
  },
  data: function data() {
    return {
      maximo: 15,
      mostrarOptions: _VariablesGlobales__WEBPACK_IMPORTED_MODULE_1__["mostrarOptions"],
      mostrar: 15,
      selected: [],
      users: []
    };
  },
  methods: {
    handleSearch: function handleSearch(searching) {
      var _print = "The user searched for: ".concat(searching, "\n");

      this.$refs.pre.appendChild(document.createTextNode(_print));
    },
    handleChangePage: function handleChangePage(page) {
      var _print = "The user changed the page to: ".concat(page, "\n");

      this.$refs.pre.appendChild(document.createTextNode(_print));
    },
    handleSort: function handleSort(key, active) {
      var _print = "the user ordered: ".concat(key, " ").concat(active, "\n");

      this.$refs.pre.appendChild(document.createTextNode(_print));
    }
  },
  created: function created() {
    var _this = this;

    if (!_store_modulos_usuarios_storeUsuarios_js__WEBPACK_IMPORTED_MODULE_0__["default"].isRegistered) {
      this.$store.registerModule("userStore", _store_modulos_usuarios_storeUsuarios_js__WEBPACK_IMPORTED_MODULE_0__["default"]);
      _store_modulos_usuarios_storeUsuarios_js__WEBPACK_IMPORTED_MODULE_0__["default"].isRegistered = true;
    }

    this.$store.dispatch("userStore/getUsuarios", this.maximo).then(function (res) {
      _this.users = res.data.data;
    }).catch(function (err) {
      console.error(err);
    });
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=template&id=6799a452&":
/*!****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=template&id=6799a452& ***!
  \****************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c(
        "vs-tabs",
        { staticClass: "pt-5", attrs: { alignment: "left", position: "top" } },
        [
          _c(
            "vs-tab",
            {
              staticClass: "pb-5",
              attrs: {
                label: "CONTROL DE USUARIOS",
                icon: "supervisor_account"
              }
            },
            [
              _c(
                "vx-card",
                {
                  ref: "filterCard",
                  staticClass: "user-list-filters mb-5 mt-5",
                  attrs: { title: "Filtros de selección" }
                },
                [
                  _c("div", { staticClass: "vx-row" }, [
                    _c(
                      "div",
                      { staticClass: "vx-col md:w-1/6 sm:w-1/2 w-full" },
                      [
                        _c("label", { staticClass: "text-sm opacity-75" }, [
                          _vm._v("Mostrar")
                        ]),
                        _vm._v(" "),
                        _c("v-select", {
                          staticClass: "mb-4 sm:mb-0",
                          attrs: {
                            options: _vm.mostrarOptions,
                            clearable: false,
                            dir: _vm.$vs.rtl ? "rtl" : "ltr"
                          },
                          model: {
                            value: _vm.mostrar,
                            callback: function($$v) {
                              _vm.mostrar = $$v
                            },
                            expression: "mostrar"
                          }
                        })
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "vx-col md:w-1/6 sm:w-1/2 w-full" },
                      [
                        _c("label", { staticClass: "text-sm opacity-75" }, [
                          _vm._v("Role")
                        ]),
                        _vm._v(" "),
                        _c("v-select", {
                          staticClass: "mb-4 md:mb-0",
                          attrs: {
                            options: _vm.mostrarOptions,
                            clearable: false,
                            dir: _vm.$vs.rtl ? "rtl" : "ltr"
                          },
                          model: {
                            value: _vm.mostrar,
                            callback: function($$v) {
                              _vm.mostrar = $$v
                            },
                            expression: "mostrar"
                          }
                        })
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "vx-col md:w-1/6 sm:w-1/2 w-full" },
                      [
                        _c("label", { staticClass: "text-sm opacity-75" }, [
                          _vm._v("Status")
                        ]),
                        _vm._v(" "),
                        _c("v-select", {
                          staticClass: "mb-4 md:mb-0",
                          attrs: {
                            options: _vm.mostrarOptions,
                            clearable: false,
                            dir: _vm.$vs.rtl ? "rtl" : "ltr"
                          },
                          model: {
                            value: _vm.mostrar,
                            callback: function($$v) {
                              _vm.mostrar = $$v
                            },
                            expression: "mostrar"
                          }
                        })
                      ],
                      1
                    )
                  ])
                ]
              ),
              _vm._v(" "),
              _c("br"),
              _vm._v(" "),
              _c(
                "vs-table",
                {
                  attrs: {
                    sst: true,
                    pagination: "",
                    "max-items": _vm.maximo,
                    data: _vm.users,
                    stripe: "",
                    noDataText: "0 Resultados"
                  },
                  on: {
                    search: _vm.handleSearch,
                    "change-page": _vm.handleChangePage,
                    sort: _vm.handleSort
                  },
                  scopedSlots: _vm._u([
                    {
                      key: "default",
                      fn: function(ref) {
                        var data = ref.data
                        return _vm._l(data, function(tr, indextr) {
                          return _c(
                            "vs-tr",
                            { key: indextr, attrs: { data: tr } },
                            [
                              _c(
                                "vs-td",
                                { attrs: { data: data[indextr].id } },
                                [_vm._v(_vm._s(data[indextr].id))]
                              ),
                              _vm._v(" "),
                              _c(
                                "vs-td",
                                { attrs: { data: data[indextr].nombre } },
                                [_vm._v(_vm._s(data[indextr].nombre))]
                              ),
                              _vm._v(" "),
                              _c(
                                "vs-td",
                                { attrs: { data: data[indextr].email } },
                                [_vm._v(_vm._s(data[indextr].email))]
                              ),
                              _vm._v(" "),
                              _c(
                                "vs-td",
                                { attrs: { data: data[indextr].genero } },
                                [_vm._v(_vm._s(data[indextr].genero))]
                              ),
                              _vm._v(" "),
                              _c(
                                "vs-td",
                                { attrs: { data: data[indextr].status } },
                                [_vm._v(_vm._s(data[indextr].status))]
                              ),
                              _vm._v(" "),
                              _c(
                                "vs-td",
                                { attrs: { data: data[indextr].rol } },
                                [_vm._v(_vm._s(data[indextr].rol))]
                              ),
                              _vm._v(" "),
                              _c(
                                "vs-td",
                                { attrs: { data: data[indextr].id } },
                                [
                                  _c(
                                    "svg",
                                    {
                                      staticClass:
                                        "feather feather-edit-3 h-5 w-5 mr-4 hover:text-primary cursor-pointer",
                                      attrs: {
                                        xmlns: "http://www.w3.org/2000/svg",
                                        width: "24px",
                                        height: "24px",
                                        viewBox: "0 0 24 24",
                                        fill: "none",
                                        stroke: "currentColor",
                                        "stroke-width": "2",
                                        "stroke-linecap": "round",
                                        "stroke-linejoin": "round"
                                      }
                                    },
                                    [
                                      _c("path", { attrs: { d: "M12 20h9" } }),
                                      _vm._v(" "),
                                      _c("path", {
                                        attrs: {
                                          d:
                                            "M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"
                                        }
                                      })
                                    ]
                                  ),
                                  _vm._v(" "),
                                  _c(
                                    "svg",
                                    {
                                      staticClass:
                                        "feather feather-trash-2 h-5 w-5 hover:text-danger cursor-pointer",
                                      attrs: {
                                        xmlns: "http://www.w3.org/2000/svg",
                                        width: "24px",
                                        height: "24px",
                                        viewBox: "0 0 24 24",
                                        fill: "none",
                                        stroke: "currentColor",
                                        "stroke-width": "2",
                                        "stroke-linecap": "round",
                                        "stroke-linejoin": "round"
                                      }
                                    },
                                    [
                                      _c("polyline", {
                                        attrs: { points: "3 6 5 6 21 6" }
                                      }),
                                      _vm._v(" "),
                                      _c("path", {
                                        attrs: {
                                          d:
                                            "M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("line", {
                                        attrs: {
                                          x1: "10",
                                          y1: "11",
                                          x2: "10",
                                          y2: "17"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("line", {
                                        attrs: {
                                          x1: "14",
                                          y1: "11",
                                          x2: "14",
                                          y2: "17"
                                        }
                                      })
                                    ]
                                  )
                                ]
                              )
                            ],
                            1
                          )
                        })
                      }
                    }
                  ]),
                  model: {
                    value: _vm.selected,
                    callback: function($$v) {
                      _vm.selected = $$v
                    },
                    expression: "selected"
                  }
                },
                [
                  _c("template", { slot: "header" }, [
                    _c("h3", { staticClass: "pb-5 text-primary" }, [
                      _vm._v("Listado de Usuarios")
                    ])
                  ]),
                  _vm._v(" "),
                  _c(
                    "template",
                    { slot: "thead" },
                    [
                      _c("vs-th", { attrs: { "sort-key": "id" } }, [
                        _vm._v("Clave")
                      ]),
                      _vm._v(" "),
                      _c("vs-th", { attrs: { "sort-key": "nombre" } }, [
                        _vm._v("Nombre")
                      ]),
                      _vm._v(" "),
                      _c("vs-th", { attrs: { "sort-key": "email" } }, [
                        _vm._v("Usuario")
                      ]),
                      _vm._v(" "),
                      _c("vs-th", { attrs: { "sort-key": "genero" } }, [
                        _vm._v("Género")
                      ]),
                      _vm._v(" "),
                      _c("vs-th", { attrs: { "sort-key": "status" } }, [
                        _vm._v("Estado")
                      ]),
                      _vm._v(" "),
                      _c("vs-th", { attrs: { "sort-key": "rol" } }, [
                        _vm._v("Rol")
                      ]),
                      _vm._v(" "),
                      _c("vs-th", [_vm._v("Acciones")])
                    ],
                    1
                  )
                ],
                2
              ),
              _vm._v("\n      " + _vm._s(_vm.selected) + "\n      "),
              _c("pre", { ref: "pre" })
            ],
            1
          ),
          _vm._v(" "),
          _c("vs-tab", {
            attrs: { label: "ROLES DEL SISTEMA", icon: "fingerprint" }
          })
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/store/modulos/usuarios/storeUsuarios.js":
/*!******************************************************************!*\
  !*** ./resources/js/src/store/modulos/usuarios/storeUsuarios.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _storeUsuariosState__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./storeUsuariosState */ "./resources/js/src/store/modulos/usuarios/storeUsuariosState.js");
/* harmony import */ var _storeUsuariosMutations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./storeUsuariosMutations */ "./resources/js/src/store/modulos/usuarios/storeUsuariosMutations.js");
/* harmony import */ var _storeUsuariosActions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./storeUsuariosActions */ "./resources/js/src/store/modulos/usuarios/storeUsuariosActions.js");
/* harmony import */ var _storeUsuariosGetters__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./storeUsuariosGetters */ "./resources/js/src/store/modulos/usuarios/storeUsuariosGetters.js");




/* harmony default export */ __webpack_exports__["default"] = ({
  isRegistered: false,
  namespaced: true,
  state: _storeUsuariosState__WEBPACK_IMPORTED_MODULE_0__["default"],
  mutations: _storeUsuariosMutations__WEBPACK_IMPORTED_MODULE_1__["default"],
  actions: _storeUsuariosActions__WEBPACK_IMPORTED_MODULE_2__["default"],
  getters: _storeUsuariosGetters__WEBPACK_IMPORTED_MODULE_3__["default"]
});

/***/ }),

/***/ "./resources/js/src/store/modulos/usuarios/storeUsuariosActions.js":
/*!*************************************************************************!*\
  !*** ./resources/js/src/store/modulos/usuarios/storeUsuariosActions.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _axios_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/axios.js */ "./resources/js/src/axios.js");
/* harmony import */ var _VariablesGlobales__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../VariablesGlobales */ "./resources/js/src/VariablesGlobales.js");
function _objectDestructuringEmpty(obj) { if (obj == null) throw new TypeError("Cannot destructure undefined"); }


/**VARIABLES GLOBALES */


/* harmony default export */ __webpack_exports__["default"] = ({
  getUsuarios: function getUsuarios(_ref, paginado) {
    var commit = _ref.commit;
    return new Promise(function (resolve, reject) {
      _axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].get(_VariablesGlobales__WEBPACK_IMPORTED_MODULE_1__["api_url"] + "/get_usuarios").then(function (response) {
        commit('SET_USERS', response.data);
        resolve(response);
      }).catch(function (error) {
        reject(error);
      });
    });
  },
  fetchUser: function fetchUser(_ref2, userId) {
    _objectDestructuringEmpty(_ref2);

    return new Promise(function (resolve, reject) {
      _axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].get("/api/user-management/users/".concat(userId)).then(function (response) {
        resolve(response);
      }).catch(function (error) {
        reject(error);
      });
    });
  },
  removeRecord: function removeRecord(_ref3, userId) {
    var commit = _ref3.commit;
    return new Promise(function (resolve, reject) {
      _axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].delete("/api/user-management/users/".concat(userId)).then(function (response) {
        commit('REMOVE_RECORD', userId);
        resolve(response);
      }).catch(function (error) {
        reject(error);
      });
    });
  }
});

/***/ }),

/***/ "./resources/js/src/store/modulos/usuarios/storeUsuariosGetters.js":
/*!*************************************************************************!*\
  !*** ./resources/js/src/store/modulos/usuarios/storeUsuariosGetters.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/*=========================================================================================
  File Name: moduleCalendarGetters.js
  Description: Calendar Module Getters
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
/* harmony default export */ __webpack_exports__["default"] = ({});

/***/ }),

/***/ "./resources/js/src/store/modulos/usuarios/storeUsuariosMutations.js":
/*!***************************************************************************!*\
  !*** ./resources/js/src/store/modulos/usuarios/storeUsuariosMutations.js ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/*=========================================================================================
  File Name: moduleCalendarMutations.js
  Description: Calendar Module Mutations
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
/* harmony default export */ __webpack_exports__["default"] = ({
  SET_USERS: function SET_USERS(state, users) {
    state.users = users;
  },
  REMOVE_RECORD: function REMOVE_RECORD(state, itemId) {
    var userIndex = state.users.findIndex(function (u) {
      return u.id == itemId;
    });
    state.users.splice(userIndex, 1);
  }
});

/***/ }),

/***/ "./resources/js/src/store/modulos/usuarios/storeUsuariosState.js":
/*!***********************************************************************!*\
  !*** ./resources/js/src/store/modulos/usuarios/storeUsuariosState.js ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/*=========================================================================================
  File Name: moduleCalendarState.js
  Description: Calendar Module State
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
/* harmony default export */ __webpack_exports__["default"] = ({
  users: []
});

/***/ }),

/***/ "./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue":
/*!***************************************************************************!*\
  !*** ./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _UsuariosList_vue_vue_type_template_id_6799a452___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./UsuariosList.vue?vue&type=template&id=6799a452& */ "./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=template&id=6799a452&");
/* harmony import */ var _UsuariosList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./UsuariosList.vue?vue&type=script&lang=js& */ "./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _UsuariosList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _UsuariosList_vue_vue_type_template_id_6799a452___WEBPACK_IMPORTED_MODULE_0__["render"],
  _UsuariosList_vue_vue_type_template_id_6799a452___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/pages/configuracion/roles/UsuariosList.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************!*\
  !*** ./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UsuariosList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./UsuariosList.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UsuariosList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=template&id=6799a452&":
/*!**********************************************************************************************************!*\
  !*** ./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=template&id=6799a452& ***!
  \**********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_UsuariosList_vue_vue_type_template_id_6799a452___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./UsuariosList.vue?vue&type=template&id=6799a452& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/pages/configuracion/roles/UsuariosList.vue?vue&type=template&id=6799a452&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_UsuariosList_vue_vue_type_template_id_6799a452___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_UsuariosList_vue_vue_type_template_id_6799a452___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);