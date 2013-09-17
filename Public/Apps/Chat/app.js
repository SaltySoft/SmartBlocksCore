define([
    'jquery',
    'underscore',
    'backbone',
    'SmartBlocks',
    'Apps/Chat/router'
], function ($, _, Backbone, SmartBlocks, Router) {
    var initialize = function (websocket) {

        Router.initialize(SmartBlocks, websocket);

    };
    return {
        initialize:initialize
    };
});