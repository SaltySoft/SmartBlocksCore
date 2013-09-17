define([
    'underscore',
    'backbone'
], function (_, Backbone) {
    var Notification = Backbone.Model.extend({
        urlRoot: "/Notifications",
        defaults: {
            content : "",
            link : "javascript:void(0);",
            seen: false,
            user: {
                id : 0
            }
        }
    });

    return Notification;
});