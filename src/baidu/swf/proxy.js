/*
 * Tangram
 * Copyright 2011 Baidu Inc. All rights reserved.
 *
 * path: baidu/swf.js
 * author: liyubei@baidu.com (leeight)
 * version: 1.0.0
 * date: 2011/02/28
 */


///import baidu.swf;
///import baidu.swf.getMovie;

/**
 * baidu.swf.proxy
 * @constructor
 * @param {string} id 使用baidu.swf.create的时候指定的那个id.
 * @param {string} property 方法或者属性名称，用来检测Flash是否初始化好了.
 * @param {Function=} opt_loadedHandler 初始化之后的回调函数.
 */
baidu.swf.proxy = function(id, property, opt_loadedHandler) {
    this._id = id;
    this._property = property;
    this._loadedHandler = opt_loadedHandler || null;

    /**
     * 页面上的Flash对象
     * @type {HTMLElement}
     */
    this._flash = null;
};

/**
 * @return {HTMLElement} Flash对象.
 */
baidu.swf.proxy.prototype.getFlash = function() {
    return (this._flash || (this._flash = baidu.swf.getMovie(this._id)));
};

/**
 * 初始化
 */
baidu.swf.proxy.prototype.init = function() {
    if (this._property) {
        var flash = this.getFlash(),
            property = this._property,
            loadedHandler = this._loadedHandler;
        var timer = setInterval(function() {
            try {
                /** @preserveTry */
                if (flash[property]) {
                    clearInterval(timer);
                    if (loadedHandler) {
                        loadedHandler();
                    }
                }
            } catch (e) {
            }
        }, 100);
    }
};

/**
 * 调用Flash中的某个方法
 * @param {string} methodName 方法名.
 * @param {...*} var_args 方法的参数.
 */
baidu.swf.proxy.prototype.call = function(methodName, var_args) {
    try {
        var flash = this.getFlash(),
            args = Array.prototype.slice.call(arguments);

        args.shift();
        if (flash[methodName]) {
            flash[methodName].apply(flash, args);
        }
    } catch (e) {
    }
};



















/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */