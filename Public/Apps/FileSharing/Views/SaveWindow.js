define([
    "jquery",
    "underscore",
    'underscore_string',
    "backbone",
    "Apps/FileSharing/Models/File",
    "text!Apps/FileSharing/Templates/save_window.html",
    "text!Apps/FileSharing/Templates/file_list.html"
], function ($, _, _s, Backbone, File, SaveWindowTemplate, FileListTemplate) {
    var SaveWindowView = Backbone.View.extend({
        tagName: "div",
        className: "k_fs_save_window cache",
        initialize: function (SmartBlocks, data, extension) {
            var base = this;

            base.extension = extension;

            base.events = $.extend(true, {}, Backbone.Events);
            base.SmartBlocks = SmartBlocks;
            base.data = data;
            base.init();

        },
        init: function () {
            var base = this;
            base.name = "";
            base.render();
        },
        loadFolder: function (folder_id) {
            var base = this;
            base.folder = new File({ id: folder_id });
            base.SmartBlocks.startLoading("Loading folder");
            base.folder.fetch({
                success: function () {

                    var files_template = _.template(FileListTemplate, { files: base.folder.get("subfiles").models, _s: _s });
                    base.$el.find(".browser").html(files_template);
                    base.$el.find(".address").html(base.folder.get("address"));
                    base.SmartBlocks.stopLoading();
                }
            });

        },
        render: function () {
            var base = this;
            var template = _.template(SaveWindowTemplate, {});
            base.$el.html(template);
            base.initializeEvents();

        },
        saveFile: function () {
            var base = this;

            if (base.name != "") {
                base.SmartBlocks.startLoading("Exporting image to file");
                var file = new File();
                file.set("name", base.name);
                file.set("data", base.data);
                if (base.extension !== undefined)
                    file.set("extension", base.extension);
                file.set("parent_folder", base.folder);
                file.save({}, {
                    success: function () {
                        base.$el.remove();
                        base.SmartBlocks.show_message("Schema was succesfully exported.");
                        base.SmartBlocks.stopLoading();
                        base.SmartBlocks.FileSharingApp.refresh();
                    }
                });
            } else {
                var errors = base.$el.find(".error_messages");
                errors.html("The name cannot be blank");
                errors.show();
            }
        },
        initializeEvents: function () {
            var base = this;

            base.$el.delegate(".k_fs_folder_tb", "click", function () {
                var elt = $(this);
                base.loadFolder(elt.attr('data-file_id'));
            });

            base.$el.delegate(".browser_button", "click", function () {
                var elt = $(this);
                var action = elt.attr("data-action");

                if (action == "up") {
                    if (base.folder.get("id") != null) {
                        base.loadFolder(base.folder.get("parent_folder").get("id"));
                    }
                }

            });

            base.$el.delegate(".file_to_save_name", "keyup", function () {
                base.name = $(this).val();
                if (base.name != "") {
                    base.$el.find(".error_messages").hide();
                }
            });

            base.$el.delegate(".control_button", "click", function () {
                var elt = $(this);
                var action = elt.attr("data-action");

                if (action == "save") {
                    base.saveFile();
                }

                if (action == "cancel") {
                    base.hide();
                }
            });

            base.loadFolder(0);
        },
        show: function () {
            var base = this;
            $("body").prepend(base.$el);
        },
        hide: function () {
            var base = this;
            base.$el.remove();
        }
    });

    return SaveWindowView;
});