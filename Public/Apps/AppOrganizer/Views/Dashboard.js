define([
    'jquery',
    'underscore',
    'backbone',
    "Apps/AppOrganizer/Views/AppThumbnail",
    'text!Apps/AppOrganizer/Templates/dashboard.html',
    'jqueryflip',
    "amplify"
], function ($, _, Backbone, AppThumbnail, DashboardTemplate, JqueryFlip) {
    var Dashboard = Backbone.View.extend({
        tagName: "div",
        className: "k_ao_dashboard",
        events: {

        },
        initialize: function () {

        },
        init: function (app) {
            var base = this;
            base.app = app;
            base.render();
        },
        render: function () {
            var base = this;

            var template = _.template(DashboardTemplate, {
            });

            base.$el.html(template);
            base.initializeEvents();

            for (var k in SmartBlocks.Data.apps.models) {
                var app = SmartBlocks.Data.apps.models[k];
                if (!app.get("dashboard_ignore")) {
                    var thumbnail = new AppThumbnail({
                        model: app
                    });
                    base.$el.find(".apps_container").append(thumbnail.$el);
                    thumbnail.init();
                }
            }
        },
        initializeEvents: function () {
            var base = this;
            base.$el.delegate(".dashboard_app", "click", function () {
                if ($(this).attr("data-flip") == 0) {
                    window.location = $(this).attr("data-link");
                }
            });

            base.$el.delegate(".app_info_button", "click", function (e) {
                var elt = $(this).parent();
                e.stopPropagation();
                if (elt.attr("data-flip") == 0) {
                    var flipDir = 'rl';
                    elt.flip({
                        direction: flipDir,
                        color: elt.css("background-color"),
                        content: '<div class="desc_title">Description :</div><div class="description">' + elt.attr("data-description") + '</div><div class="app_info_button"><img src="/images/icons/arrow_undo.png" /></div>',
                        speed: 100
                    });
                    elt.attr("data-flip", 1);
                }
                else {
                    elt.revertFlip();
                    elt.attr("data-flip", 0);
                }
            });
            base.$el.find(".nameLink").click(function (e) {
                e.stopPropagation();
            });

        }
    });

    return Dashboard;
});