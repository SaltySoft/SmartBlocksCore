define([
    'jquery',
    'underscore',
    'backbone',
    'SmartBlocks',
    'Apps/Chat/Models/Discussion',
    'Apps/Chat/Models/Message',
    'text!Apps/Chat/Templates/discussion_view.html',
    'text!Apps/Chat/Templates/message_list.html',
    'text!Apps/Chat/Templates/discussions_list.html',
    'Apps/Chat/Collections/Discussions',
    'text!Apps/Chat/Templates/discussion_properties.html',
    'ContextMenuView'
], function ($, _, Backbone, SmartBlocks, Discussion, Message, DiscussionTemplate, MessageListTemplate, DiscussionsListTemplate, DiscussionsCollection, DiscussionPropertiesTemplate, ContextMenuView) {
    var DiscussionView = Backbone.View.extend({
        tagName: "div",
        className: "k_discussion_view",
        events: {
            "click .k_chat_send_message_button": "sendMessage"
        },
        initialize: function () {
            var base = this;
            base.old_title = $(document).attr('title');
        },
        init: function (SmartBlocks, ChatApplication, AppEvents, current_user, websocket, callback) {
            var base = this;
            base.app = ChatApplication;
            base.SmartBlocks = SmartBlocks;
            this.current_user = current_user;
            this.AppEvents = AppEvents;
            this.websocket = websocket;
            base.new_messages = 0;
            base.app.discussions_notifs[base.model.get('id')] = 0;
            base.updateDiscussionNotif();

            base.unnotify();
            SmartBlocks.events.on("ws_notification", function (message) {
//                var message = SmartBlocks.parseWs(data);
                if (message.app == "k_chat") {
                    if (message.status == "new_message") {

                        if (message.sender.id != base.app.current_user.get('id')) {
                            base.notify(message.discussion.id);
                        }
                        if (message.discussion.id == base.model.get('id')) {
                            base.getMessage();
                        }
                    }
                    if (message.status == "new_discussion")
                        base.getDiscussions();
                    if (message.status == "deleted_discussion") {
                        base.getDiscussions();
                        SmartBlocks.stopLoading();
                    }
                }
            });


            base.discussions = new DiscussionsCollection();


            base.render(callback);

        },
        getDiscussions: function (callback) {
            var base = this;

            base.discussions.fetch({
                data: {
                    user_id: base.app.current_user.get('id')
                },
                success: function () {
                    console.log(base.discussions.models);
                    console.log(unload_disc);
                    var unload_disc = true;
                    for (var d in base.discussions.models) {
                        if (base.discussions.models[d].get('id') == base.model.get('id')) {
                            unload_disc = false;
                        }

                    }
                    if (unload_disc) {
                        base.$el.find(".k_chat_messages_list").html("");
                    }
                    base.renderDiscussionList();



                }
            });
        },
        renderDiscussionList: function () {
            var base = this;
            var template = _.template(DiscussionsListTemplate, {
                current_user: base.current_user,
                available_discussions: base.discussions.models
            });

            base.$el.find(".k_chat_discussion_description").html(template);
            base.$el.find(".k_chat_discussion_deletion_button").click(function () {
                var elt = $(this);
                var discussion = new Discussion({ id: elt.attr("data-discussion_id") });
                if (confirm("Are you sure you want to delete this conversation ?")) {
                    SmartBlocks.startLoading("Deleting discussion");
                    discussion.destroy();
                }
            });

            base.$el.find(".k_chat_discussion_description").attr("oncontextmenu", "return false;");
            var context_menu = new ContextMenuView();
            context_menu.addButton("Discussion properties", function () {
                alert("Show discussion properties");
            }, '/images/icons/door.png');

            context_menu.addButton("Add people", function () {
                alert("Add people to discussion");
            }, '/images/icons/add.png');

            base.$el.find(".k_chat_discussion_selector_link").mousedown(function (event) {
                event.preventDefault();
                event.stopPropagation();
                switch (event.which) {
                    case 1:

                        break;
                    case 2:

                        break;
                    case 3:
                        context_menu.show(event);
                        return false;
                        break;
                    default:
                        alert('You have a strange mouse');
                }
            });
            base.$el.find(".k_chat_discussion_selector_link").click(function (event) {
                event.preventDefault();
                event.stopPropagation();
                switch (event.which) {
                    case 1:
                        $(".k_chat_messages_list").html('<div style="text-align: center; margin-top: 200px; color: white"><img src="/images/loader.gif" /><br/>Loading...</div>');
                        var elt = $(this);
                        var id = elt.attr("data-id");
                        base.app.discussion_list_object = base.$el.find(".k_chat_discussion_description").html();
                        base.app.show_discussion(id);
                        break;
                    case 2:
                        alert('Middle mouse button pressed');
                        break;
                    case 3:
                        alert('Right mouse button pressed');
                        return false;
                        break;
                    default:
                        alert('You have a strange mouse');
                }
            });


            base.$el.find(".k_chat_discussion_unsubscribe").click(function () {
                var elt = $(this);
                if (confirm("Are you sure you want to leave this conversation ?")) {
                    $.ajax({
                        url: "/Discussions/unsubscribe",
                        type: "post",
                        data: {
                            "discussion_id": elt.attr("data-discussion_id"),
                            "user_id": base.app.current_user.get('id')
                        },
                        success: function () {
                            base.getDiscussions();
                        }
                    });
                }
            });
            //Notification
            base.$el.find(".chat_discussion_notif").each(function () {
                var elt = $(this);
                var id = elt.attr("data-discussion_id");
                elt.html("New");
                if (base.discussions.get(id).get("notify"))
                    elt.show();
                else
                    elt.hide();
            });
        },
        render: function (callback) {
            var base = this;
            this.$el.html("Discussion");

            base.model.fetch({
                success: function () {

                    var template = _.template(DiscussionTemplate, {
                        discussion: base.model,
                        current_user: base.current_user,
                        available_discussions: base.discussions.models
                    });
                    base.$el.html(template);
                    base.$el.find(".k_chat_discussion_description").html(base.app.discussion_list_object);

                    base.initializeEvents();
                    if (callback)
                        callback();

                    if (base.model.get('id') == 0) {
                        $(".k_chat_send_message_input").attr("disabled", true);
                        $(".k_chat_send_message_button").attr("disabled", true);
                        base.$el.find(".k_chat_messages_list").html('<div style="color: white; width: 200px; margin: auto; text-align: center; margin-top: 50px;">Select a discussion on the left.</div>');
                    }
                    else {
                        base.getMessage();
                    }
                    base.getDiscussions(callback);
                }
            });


            return this;
        },
        updateDiscussionNotif: function () {
            var base = this;

        },
        initializeEvents: function () {
            var base = this;
            $("body").click(function () {
                SmartBlocks.setTitle(SmartBlocks.original_title);
            });


            base.$el.find(".k_chat_send_message_input").keydown(function (e) {

                var code = (e.keyCode ? e.keyCode : e.which);
                if (code == 13) { //Enter keycode
                    base.sendMessage();
                    base.$el.find(".k_chat_send_message_input").val("");
                    return false;
                }

            });
            base.$el.find(".close_window_link").click(function () {
                base.app.$el.hide();
                base.app.shown = false;
            });
            base.$el.find(".k_chat_discussion_creation_button").click(function () {
                base.app.discussion_creation_view.show();
            });


        },
        getMessage: function () {
            var base = this;

            base.model.fetch({
                success: function () {
                    var template = _.template(MessageListTemplate, { discussion: base.model, current_user: base.current_user });
                    base.$el.find(".k_chat_messages_list").html(template);
                    base.$el.find(".k_chat_messages_list").scrollTop(base.$el.find(".k_chat_messages_list")[0].scrollHeight);
                    if (base.$el.is(":visible")) {
                        base.unnotify();
                    }
                    SmartBlocks.chatNotif(base.app.current_user.get('id'));

                }
            });
        },
        unnotify: function () {
            var base = this;
            $.ajax({
                url: "/Discussions/unnotify",
                type: "post",
                data: {
                    "discussion_id": base.model.get('id')
                },
                success: function (response) {
                    //Notification
                    base.$el.find(".chat_discussion_notif").each(function () {
                        var elt = $(this);
                        var id = elt.attr("data-discussion_id");
                        if (id == base.model.get('id'))
                            elt.hide();
                    });
                }
            });
        },
        notify: function (discussion_id) {
            var base = this;
            base.$el.find(".chat_discussion_notif").each(function () {
                var elt = $(this);
                var id = elt.attr("data-discussion_id");
                if (id == discussion_id)
                    elt.show();
            });
        },
        sendMessage: function () {
            var base = this;
            var message_val = base.$el.find(".k_chat_send_message_input").val();

            if (message_val != "") {
                var message = new Message({
                    content: message_val,
                    discussion_id: base.model.get('id')
                });
                message.save({}, {
                    success: function () {

                        base.$el.find(".k_chat_send_message_input").val("");
                    }
                });
            }

        }
    });

    return DiscussionView;
});