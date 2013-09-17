define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/AppOrganizer/router'
], function ($, _, Backbone, Router) {
    var initialize = function () {

        Router.initialize();
    };
    return {
        initialize:initialize
    };
});