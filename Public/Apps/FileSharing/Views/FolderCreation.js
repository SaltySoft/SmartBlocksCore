define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/FileSharing/Models/File',
    'text!Apps/FileSharing/Templates/folder_creation.html'
], function ($, _, Backbone, File, FolderCreationTemplate) {
    var FolderCreationView = Backbone.View.extend({
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
            container.addClass("popup");
            base.$el.append(container);

            var template = _.template(FolderCreationTemplate, { parent_folder_id: base.folder_browser.current_folder  });
            container.html(template);

            base.initializeEvents();
        },
        initializeEvents: function () {
            var base = this;
            base.$el.find(".k_fs_folder_creation_cancel_button").click(function () {
                base.$el.remove();
            });
            base.$el.find(".k_fs_folder_creation_button").click(function () {
                var array = base.$el.find(".k_fs_folder_creation_form").serializeArray();
                var ob_lit = {};

                for (k in array) {
                    ob_lit[array[k].name]  = array[k].value
                }
                var folder = new File(ob_lit);
                folder.set("is_folder", true);
                folder.set("parent_folder", new File({ id: base.folder_browser.current_folder}) );
                console.log(folder);
                base.SmartBlocks.startLoading("Creating folder");
                folder.save({}, {
                    success: function () {
                        base.$el.remove();
                        base.SmartBlocks.stopLoading();
                        console.log(folder);
                        base.folder_browser.reload();
                    }
                });
            });
        }
    });

    return FolderCreationView
});