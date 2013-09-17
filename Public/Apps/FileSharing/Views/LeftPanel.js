define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/FileSharing/Collections/Files',
    'text!Apps/FileSharing/Templates/left_panel.html'
], function ($, _, Backbone, FilesCollection, LeftPanelTemplate) {
    var LeftPanelView = Backbone.View.extend({
        tagName: "div",
        className: "k_fs_left_panel",
        initialize: function () {

        },
        init: function (SmartBlocks, folder_browser) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.folder_browser = folder_browser;
            base.shared_folders = new FilesCollection();
            SmartBlocks.events.on("ws_notification", function (message) {
                if (message.app == "k_fs") {
                    if (message.status == "sharing_update")
                    {
                        base.refresh();
                    }
                }

            });
            base.refresh();

        },
        refresh: function () {
            var base = this;
            base.shared_folders = new FilesCollection();
            base.shared_folders.fetch({
                data: {
                    "shared": true
                },
                success: function () {
                    base.render();
                }
            });
        },
        render: function () {
            var base = this;

            var template = _.template(LeftPanelTemplate, { folders: base.shared_folders.models });
            base.$el.html(template);
            base.initializeEvents();
        },
        initializeEvents: function () {
            var base = this;

            base.$el.find(".k_fs_shared_folders_item").click(function () {
                var elt = $(this);
                base.folder_browser.fetchAll(elt.attr("data-id"));
            });

        }
    });

    return LeftPanelView;
    return undefined;
});