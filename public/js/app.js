/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

throw new Error("Module build failed (from ./node_modules/babel-loader/lib/index.js):\nError: Cannot find module '@babel/core'\nRequire stack:\n- /var/www/node_modules/babel-loader/lib/index.js\n- /var/www/node_modules/loader-runner/lib/loadLoader.js\n- /var/www/node_modules/loader-runner/lib/LoaderRunner.js\n- /var/www/node_modules/webpack/lib/NormalModule.js\n- /var/www/node_modules/webpack/lib/NormalModuleFactory.js\n- /var/www/node_modules/webpack/lib/Compiler.js\n- /var/www/node_modules/webpack/lib/webpack.js\n- /var/www/node_modules/webpack-cli/bin/utils/validate-options.js\n- /var/www/node_modules/webpack-cli/bin/utils/convert-argv.js\n- /var/www/node_modules/webpack-cli/bin/cli.js\n- /var/www/node_modules/webpack/bin/webpack.js\n babel-loader@8 requires Babel 7.x (the package '@babel/core'). If you'd like to use Babel 6.x ('babel-core'), you should install 'babel-loader@7'.\n    at Function.Module._resolveFilename (internal/modules/cjs/loader.js:1029:15)\n    at Function.Module._load (internal/modules/cjs/loader.js:898:27)\n    at Module.require (internal/modules/cjs/loader.js:1089:19)\n    at require (/var/www/node_modules/v8-compile-cache/v8-compile-cache.js:161:20)\n    at Object.<anonymous> (/var/www/node_modules/babel-loader/lib/index.js:10:11)\n    at Module._compile (/var/www/node_modules/v8-compile-cache/v8-compile-cache.js:194:30)\n    at Object.Module._extensions..js (internal/modules/cjs/loader.js:1220:10)\n    at Module.load (internal/modules/cjs/loader.js:1049:32)\n    at Function.Module._load (internal/modules/cjs/loader.js:937:14)\n    at Module.require (internal/modules/cjs/loader.js:1089:19)\n    at require (/var/www/node_modules/v8-compile-cache/v8-compile-cache.js:161:20)\n    at loadLoader (/var/www/node_modules/loader-runner/lib/loadLoader.js:18:17)\n    at iteratePitchingLoaders (/var/www/node_modules/loader-runner/lib/LoaderRunner.js:169:2)\n    at runLoaders (/var/www/node_modules/loader-runner/lib/LoaderRunner.js:365:2)\n    at NormalModule.doBuild (/var/www/node_modules/webpack/lib/NormalModule.js:295:3)\n    at NormalModule.build (/var/www/node_modules/webpack/lib/NormalModule.js:446:15)\n    at Compilation.buildModule (/var/www/node_modules/webpack/lib/Compilation.js:739:10)\n    at /var/www/node_modules/webpack/lib/Compilation.js:981:14\n    at /var/www/node_modules/webpack/lib/NormalModuleFactory.js:409:6\n    at /var/www/node_modules/webpack/lib/NormalModuleFactory.js:155:13\n    at AsyncSeriesWaterfallHook.eval [as callAsync] (eval at create (/var/www/node_modules/tapable/lib/HookCodeFactory.js:33:10), <anonymous>:6:1)\n    at AsyncSeriesWaterfallHook.lazyCompileHook (/var/www/node_modules/tapable/lib/Hook.js:154:20)\n    at /var/www/node_modules/webpack/lib/NormalModuleFactory.js:138:29\n    at /var/www/node_modules/webpack/lib/NormalModuleFactory.js:346:9\n    at processTicksAndRejections (internal/process/task_queues.js:79:11)");

/***/ }),

