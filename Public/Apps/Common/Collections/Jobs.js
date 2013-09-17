define([
    'underscore',
    'backbone',
    'JobModel'
], function (_, Backbone, Job) {
    var JobsCollection = Backbone.Collection.extend({
        model: Job,
        url: "/Jobs"
    });

    return JobsCollection;
});