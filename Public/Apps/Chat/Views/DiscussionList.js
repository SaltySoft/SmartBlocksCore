define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/Chat/Models/Discussion',
    'Apps/Chat/Views/Discussion',
    'text!Apps/Chat/Templates/message_template.html',
    "amplify"
], function ($, _, Backbone, Discussion, DiscussionView) {
    var DiscussionListView = Backbone.View.extend({
        tagName: "ul",
        className: "chat_discussion_list",
        initialize: function () {
            var base = this;
        },
        init: function (SmartBlocks, main_view) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.discussion_views = [];

            base.i = 0;
            base.main_view = main_view;

        },
        render: function (view) {
            var base = this;

            view.init(base.SmartBlocks, base.main_view);

            base.$el.append(view.$el);
        },
        createDiscussion: function (users) {
            var base = this;
            users.push(base.SmartBlocks.current_user);
            var discussion = new Discussion({
                participants: users
            });
            discussion.save({}, {
                success: function () {
                    base.main_view.discussions.add(discussion);
                    base.addDiscussion(discussion);
                }
            });
        },
        addDiscussion: function (discussion) {
            var base = this;
            if (discussion.messages) {
                var discussion_view = new DiscussionView({
                    model: discussion
                });
                base.render(discussion_view);
            } else {
                discussion.fetch({
                    success: function () {
                        var discussion_view = new DiscussionView({
                            model: discussion
                        });

                        base.render(discussion_view);
                    }
                });
            }
            base.main_view.discussions.add(discussion);
            amplify.store("discussions", base.main_view.discussions);
        },
        registerEvents: function () {
            var base = this;


        }
    });

    return DiscussionListView;
});