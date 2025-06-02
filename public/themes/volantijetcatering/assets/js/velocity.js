(self["webpackChunk"] = self["webpackChunk"] || []).push([["/js/velocity"],{

/***/ "./src/Resources/assets/js/app.js":
/*!****************************************!*\
  !*** ./src/Resources/assets/js/app.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var accounting__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! accounting */ "./node_modules/accounting/accounting.js");
/* harmony import */ var accounting__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(accounting__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");
/* harmony import */ var vue_carousel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vue-carousel */ "./node_modules/vue-carousel/dist/vue-carousel.min.js");
/* harmony import */ var vue_carousel__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(vue_carousel__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_trans__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @components/trans */ "./src/Resources/assets/js/UI/components/trans.js");
/* harmony import */ var _components_trans__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_components_trans__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _bootstrap__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./bootstrap */ "./src/Resources/assets/js/bootstrap.js");
/* harmony import */ var _bootstrap__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_bootstrap__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var vee_validate_dist_locale_ar__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! vee-validate/dist/locale/ar */ "./node_modules/vee-validate/dist/locale/ar.js");
/* harmony import */ var vee_validate_dist_locale_ar__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(vee_validate_dist_locale_ar__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var vee_validate_dist_locale_de__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! vee-validate/dist/locale/de */ "./node_modules/vee-validate/dist/locale/de.js");
/* harmony import */ var vee_validate_dist_locale_de__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(vee_validate_dist_locale_de__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var vee_validate_dist_locale_fa__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! vee-validate/dist/locale/fa */ "./node_modules/vee-validate/dist/locale/fa.js");
/* harmony import */ var vee_validate_dist_locale_fa__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(vee_validate_dist_locale_fa__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var vee_validate_dist_locale_fr__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! vee-validate/dist/locale/fr */ "./node_modules/vee-validate/dist/locale/fr.js");
/* harmony import */ var vee_validate_dist_locale_fr__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(vee_validate_dist_locale_fr__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var vee_validate_dist_locale_nl__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! vee-validate/dist/locale/nl */ "./node_modules/vee-validate/dist/locale/nl.js");
/* harmony import */ var vee_validate_dist_locale_nl__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(vee_validate_dist_locale_nl__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var vee_validate_dist_locale_tr__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! vee-validate/dist/locale/tr */ "./node_modules/vee-validate/dist/locale/tr.js");
/* harmony import */ var vee_validate_dist_locale_tr__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(vee_validate_dist_locale_tr__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var vee_validate_dist_locale_hi__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! vee-validate/dist/locale/hi */ "./node_modules/vee-validate/dist/locale/hi.js");
/* harmony import */ var vee_validate_dist_locale_hi__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(vee_validate_dist_locale_hi__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var vee_validate_dist_locale_zh_CN__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! vee-validate/dist/locale/zh_CN */ "./node_modules/vee-validate/dist/locale/zh_CN.js");
/* harmony import */ var vee_validate_dist_locale_zh_CN__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(vee_validate_dist_locale_zh_CN__WEBPACK_IMPORTED_MODULE_12__);
/**
 * Main imports.
 */







/**
 * Lang imports.
 */









/**
 * Vue plugins.
 */
vue__WEBPACK_IMPORTED_MODULE_13__["default"].use((vue_carousel__WEBPACK_IMPORTED_MODULE_2___default()));
vue__WEBPACK_IMPORTED_MODULE_13__["default"].use(BootstrapSass);
vue__WEBPACK_IMPORTED_MODULE_13__["default"].use(vee_validate__WEBPACK_IMPORTED_MODULE_1__["default"], {
  dictionary: {
    ar: (vee_validate_dist_locale_ar__WEBPACK_IMPORTED_MODULE_5___default()),
    de: (vee_validate_dist_locale_de__WEBPACK_IMPORTED_MODULE_6___default()),
    fa: (vee_validate_dist_locale_fa__WEBPACK_IMPORTED_MODULE_7___default()),
    fr: (vee_validate_dist_locale_fr__WEBPACK_IMPORTED_MODULE_8___default()),
    nl: (vee_validate_dist_locale_nl__WEBPACK_IMPORTED_MODULE_9___default()),
    tr: (vee_validate_dist_locale_tr__WEBPACK_IMPORTED_MODULE_10___default()),
    hi_IN: (vee_validate_dist_locale_hi__WEBPACK_IMPORTED_MODULE_11___default()),
    zh_CN: (vee_validate_dist_locale_zh_CN__WEBPACK_IMPORTED_MODULE_12___default())
  },
  events: 'input|change|blur'
});

/**
 * Filters.
 */
