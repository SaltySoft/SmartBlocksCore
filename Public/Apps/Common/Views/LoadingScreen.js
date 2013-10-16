define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/Common/Templates/loading_screen.html',
    'Apps/Common/Views/ProgressBar'
], function ($, _, Backbone, LoadingScreenTemplate, ProgressBarView) {
    var View = Backbone.View.extend({
        tagName: "div",
        className: "loading_screen",
        initialize: function () {
            var base = this;
            var pb_view = new ProgressBarView();
            base.pb_view = pb_view;
            base.initiated = false;
            base.show_progress_bar = true;
        },
        init: function (show_progress_bar) {
            var base = this;

            base.render();
            base.initiated = true;

        },
        render: function () {
            var base = this;

            var template = _.template(LoadingScreenTemplate, {});
            base.$el.html(template);


            base.$el.find(".loading_status").html(base.pb_view.$el);

            base.pb_view.init(base.SmartBlocks);
            base.setLoad(0);

        },
        setMax: function (max) {
            var base = this;
            base.pb_view.setMax(max);
        },
        setLoad: function (load) {
            var base = this;
            base.pb_view.setLoad(load);
        },
        setText: function (text) {
            var base = this;
            base.$el.find(".loading_text").text(text);
        },
        registerEvents: function () {
            var base = this;
        },
        hidePb: function () {
            var base = this;
            base.$el.find(".loading_status").hide();
        },
        showPb: function () {
            var base = this;
            base.$el.find(".loading_status").show();
        }
    });

    return View;
});