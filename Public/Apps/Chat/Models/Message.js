define([
    'underscore',
    'backbone'
], function (_, Backbone) {
    var Message = Backbone.Model.extend({
        urlRoot: "/Messages",
        defaults: {
        },
        parse: function (response, options) {
            return response;
        }
    });

    return Message;
});

