define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/Chat/Templates/contact_item.html',
    'Apps/Chat/Collections/Discussions'
], function ($, _, Backbone, ContactItemTemplate, DiscussionsCollection) {
    var ContactItem = Backbone.View.extend({
        tagName: "li",
        className: "chat_contact_item",
        initialize: function () {
            var base = this;
            base.user = base.model;
        },
        init: function (SmartBlocks, contact_list) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.contact_list = contact_list;
            base.render();
            base.registerEvents();
        },
        render: function () {
            var base = this;

            var template = _.template(ContactItemTemplate, {
                user: base.user
            });

            base.$el.html(template);
        },
        registerEvents: function () {
            var base = this;

            base.$el.click(function () {
                var discussions  = new DiscussionsCollection();

                discussions.fetch({
                    data: {
                        user_id: base.SmartBlocks.current_user.get('id'),
                        user2: base.user.get('id')
                    },
                    success: function (discussions) {
                        if (discussions.models.length > 0) {
                            if (!base.contact_list.main_view.discussions.get(discussions.models[0]))
                                base.contact_list.main_view.discussion_list.addDiscussion(discussions.models[0]);
                        }
                        else {
                            base.contact_list.main_view.discussion_list.createDiscussion([
                                base.user
                            ]);
                        }
                    }
                });

            });
            if (base.SmartBlocks.connected_users.get(base.user.get('id'))) {
                base.$el.find(".status").addClass("online");

            } else {
                base.$el.find(".status").removeClass("online");
            }
            base.SmartBlocks.connected_users.on("change", function () {
                if (base.SmartBlocks.connected_users.get(base.user.get('id'))) {
                    base.$el.find(".status").addClass("online");
                } else {
                    base.$el.find(".status").removeClass("online");
                }

            });
        }
    });

    return ContactItem;
});