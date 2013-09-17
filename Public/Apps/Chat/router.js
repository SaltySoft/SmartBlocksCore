define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/Chat/Views/MainView',
    'SmartBlocks',
    'TabView',
    'Apps/Chat/Models/Discussion',
    'Apps/Chat/Views/HomeView',
    'Apps/Chat/Views/DiscussionView',
    'Apps/Chat/Views/DiscussionCreationView',
    'UserModel'
], function ($, _, Backbone, MainView, SmartBlocks, TabView, Discussion, HomeView, DiscussionView, DiscussionCreationView, User) {

        var initialize = function (SmartBlocks, websocket) {
            var base = this;
            base.SmartBlocks = SmartBlocks;

            var chat_view = new MainView();
            chat_view.init(SmartBlocks);

//            var contact_list = new ContactList();
//            contact_list.init(SmartBlocks);


//            var ChatApplication = {
//                current_user: base.SmartBlocks.current_user,
//                shown: false
//            };
//            ChatApplication.discussions_notifs = {};
//            var discussion_creation_view = new DiscussionCreationView();
//            discussion_creation_view.init(ChatApplication);
//
//            ChatApplication.discussion_creation_view = discussion_creation_view;
//
//            var container = $(document.createElement("div"));
//            container.addClass("k_chat_main_container");
//            ChatApplication.$el = $("#chat_container");
//            $("#chat_container").append(container);
//            $("#chat_button").click(function () {
//                $("#chat_container").toggle();
//                if ($("#chat_container").css("display") == "block") {
//                    $("#file_sharing_container").hide();
//                    $(container).css("left", $(window).width() / 2 - container.width() / 2);
//                }
//            });
//            var pressed_keys = [];
//            $(document).keyup(function (e) {
//                if ($("#chat_container").css("display") == "block")
//                    if (e.keyCode == 27) {
//                        $("#chat_container").hide();
//                        ChatApplication.shown = false;
//                    }
//                if (pressed_keys[39] && pressed_keys[17] && pressed_keys[16]) {
//                    $("#chat_container").show();
//                    $(container).css("left", $(window).width() / 2 - container.width() / 2);
//                }
//                pressed_keys = [];
//
//            });
//
//            SmartBlocks.original_title = $(document).attr("title");
//            SmartBlocks.events.on("ws_notification", function (message) {
//                if (message.app == "k_chat") {
//                    if (message.status == "new_message") {
//                        if (message.sender.id != base.SmartBlocks.current_user.get('id')) {
//                            SmartBlocks.notifySound();
//                            SmartBlocks.animateTitle('New message from ' + message.sender.username + ' in ' + message.discussion.name);
//                            SmartBlocks.chatNotif(base.SmartBlocks.current_user.get('id'));
//                        }
//                    }
//                }
//            });
//
//            $(document).keydown(function (e) {
//
//                pressed_keys[e.keyCode] = true;
//
//            });
//
//            $(window).resize(function () {
//                if ($("#chat_container").css("display") == "block")
//                    $(container).css("left", $(window).width() / 2 - container.width() / 2);
//            });
//
//
//            var AppEvents = _.extend({}, Backbone.Events);
//            ChatApplication.AppEvents = AppEvents;
//
//            var discussion_container = $(document.createElement("div"));
//            $(container).html(discussion_container);
//            ChatApplication.show_discussion = function (id) {
//                ChatApplication.shown = true;
//                var discussion = new Discussion({ id: id });
//                var discussionView = new DiscussionView({ model: discussion });
//                discussionView.init(base.SmartBlocks, ChatApplication, AppEvents, base.SmartBlocks.current_user, websocket, function () {
//                    discussion_container.html(discussionView.$el);
//                });
//            };
//            ChatApplication.show_discussion(0);
//
//
//
//            SmartBlocks.chatNotif(base.SmartBlocks.current_user.get('id'));

        };


        return {
            initialize: initialize
        };

    }
)
;