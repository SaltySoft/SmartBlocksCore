define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/NotificationsCenter/Views/main'
], function ($, _, Backbone, MainView) {
    var initialize = function (SmartBlocks) {
        var main_view = new MainView();
        $("#notification_center_container").html(main_view.$el);
        main_view.init(SmartBlocks);
    };

    return  {
        initialize: initialize
    };
});