define([
    'underscore',
    'backbone'

], function (_, Backbone) {
    var Block = Backbone.Model.extend({
        baseUrl:"/Blocks",
        defaults:{
            "color":"#024053"
        }
    });

    return Block;
});