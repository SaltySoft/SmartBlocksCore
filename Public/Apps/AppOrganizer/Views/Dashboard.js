define([
    'jquery',
    'underscore',
    'backbone',
    'jqueryflip',
    'text!Apps/AppOrganizer/Templates/dashboard.html',
    "Apps/AppOrganizer/Collections/Blocks",
    "amplify"
], function ($, _, Backbone, JqueryFlip, DashboardTemplate, BlocksCollection) {
    var Dashboard = Backbone.View.extend({
        tagName: "div",
        className: "k_ao_dashboard",
        events: {

        },
        initialize: function () {

        },
        init: function (AppEvents) {
            var base = this;
            this.AppEvents = AppEvents;

            base.blocks_collection = SmartBlocks.Data.blocks;
            if (amplify.store("blocks_collection") === undefined || true) {
                base.render();
                amplify.store("blocks_collection", base.blocks_collection)
            } else {
                base.blocks_collection = new BlocksCollection(amplify.store("blocks_collection"));
                base.blocks_collection.reparse();
                base.render();
            }
        },
        render: function () {
            var base = this;

            var template = _.template(DashboardTemplate, {
                blocks: base.blocks_collection.models,
                kernel: "kernel"
            });

            base.$el.html(template);
            base.initializeEvents();
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
                    var randomNumber = Math.floor((Math.random() * 4) + 1);
                    var flipDir = 'tb';
                    if (randomNumber == 2)
                        flipDir = 'bt';
                    if (randomNumber == 3)
                        flipDir = 'lr';
                    if (randomNumber == 4)
                        flipDir = 'rl';

                    elt.flip({
                        direction: flipDir,
                        color: elt.css("background-color"),
                        content: elt.attr("data-description") + '<div class="app_info_button"><img src="/images/icons/arrow_undo.png" /></div>',
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