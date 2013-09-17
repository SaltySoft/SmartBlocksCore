define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/NotificationsCenter/Templates/main_template.html',
    'Apps/NotificationsCenter/Models/Notification',
    'Apps/NotificationsCenter/Collections/Notifications',
    'Apps/NotificationsCenter/Views/notification'
], function ($, _, Backbone, MainTemplate, Notification, NotificationsCollection, NotifView) {
    var MainView = Backbone.View.extend({
        tagName: "div",
        className: "notification_center",
        initialize: function () {

        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.notifications = new NotificationsCollection();
            base.render();
            base.registerEvents();
            base.fetch();
        },
        render: function () {
            var base = this;
            var template = _.template(MainTemplate, {});
            base.$el.html(template);
        },
        fillList: function () {
            var base = this;
            var list_container = base.$el.find(".notif_list");
            list_container.html("");
            for (var k in base.notifications.models) {
                var notification = base.notifications.models[k];
                var notif_view = new NotifView({ model: notification });
                list_container.append(notif_view.$el);
                notif_view.init(base.SmartBlocks);
            }

        },
        fetch: function () {
            var base = this;
            base.notifications.fetch({
                success: function () {
                    base.fillList();
                }
            });
        },
        registerEvents: function () {
            var base = this;

            base.notifications.on("add", function () {
                base.fillList();
            });

            base.SmartBlocks.events.on("ws_notification", function (message) {
                if (message.app === "notifications") {
                    var notif = new Notification(message.notification);
                    base.notifications.add(notif);
                }
            });
        }
    });

    return MainView;
});