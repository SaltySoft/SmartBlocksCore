define([
    'jquery',
    'underscore',
    'backbone'
], function ($, _, Backbone) {
    var Application = Backbone.Model.extend({
        idAttribute: 'token',
        defaults: {
        },
        initialize: function () {
            var base = this;
            base.events = $.extend({}, Backbone.Events);

        },
        launch: function () {
            var base = this;
            if (base.get("entry_point")) {

                require([base.get("entry_point")], function (View) {
                    var view = new View();
                    SmartBlocks.Methods.render(view);
                    view.init(base);
                    base.route();
                });
            }
        },
        quit: function () {
            var base = this;
            SmartBlocks.Methods.entry();
        },
        initRoutes: function (obj) {
            var base = this;
            var reload = !base.routes;
            base.routes = obj;
            if (reload) {
                base.routeit();
            }
        },
        route: function () {
            var base = this;
            if (SmartBlocks.current_app.get("token") == base.get("token") && SmartBlocks.Url.full != base.current_url) {
                base.routeit();
            }
            base.current_url = SmartBlocks.Url.full;
        },
        routeit: function () {
            var base = this;

            var app_routes = base.get("routing");
            for (var k in app_routes) {
                var route = app_routes[k];
                var do_it = true;
                var parameters = [];
                var route_params = k.split("/");
                for (var j in SmartBlocks.Url.params) {
                    var param = SmartBlocks.Url.params[j];
                    if (route_params[j]) {
                        if (route_params[j][0] == ":") {
                            parameters.push(param);
                        } else {
                            if (param != route_params[j]) {
                                do_it = false;
                            }
                        }
                    } else {
                        do_it = false;
                    }
                }
                if (do_it) {
                    console.log("route string ", route_params[j], " now calling ", route);
                    if (base.routes)
                        base.routes[route].apply(base, parameters);
                    break;
                }
            }
        }
    });

    return Application;
});