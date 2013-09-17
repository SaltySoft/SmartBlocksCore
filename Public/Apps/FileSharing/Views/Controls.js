define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/FileSharing/Templates/controls.html'
], function ($, _, Backbone, ControlsTemplate) {
    var ControlsView = Backbone.View.extend({
        tagName: "div",
        className: "k_fs_controls",
        initialize: function () {

        },
        init: function (SmartBlocks, folder_browser) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.folder_browser = folder_browser;
            base.render();
        },
        render: function () {
            var base = this;
            var template = _.template(ControlsTemplate, {});
            base.$el.html(template);
            base.initializeEvents();
        },
        initializeEvents: function () {
            var base = this;
            base.$el.find(".k_fs_controls_folder_create_button").click(function () {
                base.folder_browser.createFolder();
            });

            base.$el.find(".k_fs_controls_file_upload_button").click(function () {
                base.folder_browser.uploadFile();
            });
        }
    });

    return ControlsView;
});