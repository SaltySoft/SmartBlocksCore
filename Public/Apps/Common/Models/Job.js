define([
    'underscore',
    'backbone'
], function (_, Backbone) {
    var Job = Backbone.Model.extend({
        urlRoot: "/Jobs",
        defaults: {
            name: "unregistered"
        }
    });

    return Job;
});