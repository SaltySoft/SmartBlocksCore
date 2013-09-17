define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/FileSharing/Templates/file_properties.html'
], function ($, _, Backbone, FolderPropertiesTemplate) {
    var FilePropertiesView = Backbone.View.extend({
        tagName: "div",
        className: "k_fs_file_properties",
        initialize: function () {

        },
        init: function (SmartBlocks, folder_browser) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.folder_browser = folder_browser;
            base.model.fetch({
                success: function () {
                    base.render();
                }
            });
        },
        render: function () {
            var base = this;
            base.$el.addClass("cache");
            var template = _.template(FolderPropertiesTemplate, { folder: base.model });
            var container = $(document.createElement("div"));
            container.addClass("popup");
            base.$el.append(container);

            container.html(template);
            base.initializeEvents();
        },
        initializeEvents: function () {
            var base = this;
            base.$el.find(".k_fs_folder_properties_validation_button").click(function () {
                base.save();
            });
            base.$el.find(".k_fs_folder_properties_cancel_button").click(function () {
                base.cancel();
            });
        },
        save: function () {
            var base = this;
            var array = base.$el.find(".k_fs_folder_properties_form").serializeArray();
            var ob_lit = {};
            for (k in array) {
                ob_lit[array[k].name]  = array[k].value
            }
            base.model.set(ob_lit);
            console.log(base.model);
            base.model.save({}, {
                success: function () {
                    base.$el.remove();
                    base.folder_browser.reload();
                },
                error: function (data, status) {
                }
            });
        },
        cancel: function () {
            var base = this;
            base.$el.remove();
        }
    });

    return FilePropertiesView;
});