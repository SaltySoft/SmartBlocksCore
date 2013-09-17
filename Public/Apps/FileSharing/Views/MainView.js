define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/FileSharing/Templates/main_view.html',
    'Apps/FileSharing/Views/FolderBrowser',
    'Apps/FileSharing/Views/Controls',
    'Apps/FileSharing/Views/LeftPanel'
], function ($, _, Backbone, MainViewTemplate, FolderBrowserView, ControlsView, LeftPanelView) {
    var MainView = Backbone.View.extend({
        tagName: "div",
        className:"k_fs_main_view",
        initialize: function () {

        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.render();

        },
        render: function () {
            var base = this;
            var template = _.template(MainViewTemplate, {});
            base.$el.html(template);

            base.folder_browser = new FolderBrowserView();
            base.folder_browser.init(base.SmartBlocks);
            base.$el.find(".k_fs_folder_browser_container").html(base.folder_browser.$el);

            var left_panel = new LeftPanelView();
            left_panel.init(base.SmartBlocks, base.folder_browser);
            base.$el.find(".k_fs_left_panel_container").html(left_panel.$el);

            var controls_view = new ControlsView();
            controls_view.init(base.SmartBlocks, base.folder_browser);
            base.$el.find(".k_fs_dragdrop_container").html(controls_view.$el);



            base.initializeEvents();
        },
        initializeEvents: function () {
            var base = this;

            base.$el.find(".k_ds_close_button").click(function () {
                base.$el.parent().parent().hide();
            });
        },
        refresh : function () {
            var base = this;
            base.folder_browser.refresh();
        }
    });

    return MainView;
});