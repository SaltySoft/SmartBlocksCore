define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/Socials/Views/MainView'
], function ($, _, Backbone, MainView) {
    var initialize = function (SmartBlocks) {
        var view = new MainView();
        $("#app_container").html(view.$el);
        view.init(SmartBlocks);
    };
    return {
        initialize: initialize
    };
});