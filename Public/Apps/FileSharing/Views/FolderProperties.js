define([
    'jquery',
    'underscore',
    'backbone',
    'UsersCollection',
    'text!Apps/FileSharing/Templates/folder_properties.html',
    'text!SearchResultsTemplate',
    'text!SelectionTemplate'
], function ($, _, Backbone, UsersCollection, FolderPropertiesTemplate, SearchResultsTemplate, SelectionTemplate) {
    var FolderPropertiesView = Backbone.View.extend({
        tagName: "div",
        className: "k_fs_folder_properties",
        initialize: function () {

        },
        init: function (SmartBlocks, folder_browser) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.folder_browser = folder_browser;
            base.user_search_results = new UsersCollection();
            base.user_shared = new UsersCollection();

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
            var users = base.model.get('users_allowed');
            for (var k in users) {
                base.user_shared.add(users[k]);
            }
        },
        initializeEvents: function () {
            var base = this;
            base.$el.find(".k_fs_folder_properties_validation_button").click(function () {
                base.save();
            });
            base.$el.find(".k_fs_folder_properties_cancel_button").click(function () {
                base.cancel();
            });
            var usersearch_timer = 0;
            base.$el.find(".k_fs_foldp_user_search_input").keyup(function () {
                clearTimeout(usersearch_timer);
                usersearch_timer = setTimeout(function () {
                    base.searchForUsers();
                }, 200);
            });

            base.user_shared.on("add", function () {
                base.updateUserSelectionList();
            });
            base.user_shared.on("remove", function () {
                base.updateUserSelectionList();
            });
            base.updateUserSelectionList();
        },
        updateUserSelectionList: function () {
            var base = this;
            var template = _.template(SelectionTemplate, {type: "users", field: "username", selection: base.user_shared.models});
            base.$el.find(".k_fs_foldp_user_selection").html(template);
            base.$el.find(".k_fs_foldp_user_selection").find(".remove_from_selection").click(function () {
                var elt = $(this);
                base.user_shared.remove(elt.attr('data-id'));
            });
        },
        searchForUsers: function () {
            var base = this;
            base.user_search_results.fetch({
                data: {
                    filter: base.$el.find(".k_fs_foldp_user_search_input").val()
                },
                success: function () {
                    var template = _.template(SearchResultsTemplate, {type: "users", field: "username", results: base.user_search_results.models});
                    base.$el.find(".k_fs_foldp_user_search_results").html(template);
                    base.$el.find(".k_fs_foldp_user_search_results").find(".add_to_selection").click(function () {
                        var elt = $(this);
                        var model = base.user_search_results.get(elt.attr("data-id"));
                        base.user_shared.add(model);
                    });
                }
            });
        },

        save: function () {
            var base = this;

            base.model.set({
                name: base.$el.find(".k_fs_foldp_name").val(),
                users_allowed: base.user_shared.toArray()
            });
            console.log(base.user_shared.toArray());
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

    return FolderPropertiesView;
});