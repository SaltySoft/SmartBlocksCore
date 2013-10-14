define([
    'underscore',
    'backbone',
    'Apps/Chat/Models/Discussion'
], function (_, Backbone, Message) {
    var MessagesCollection = Backbone.Collection.extend({
        model: Message,
        url: "/Message",
        parse: function (response, options) {
            console.log(response);

            return response.results;
        }
    });

    return MessagesCollection;
});