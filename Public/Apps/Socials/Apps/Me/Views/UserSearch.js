define([
    'jquery',
    'underscore',
    'backbone',
    'UsersCollection',
    'Apps/Socials/Views/ContactItem',
    'text!Apps/Socials/Apps/Me/Templates/user_search.html'
], function ($, _, Backbone, UsersCollection, ContactItem, UserSearchTemplate){
    var UserSearchView = Backbone.View.extend({
        tagName: "div",
        className: "user_search_view",
        initialize: function () {

        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;

            base.user_search_res = new UsersCollection();

            base.render();
            base.registerEvents();
        },
        render: function () {
            var base = this;

            var template = _.template(UserSearchTemplate, {});

            base.$el.html(template);
        },
        launchSearch: function (username) {
            var base = this;
            base.user_search_res.fetch({
                data: {
                    "filter": username
                },
                success: function (list) {
                    var elt = base.$el.find(".user_finder_results");

                    elt.html("");
                    for (var k in list.models) {
                        var user = list.models[k];

                        var contact_item = new ContactItem({
                            model: user
                        });
                        contact_item.init(base.SmartBlocks, true);
                        elt.append(contact_item.$el);
                    }
                }
            });
        },
        registerEvents: function () {
            var base = this;

            var search_timer = 0;
            base.$el.delegate(".user_finder_input", "keyup", function () {
                var elt = $(this);
                console.log("typed");
                clearTimeout(search_timer);

                search_timer = setTimeout(function () {
                    if (elt.val() != "") {
                        base.launchSearch(elt.val());
                    }
                }, 500);
            });
        }
    });

    return UserSearchView;
});