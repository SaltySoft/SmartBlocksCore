define([
    'underscore',
    'backbone'
], function (_, Backbone) {
    var Application = Backbone.Model.extend({
        defaults:{
//            "logo_url":"/Public/images/logo.png"
        }
    });

    return Application;
});