/***/ "./resources/sass/app.scss":
/*!*********************************!*\
  !*** ./resources/sass/app.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

throw new Error("Module build failed (from ./node_modules/css-loader/dist/cjs.js):\nTypeError: text.forEach is not a function\n    at /var/www/node_modules/extract-text-webpack-plugin/dist/loader.js:145:16\n    at /var/www/node_modules/webpack/lib/Compiler.js:343:11\n    at /var/www/node_modules/webpack/lib/Compiler.js:681:15\n    at AsyncSeriesHook.eval [as callAsync] (eval at create (/var/www/node_modules/tapable/lib/HookCodeFactory.js:33:10), <anonymous>:24:1)\n    at AsyncSeriesHook.lazyCompileHook (/var/www/node_modules/tapable/lib/Hook.js:154:20)\n    at /var/www/node_modules/webpack/lib/Compiler.js:678:31\n    at AsyncSeriesHook.eval [as callAsync] (eval at create (/var/www/node_modules/tapable/lib/HookCodeFactory.js:33:10), <anonymous>:6:1)\n    at AsyncSeriesHook.lazyCompileHook (/var/www/node_modules/tapable/lib/Hook.js:154:20)\n    at /var/www/node_modules/webpack/lib/Compilation.js:1423:35\n    at AsyncSeriesHook.eval [as callAsync] (eval at create (/var/www/node_modules/tapable/lib/HookCodeFactory.js:33:10), <anonymous>:6:1)\n    at AsyncSeriesHook.lazyCompileHook (/var/www/node_modules/tapable/lib/Hook.js:154:20)\n    at /var/www/node_modules/webpack/lib/Compilation.js:1414:32\n    at AsyncSeriesHook.eval [as callAsync] (eval at create (/var/www/node_modules/tapable/lib/HookCodeFactory.js:33:10), <anonymous>:6:1)\n    at AsyncSeriesHook.lazyCompileHook (/var/www/node_modules/tapable/lib/Hook.js:154:20)\n    at /var/www/node_modules/webpack/lib/Compilation.js:1409:36\n    at AsyncSeriesHook.eval [as callAsync] (eval at create (/var/www/node_modules/tapable/lib/HookCodeFactory.js:33:10), <anonymous>:6:1)\n    at AsyncSeriesHook.lazyCompileHook (/var/www/node_modules/tapable/lib/Hook.js:154:20)\n    at /var/www/node_modules/webpack/lib/Compilation.js:1405:32\n    at AsyncSeriesHook.eval [as callAsync] (eval at create (/var/www/node_modules/tapable/lib/HookCodeFactory.js:33:10), <anonymous>:6:1)\n    at AsyncSeriesHook.lazyCompileHook (/var/www/node_modules/tapable/lib/Hook.js:154:20)\n    at Compilation.seal (/var/www/node_modules/webpack/lib/Compilation.js:1342:27)\n    at /var/www/node_modules/webpack/lib/Compiler.js:675:18\n    at /var/www/node_modules/webpack/lib/Compilation.js:1261:4\n    at AsyncSeriesHook.eval [as callAsync] (eval at create (/var/www/node_modules/tapable/lib/HookCodeFactory.js:33:10), <anonymous>:15:1)\n    at AsyncSeriesHook.lazyCompileHook (/var/www/node_modules/tapable/lib/Hook.js:154:20)\n    at Compilation.finish (/var/www/node_modules/webpack/lib/Compilation.js:1253:28)\n    at /var/www/node_modules/webpack/lib/Compiler.js:672:17\n    at eval (eval at create (/var/www/node_modules/tapable/lib/HookCodeFactory.js:33:10), <anonymous>:11:1)\n    at /var/www/node_modules/webpack/lib/Compilation.js:1185:12\n    at /var/www/node_modules/webpack/lib/Compilation.js:1097:9\n    at processTicksAndRejections (internal/process/task_queues.js:79:11)");

/***/ }),

/***/ 0:
/*!*************************************************************!*\
  !*** multi ./resources/js/app.js ./resources/sass/app.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /var/www/resources/js/app.js */"./resources/js/app.js");
module.exports = __webpack_require__(/*! /var/www/resources/sass/app.scss */"./resources/sass/app.scss");


/***/ })

/******/ });