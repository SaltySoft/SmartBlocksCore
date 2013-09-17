define([
    'jquery',
    'underscore',
    'backbone',
    'SmartBlocks',
    'Apps/NotificationsCenter/launcher'
], function ($, _, Backbone, SmartBlocks, Router) {
    var initialize = function (websocket) {

        Router.initialize(SmartBlocks, websocket);

    };
    return {
        initialize:initialize
    };
});