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
                base.ready = false;

                require([base.get("entry_point")], function (View) {
                    var view = new View();
                    SmartBlocks.Methods.render(view);
                    view.init(base);
                    base.ready = true;
                    base.events.trigger("ready");
                });
            }
        },
        quit: function () {
            var base = this;
            window.location = "#";
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
            if (SmartBlocks.current_app.get("token") == base.get("token")) {
                base.routeit();
            }
            base.current_url = SmartBlocks.Url.full;
        },
        routeit: function () {
            var base = this;

            var app_routes = base.get("routing");
            if (SmartBlocks.Url.params.length == 0) {
                if (base.routes) {
                    base.routes[base.get("routing")["-"]].apply(base, parameters);
                }
                return;
            }
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
        },
        getBlock: function () {
            var base = this;
            var block = SmartBlocks.Data.blocks.get(base.get("block_token"));
            return block;
        }
    });

    return Application;
});