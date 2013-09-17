define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/Chat/Templates/discussion.html',
    'Apps/Chat/Models/Message',
    'text!Apps/Chat/Templates/message_template.html'
], function ($, _, Backbone, DiscussionTemplate, Message, MessageTemplate) {
    var DiscussionView = Backbone.View.extend({
        tagName: "li",
        className: "chat_discussion_view",
        initialize: function () {
            var base = this;
            base.discussion = base.model;
            base.messages = [];
        },
        init: function (SmartBlocks, main_view) {
            var base = this;
            base.SmartBlocks = SmartBlocks;

            base.main_view = main_view;

            base.render();
            base.registerEvents();
            var message_list = base.discussion.get("messages");
            for (var k in message_list) {
                var message = message_list[k];

                var last_message = base.messages[base.messages.length - 1];
                var show_sender = false;
                if (!last_message || last_message.sender.id != message.get("sender").id) {
                    show_sender = true;
                }
                var template = _.template(MessageTemplate, {
                    message: message,
                    sender: message.get("sender"),
                    time: new Date().getTime() / 1000,
                    show_sender: show_sender
                });
                base.messages.push(message.attributes);
                base.$el.find(".messages_list").append(template);
            }
        },
        render: function () {
            var base = this;

            var template = _.template(DiscussionTemplate, {});
            base.$el.html(template);

            var participants = base.discussion.get("participants");
            for (var k in participants) {
                if (base.SmartBlocks.current_user.get("id") != participants[k].id) {
                    base.$el.find(".participants").append(participants[k].get("username"));
                }
            }
        },
        registerEvents: function () {
            var base = this;

            base.$el.find(".discussion_button").click(function () {
                base.$el.find(".discussion").toggle();
            });

            base.$el.find(".remove_button").click(function () {
                console.log(base.main_view.discussions);
                base.main_view.discussions.remove(base.discussion);
                console.log(base.main_view.discussions);
                base.$el.remove();
            });

            base.$el.find(".discussion_input").keyup(function (e) {
                var elt = $(this);
                if (e.keyCode == 13) {
                    var part = [];
                    var part_list = base.discussion.get("participants");
                    for (var k in part_list) {
                        part.push(part_list[k].get("session_id"));
                    }

                    var message = new Message();
                    message.set("discussion_id", base.discussion.get("id"));
                    message.set("content", elt.val());
                    message.save();
                    base.SmartBlocks.sendWs("chat", {
                        content: elt.val(),
                        discussion_id: base.discussion.get('id'),
                        sender: base.SmartBlocks.current_user.attributes
                    }, part);
                    console.log(message);
                    elt.val("");
                }
            });

            base.main_view.events.on("message", function (message) {
                if (message.app == "chat") {
                    if (message.content) {
                        if (message.discussion_id == base.discussion.get('id')) {
                            var last_message = base.messages[base.messages.length - 1];
                            var show_sender = false;
                            if (!last_message || last_message.sender.id != message.sender.id) {
                                show_sender = true;
                            }
                            var template = _.template(MessageTemplate, {
                                message: message,
                                sender: message.sender,
                                show_sender: show_sender
                            });
                            base.messages.push(message);
                            base.$el.find(".messages_list").append(template);
                            var elt = base.$el.find(".messages_list_container");
                            elt.scrollTop(10000);
                        }
                    }
                }
            });
        }
    });

    return DiscussionView;
});