define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/UserRequester/Views/Notification'
], function ($, _, Backbone, NotificationView) {
    var initialize = function () {
        var notifications = [];

        SmartBlocks.events.on("ws_notification", function (message) {
            if (message.app == "user_requester") {
                if ($(document).find("." + message.data.class).length == 0) {
                    var notification = new NotificationView();
                    notification.init(SmartBlocks, message.data);
                }

            }
        });
    };

    return {
        initialize: initialize
    };
});