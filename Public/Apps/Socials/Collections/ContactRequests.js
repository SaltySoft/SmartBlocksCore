define([
    'underscore',
    'backbone',
    'Apps/Socials/Models/ContactRequest'
], function (_, Backbone, ContactRequest) {
    var ContactRequestsCollection = Backbone.Collection.extend({
        model: ContactRequest,
        url: "/ContactRequests"
    });

    return ContactRequestsCollection;
});