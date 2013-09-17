define([
    'underscore',
    'backbone'
], function (_, Backbone) {
    var Group = Backbone.Model.extend({
        urlRoot: "/Groups",
        defaults: {
            name: "unregistered"
        }
    });

    return Group;
});

