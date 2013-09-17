define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/FileSharing/Templates/file_upload.html'
], function ($, _, Backbone, FileUploadTemplate) {
    var FileUploadView = Backbone.View.extend({
        tagName: "div",
        className: "k_fs_file_upload",
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
            base.$el.addClass("cache");
            var container = $(document.createElement("div"));
            container.addClass("file_upload_form");
            base.$el.append(container);

            var template = _.template(FileUploadTemplate, { parent_folder_id: base.folder_browser.folder.get("id")  });
            container.html(template);

            base.initializeEvents();
        },
        initializeEvents: function () {
            var base = this;
            base.$el.find(".k_fs_upload_button").click(function () {
                base.$el.find("iframe").load(function () {
                    base.$el.remove();
                    base.folder_browser.reload();
                });
            });

            base.$el.find(".k_fs_upload_cancel_button").click(function () {
                base.$el.remove();
            });
        }
    });

    return FileUploadView
});