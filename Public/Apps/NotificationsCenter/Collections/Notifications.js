define([
    'underscore',
    'backbone',
    'Apps/NotificationsCenter/Models/Notification'
], function (_, Backbone, Notification) {
    var NotificationsCollection = Backbone.Collection.extend({
        model: Notification,
        url: "/Notifications",
        comparator: function (notification) {
            return -1 * notification.get("timestamp");
        }
    });

    return NotificationsCollection;
});