vue__WEBPACK_IMPORTED_MODULE_13__["default"].filter('currency', function (value, argument) {
  return accounting__WEBPACK_IMPORTED_MODULE_0___default().formatMoney(value, argument);
});

/**
 * Global components.
 */
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('vue-slider', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.t.bind(__webpack_require__, /*! vue-slider-component */ "./node_modules/vue-slider-component/dist/vue-slider-component.umd.min.js", 23));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('mini-cart-button', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/mini-cart-button */ "./src/Resources/assets/js/UI/components/mini-cart-button.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('mini-cart', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/mini-cart */ "./src/Resources/assets/js/UI/components/mini-cart.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('modal-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/modal */ "./src/Resources/assets/js/UI/components/modal.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('add-to-cart', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/add-to-cart */ "./src/Resources/assets/js/UI/components/add-to-cart.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('star-ratings', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/star-rating */ "./src/Resources/assets/js/UI/components/star-rating.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('quantity-btn', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/quantity-btn */ "./src/Resources/assets/js/UI/components/quantity-btn.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('quantity-changer', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/quantity-changer */ "./src/Resources/assets/js/UI/components/quantity-changer.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('proceed-to-checkout', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/proceed-to-checkout */ "./src/Resources/assets/js/UI/components/proceed-to-checkout.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('compare-component-with-badge', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/header-compare-with-badge */ "./src/Resources/assets/js/UI/components/header-compare-with-badge.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('searchbar-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/header-searchbar */ "./src/Resources/assets/js/UI/components/header-searchbar.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('wishlist-component-with-badge', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/header-wishlist-with-badge */ "./src/Resources/assets/js/UI/components/header-wishlist-with-badge.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('mobile-header', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/header-mobile */ "./src/Resources/assets/js/UI/components/header-mobile.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('sidebar-header', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/header-sidebar */ "./src/Resources/assets/js/UI/components/header-sidebar.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('right-side-header', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/header-right-side */ "./src/Resources/assets/js/UI/components/header-right-side.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('sidebar-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/sidebar */ "./src/Resources/assets/js/UI/components/sidebar.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('product-card', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/product-card */ "./src/Resources/assets/js/UI/components/product-card.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('wishlist-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/wishlist */ "./src/Resources/assets/js/UI/components/wishlist.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('carousel-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/carousel */ "./src/Resources/assets/js/UI/components/carousel.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('slider-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/banners */ "./src/Resources/assets/js/UI/components/banners.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('child-sidebar', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/child-sidebar */ "./src/Resources/assets/js/UI/components/child-sidebar.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('card-list-header', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/card-header */ "./src/Resources/assets/js/UI/components/card-header.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('logo-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/image-logo */ "./src/Resources/assets/js/UI/components/image-logo.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('magnify-image', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/image-magnifier */ "./src/Resources/assets/js/UI/components/image-magnifier.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('image-search-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/image-search */ "./src/Resources/assets/js/UI/components/image-search.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('compare-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/product-compare */ "./src/Resources/assets/js/UI/components/product-compare.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('shimmer-component', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/shimmer-component */ "./src/Resources/assets/js/UI/components/shimmer-component.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('responsive-sidebar', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/responsive-sidebar */ "./src/Resources/assets/js/UI/components/responsive-sidebar.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('product-quick-view', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/product-quick-view */ "./src/Resources/assets/js/UI/components/product-quick-view.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('product-quick-view-btn', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/product-quick-view-btn */ "./src/Resources/assets/js/UI/components/product-quick-view-btn.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('recently-viewed', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/recently-viewed */ "./src/Resources/assets/js/UI/components/recently-viewed.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('product-collections', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/product-collections */ "./src/Resources/assets/js/UI/components/product-collections.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('hot-category', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/hot-category */ "./src/Resources/assets/js/UI/components/hot-category.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('hot-categories', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/hot-categories */ "./src/Resources/assets/js/UI/components/hot-categories.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('popular-category', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/popular-category */ "./src/Resources/assets/js/UI/components/popular-category.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('popular-categories', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/popular-categories */ "./src/Resources/assets/js/UI/components/popular-categories.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('velocity-overlay-loader', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/overlay-loader */ "./src/Resources/assets/js/UI/components/overlay-loader.vue"));
});
//
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('custom-product-card', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/custom-product-card */ "./src/Resources/assets/js/UI/components/custom-product-card.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('custom-order-card', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.bind(__webpack_require__, /*! @components/custom-product-card */ "./src/Resources/assets/js/UI/components/custom-product-card.vue"));
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('vnode-injector', {
  functional: true,
  props: ['nodes'],
  render: function render(h, _ref) {
    var props = _ref.props;
    return props.nodes;
  }
});
vue__WEBPACK_IMPORTED_MODULE_13__["default"].component('go-top', function () {
  return __webpack_require__.e(/*! import() */ "js/components").then(__webpack_require__.t.bind(__webpack_require__, /*! @inotom/vue-go-top */ "./node_modules/@inotom/vue-go-top/dist/vue-go-top.umd.js", 23));
});

/**
 * Start from here.
 */
$(function () {
  /**
   * Define a mixin object.
   */
  vue__WEBPACK_IMPORTED_MODULE_13__["default"].mixin((_components_trans__WEBPACK_IMPORTED_MODULE_3___default()));
  vue__WEBPACK_IMPORTED_MODULE_13__["default"].mixin({
    data: function data() {
      return {
        imageObserver: null,
        navContainer: false,
        headerItemsCount: 0,
        sharedRootCategories: [],
        responsiveSidebarTemplate: '',
        responsiveSidebarKey: Math.random(),
        baseUrl: getBaseUrl()
      };
    },
    methods: {
      redirect: function redirect(route) {
        route ? window.location.href = route : '';
      },
      debounceToggleSidebar: function debounceToggleSidebar(id, _ref2, type) {
        var target = _ref2.target;
        this.toggleSidebar(id, target, type);
      },
      toggleSidebar: function toggleSidebar(id, _ref3, type) {
        var target = _ref3.target;
        if (Array.from(target.classList)[0] === 'main-category' || Array.from(target.parentElement.classList)[0] === 'main-category') {
          var sidebar = $("#sidebar-level-".concat(id));
          if (sidebar && sidebar.length > 0) {
            if (type === 'mouseover') {
              this.show(sidebar);
            } else if (type === 'mouseout') {
              this.hide(sidebar);
            }
          }
        } else if (Array.from(target.classList)[0] === 'category' || Array.from(target.classList)[0] === 'category-icon' || Array.from(target.classList)[0] === 'category-title' || Array.from(target.classList)[0] === 'category-content' || Array.from(target.classList)[0] === 'rango-arrow-right') {
          var parentItem = target.closest('li');
          if (target.id || parentItem.id.match('category-')) {
            var subCategories = $("#".concat(target.id ? target.id : parentItem.id, " .sub-categories"));
            if (subCategories && subCategories.length > 0) {
              var subCategories1 = Array.from(subCategories)[0];
              subCategories1 = $(subCategories1);
              if (type === 'mouseover') {
                this.show(subCategories1);
                var sidebarChild = subCategories1.find('.sidebar');
                this.show(sidebarChild);
              } else if (type === 'mouseout') {
                this.hide(subCategories1);
              }
            } else {
              if (type === 'mouseout') {
                var _sidebar = $("#".concat(id));
                _sidebar.hide();
              }
            }
          }
        }
      },
      show: function show(element) {
        element.show();
        element.mouseleave(function (_ref4) {
          var target = _ref4.target;
          $(target.closest('.sidebar')).hide();
        });
      },
      hide: function hide(element) {
        element.hide();
      },
      toggleButtonDisability: function toggleButtonDisability(_ref5) {
        var event = _ref5.event,
          actionType = _ref5.actionType;
        var button = event.target.querySelector('button[type=submit]');
        button ? button.disabled = actionType : '';
      },
      onSubmit: function onSubmit(event) {
        var _this = this;
        this.toggleButtonDisability({
          event: event,
          actionType: true
        });
        if (typeof tinyMCE !== 'undefined') tinyMCE.triggerSave();
        this.$validator.validateAll().then(function (result) {
          if (result) {
            event.target.submit();
          } else {
            _this.toggleButtonDisability({
              event: event,
              actionType: false
            });
            eventBus.$emit('onFormError');
          }
        });
      },
      isMobile: isMobile,
      loadDynamicScript: function (_loadDynamicScript) {
        function loadDynamicScript(_x, _x2) {
          return _loadDynamicScript.apply(this, arguments);
        }
        loadDynamicScript.toString = function () {
          return _loadDynamicScript.toString();
        };
        return loadDynamicScript;
      }(function (src, onScriptLoaded) {
        loadDynamicScript(src, onScriptLoaded);
      }),
      getDynamicHTML: function getDynamicHTML(input) {
        var _staticRenderFns, output;
        var _Vue$compile = vue__WEBPACK_IMPORTED_MODULE_13__["default"].compile(input),
          render = _Vue$compile.render,
          staticRenderFns = _Vue$compile.staticRenderFns;
        if (this.$options.staticRenderFns.length > 0) {
          _staticRenderFns = this.$options.staticRenderFns;
        } else {
          _staticRenderFns = this.$options.staticRenderFns = staticRenderFns;
        }
        try {
          output = render.call(this, this.$createElement);
        } catch (exception) {
          console.log(this.__('error.something_went_wrong'));
        }
        this.$options.staticRenderFns = _staticRenderFns;
        return output;
      },
      getStorageValue: function getStorageValue(key) {
        var value = window.localStorage.getItem(key);
        if (value) {
          value = JSON.parse(value);
        }
        return value;
      },
      setStorageValue: function setStorageValue(key, value) {
        window.localStorage.setItem(key, JSON.stringify(value));
        return true;
      }
    }
  });
  window.app = new vue__WEBPACK_IMPORTED_MODULE_13__["default"]({
    el: '#app',
    data: function data() {
      return {
        loading: false,
        modalIds: {},
        miniCartKey: 0,
        quickView: false,
        productDetails: [],
        currentScreen: window.innerWidth
      };
    },
    created: function created() {
      window.addEventListener('resize', this.handleResize);
    },
    destroyed: function destroyed() {
      window.removeEventListener('resize', this.handleResize);
    },
    mounted: function mounted() {
      this.$validator.localize(document.documentElement.lang);
      this.addServerErrors();
      this.loadCategories();
      this.addIntersectionObserver();
    },
    methods: {
      onSubmit: function onSubmit(event) {
        var _this2 = this;
        this.toggleButtonDisability({
          event: event,
          actionType: true
        });
        if (typeof tinyMCE !== 'undefined') tinyMCE.triggerSave();
        this.$validator.validateAll().then(function (result) {
          if (result) {
            event.target.submit();
          } else {
            _this2.toggleButtonDisability({
              event: event,
              actionType: false
            });
            eventBus.$emit('onFormError');
          }
        });
      },
      toggleButtonDisable: function toggleButtonDisable(value) {
        var buttons = document.getElementsByTagName('button');
        for (var i = 0; i < buttons.length; i++) {
          buttons[i].disabled = value;
        }
      },
      addServerErrors: function addServerErrors() {
        var _this3 = this;
        var scope = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var _loop = function _loop() {
          var inputNames = [];
          key.split('.').forEach(function (chunk, index) {
            if (index) {
              inputNames.push('[' + chunk + ']');
            } else {
              inputNames.push(chunk);
            }
          });
          var inputName = inputNames.join('');
          var field = _this3.$validator.fields.find({
            name: inputName,
            scope: scope
          });
          if (field) {
            _this3.$validator.errors.add({
              id: field.id,
              field: inputName,
              msg: serverErrors[key][0],
              scope: scope
            });
          }
        };
        for (var key in serverErrors) {
          _loop();
        }
      },
      addFlashMessages: function addFlashMessages() {
        if (window.flashMessages.alertMessage) window.alert(window.flashMessages.alertMessage);
      },
      showModal: function showModal(id) {
        this.$set(this.modalIds, id, true);
      },
      loadCategories: function loadCategories() {
        var _this4 = this;
        this.$http.get("".concat(this.baseUrl, "/categories")).then(function (response) {
          _this4.sharedRootCategories = response.data.categories;
          $("<style type='text/css'> .sub-categories{ min-height:".concat(response.data.categories.length * 30, "px;} </style>")).appendTo('head');
        })["catch"](function (error) {
          console.error("Failed to load categories:", error);
        });
      },
      addIntersectionObserver: function addIntersectionObserver() {
        this.imageObserver = new IntersectionObserver(function (entries, imgObserver) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              var lazyImage = entry.target;
              lazyImage.src = lazyImage.dataset.src;
            }
          });
        });
      },
      showLoader: function showLoader() {
        this.loading = true;
      },
      hideLoader: function hideLoader() {
        this.loading = false;
      },
      handleResize: function handleResize() {
        this.currentScreen = window.innerWidth;
      }
    }
  });
});

/***/ }),

/***/ "./src/Resources/assets/js/bootstrap.js":
/*!**********************************************!*\
  !*** ./src/Resources/assets/js/bootstrap.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

window._ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
window.axios = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
window.$ = window.jQuery = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
if (window.axios) {
  window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

  /**
   * Next we will register the CSRF Token as a common header with Axios so that
   * all outgoing HTTP requests automatically have it attached. This is just
   * a simple convenience so we don't have to attach every token manually.
   */

  var token = document.head.querySelector('meta[name="csrf-token"]');
  if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
  } else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
  }
}

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["js/components"], () => (__webpack_exec__("./src/Resources/assets/js/app.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);