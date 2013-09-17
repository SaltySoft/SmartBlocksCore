define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/NotificationsCenter/Templates/notification.html'
], function ($, _, Backbone, NotificationTemplate) {
    var NotificationView = Backbone.View.extend({
        tagName: "li",
        className: "notif",
        initialize: function (obj) {
            var base = this;
            base.model = obj.model;
        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.render();
            base.registerEvents();

        },
        render: function () {
            var base = this;
            var template = _.template(NotificationTemplate, { notification: base.model });
            base.$el.html(template);
            if (base.model.get("seen") === true) {
                base.$el.addClass("seen");
            }
        },
        registerEvents: function () {
            var base = this;
        }
    });

    return NotificationView;
});