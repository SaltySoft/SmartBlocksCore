define([
    'jquery',
    'underscore',
    'backbone',
    'text!../Templates/progress_bar.html'
], function ($, _, Backbone, ProgressBarTemplate) {
    var View = Backbone.View.extend({
        tagName: "div",
        className: "progress_bar",
        initialize: function () {
            var base = this;
            base.max = 100;
            base.load = 0;
        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;

            base.render();
        },
        render: function () {
            var base = this;

            var template = _.template(ProgressBarTemplate, {});
            base.$el.html(template);
        },
        registerEvents: function () {
            var base = this;
        },
        setMax: function (max) {
            var base = this;
            base.max = max;
        },
        setLoad: function (load) {
            var base = this;

            if (load < 0)
                load = 0;
            if (load > base.max)
                load = base.max;
            base.load = load;

            var percent = Math.round(base.load / base.max * 100);

            base.$el.find(".progress_bar_load").css("width", percent + "%");
        }
    });

    return View;
});