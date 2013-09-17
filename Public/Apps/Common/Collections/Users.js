define([
    'underscore',
    'backbone',
    'UserModel'
], function (_, Backbone, User) {
    var UsersCollection = Backbone.Collection.extend({
        model: User,
        url: "/Users"
    });

    return UsersCollection;
});