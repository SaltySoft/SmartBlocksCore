define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/Chat/Templates/main_view.html',
    'Apps/Chat/Views/ContactList',
    'Apps/Chat/Views/DiscussionList',
    'Apps/Chat/Collections/Discussions',
    'Apps/Chat/Models/Discussion',
    "amplify"
], function ($, _, Backbone, MainViewTemplate, ContactListView, DiscussionListView, DiscussionsCollection, Discussion) {
    var MainView = Backbone.View.extend({
        tagName: "div",
        className: "chat_main_view",
        initialize: function () {
            var base = this;
            base.events = $.extend({}, Backbone.Events);
            base.discussions = new DiscussionsCollection();
        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;

            $("body").append(base.$el);

            base.render();
            base.registerEvents();
            var discussions = amplify.store("discussions");
            for (var k in discussions) {
                var discussion = new Discussion({id: discussions[k].id });
                discussion.fetch({
                    success: function () {
                        base.discussion_list.addDiscussion(discussion);
                    }
                });
            }


        },
        render: function () {
            var base = this;

            var template = _.template(MainViewTemplate, {});
            base.$el.html(template);

            base.discussion_list = new DiscussionListView();
            base.$el.find(".discussions_container").html(base.discussion_list.$el);
            base.discussion_list.init(base.SmartBlocks, base);

            var contact_list = new ContactListView();
            base.$el.find(".chat_contact_list_container").html(contact_list.$el);
            contact_list.init(base.SmartBlocks, base);
            var opened_discussions = amplify.store("opened_discussions");
            for (var k in opened_discussions) {
                var discussion = new Discussion({
                    id: opened_discussions[k]
                });
                discussion.fetch({
                    success: function () {
                        base.discussion_list.addDiscussion(discussion, false);
                    }
                });
            }
        },
        registerEvents: function () {
            var base = this;
            base.SmartBlocks.events.on("ws_notification", function (message) {
                if (message.app == "chat") {
                    if (message.content) {
                        if (base.discussions.get(message.discussion_id) !== undefined) {
                            base.events.trigger("message", message);
                        } else {
                            var discussion = new Discussion({id: message.discussion_id });
                            discussion.fetch({
                                success: function () {
                                    base.discussion_list.addDiscussion(discussion);
                                    base.events.trigger("message", message);
                                }
                            });
                        }
                    }
                }
            });
        }
    });


    return MainView;
});