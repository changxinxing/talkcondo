(window.webpackJsonp=window.webpackJsonp||[]).push([[8],{269:function(t,e,n){"use strict";n.r(e),n.d(e,"AdvancedButton",(function(){return d}));n(31),n(38),n(39),n(57),n(32),n(80),n(18),n(81),n(29),n(21),n(40);var r=n(0),o=n.n(r),u=n(1),c=n.n(u);function i(t){return(i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function f(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function a(t,e){for(var n=0;n<e.length;n++){var r=e[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(t,r.key,r)}}function l(t,e){return(l=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}function p(t){var e=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(t){return!1}}();return function(){var n,r=b(t);if(e){var o=b(this).constructor;n=Reflect.construct(r,arguments,o)}else n=r.apply(this,arguments);return s(this,n)}}function s(t,e){return!e||"object"!==i(e)&&"function"!=typeof e?y(t):e}function y(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}function b(t){return(b=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function h(t,e,n){return e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}
/**
 * @package     Gravity PDF
 * @copyright   Copyright (c) 2021, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       6.0
 */var d=function(t){!function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&l(t,e)}(c,t);var e,n,r,u=p(c);function c(){var t;f(this,c);for(var e=arguments.length,n=new Array(e),r=0;r<e;r++)n[r]=arguments[r];return h(y(t=u.call.apply(u,[this].concat(n))),"handleClick",(function(e){e.preventDefault(),t.props.history.push("/fontmanager/")})),t}return e=c,(n=[{key:"render",value:function(){return o.a.createElement("button",{type:"button",className:"button gfpdf-button",onClick:this.handleClick},GFPDF.manage)}}])&&a(e.prototype,n),r&&a(e,r),c}(r.Component);h(d,"propTypes",{history:c.a.object}),e.default=d}}]);