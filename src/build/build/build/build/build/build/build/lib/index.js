/**
 * Created by liuhuizhi on 15/11/12.
 */

require.config({
    baseUrl: '../build/lib',
    path: {
        fastclick: "fastclick.js",
        zepto: "zepto.js"
    }
})



requirejs(['zepto', 'fastclick'],function($,fc){
    console.log($);
    console.log(fc);
})

//define([fastclick],function(fc){
//    console.log(fc);
//})