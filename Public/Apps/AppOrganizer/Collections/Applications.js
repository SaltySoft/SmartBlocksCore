define([
    "underscore",
    "backbone",
    "Applications/AppOrganizer/Models/Application"
], function (_, Backbone, Application) {
    var ApplicationsCollection = Backbone.Collection.extend({
        url: "/Applications",
        model: Application
    });

    return ApplicationsCollection;
});