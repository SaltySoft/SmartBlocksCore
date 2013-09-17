define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/UserRequester/Templates/notification.html',
    'Apps/UserRequester/Views/Form'
], function ($, _, Backbone, NotificationTemplate, FormView) {
    var NotificationView = Backbone.View.extend({
        tagName: "div",
        className: 'user_request_notification',
        initialize: function () {

        },
        init: function (SmartBlocks, data) {
            var base = this;

            base.SmartBlocks = SmartBlocks;
            base.data = data;
            base.$el.addClass(base.data.class);
            base.render();
        },
        render: function () {
            var base = this;

            if ($(document).find(".user_request_notif_container").length > 0) {
                $(document).find(".user_request_notif_container").append(base.$el);
            } else {
                var div = $(document.createElement("div"));
                div.addClass("user_request_notif_container");
                $("body").append(div);
                div.append(base.$el);
            }





            var template = _.template(NotificationTemplate, {
                text: base.data.notification_text
            });
            base.$el.html(template);
            base.registerEvents();
        },
        registerEvents: function () {
            var base = this;

            base.$el.delegate(".answer_button", "click", function (e) {
                var form_view = new FormView();
                form_view.init(base.SmartBlocks, base.data);
                base.$el.remove();
            });

            base.$el.delegate(".decline_button", "click", function (e) {
                base.$el.slideUp(200, function () {
                    base.$el.remove();
                    if ($(document).find(".user_request_notification").length <= 0) {
                        $(document).find(".user_request_notif_container");
                    }
                });
            });
        }

    });

    return NotificationView;
});