define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/libs/chardin/chardinjs.min'
], function ($, _, Backbone) {
    var main = {
        init: function () {
            var base = this;

            SmartBlocks.Shortcuts.add([
                17, 112
            ], function () {
                base.showHelp();
            });
        },
        showHelp: function () {
            $('body').chardinJs('start');
        }
    };
    return main;
});