define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/FileSharing/router'
], function ($, _, Backbone, Router) {
    var initialize = function (SmartBlocks) {
        Router.initialize(SmartBlocks);
    };
    return {
        initialize: initialize,
        refresh: Router.refresh
    };
});