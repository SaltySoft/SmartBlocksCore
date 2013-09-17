define([
    'jquery',
    'underscore',
    'backbone',
    'TabView',
    'UserModel',
    'Apps/AppOrganizer/Views/AoHome',
    'Apps/AppOrganizer/Views/Dashboard'
], function ($, _, Backbone, TabView, User, AoHomeView, DashboardView) {

    var AppRouter = Backbone.Router.extend({
        routes:{
            'show_dashboard':'dashboard',
            'show_app':'showApp',
            '':"home"
        }
    });

    var initialize = function () {
        //Init the events and the router
        var AppEvents = _.extend({}, Backbone.Events);
        var app_router = new AppRouter();

        //Append a tabView to app_container
//        var app_view = new TabView();
//        app_view.init(AppEvents);
//        $("#app_container").html(app_view.$el);
//        $("#inner_container").addClass("backgroundThem1");

        //Home view: 1
//        var home_view = new AoHomeView();
//        home_view.init(AppEvents);
//        app_view.addTab("App organizer", home_view.$el, "home");
//
//        app_router.on("route:home", function () {
//            app_view.show(1);
//        });

        //Dashboard view: 1
        var dashboard = new DashboardView();
        dashboard.init(AppEvents);
//        app_view.addTab("Dashboard", dashboard.$el, "show_dashboard");
        $("#app_container").html(dashboard.$el);
//        app_router.on('route:dashboard', function () {
//            app_view.show(1);
//        });
//        app_router.on('route:home', function () {
//            app_view.show(1);
//        });
        Backbone.history.start();
    };

    return {
        initialize:initialize
    };
});