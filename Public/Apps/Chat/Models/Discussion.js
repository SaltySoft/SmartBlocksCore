define([
    'underscore',
    'backbone',
    'Apps/Chat/Models/Message',
    'UserModel'
], function (_, Backbone, Message, User) {
    var Discussion = Backbone.Model.extend({
        urlRoot: "/Discussions",
        defaults: {
        },
        parse : function (response, option) {

            var messages = response.messages;
            var messages_array = new Array();
            for (var key in messages)
            {
                var message = new Message(messages[key]);
                messages_array.push(message);
            }
            response.messages = messages_array;

            var participants = response.participants;
            var participants_array = new Array();
            for (var key in participants)
            {
                var participant = new User(participants[key]);

                participants_array.push(participant);
            }
            response.participants = participants_array;

            return response;
        }
    });

    return Discussion;
});
