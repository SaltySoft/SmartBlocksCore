define([
    'jquery',
    'underscore',
    'backbone',
    'text!../Templates/app_thumbnail.html'
], function ($, _, Backbone, thumbnail_tpl) {
    var View = Backbone.View.extend({
        tagName: "div",
        className: "application_thumbnail",
        initialize: function (app) {
            var base = this;
            base.app = app;
        },
        init: function () {
            var base = this;

            base.render();
        },
        render: function () {
            var base = this;

            var template = _.template(thumbnail_tpl, {
                app: base.app
            });
            base.$el.html(template);


        },
        registerEvents: function () {
            var base = this;
        }
    });

    return View;
});