define([
    'underscore',
    'backbone',
    'UserModel'
], function (_, Backbone, User) {
    var ContactRequest = Backbone.Model.extend({
        urlRoot: "/ContactRequests",
        parse: function (response) {

            var sender_a = response.sender;
            var sender = new User(sender_a);
            response.sender = sender;

            var target_a = response.target;
            var target = new User(target_a);
            response.target = target;

            return response;
        }
    });

    return ContactRequest;
});