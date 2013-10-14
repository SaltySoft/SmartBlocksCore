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
            if (!base.get("restricted_to") || SmartBlocks.current_user.hasRight(base.get("restricted_to"))) {
                SmartBlocks.Methods.startMainLoading("Loading " + base.get('name'), 2);

                if (base.get("entry_point")) {
                    base.ready = false;
                    var block = SmartBlocks.Data.blocks.get(base.get("block_token"));
                    var main = SmartBlocks.Blocks[block.get('name')].Main;
                    console.log(base.get("block_token"), block, main);
                    if (main[base.get("entry_point")]) {
                        main[base.get("entry_point")](base);
                        base.events.trigger("ready");
                        SmartBlocks.current_app = base;
                        base.routeit();
                        console.log(base);
                    }
                    base.ready = true;



//                    require([base.get("entry_point")], function (View) {
//                        SmartBlocks.Methods.continueMainLoading(2);
//                        var view = new View();
//                        SmartBlocks.Methods.render(view);
//                        view.init(base);
//                        base.ready = true;
//                        base.events.trigger("ready");
//                        SmartBlocks.current_app = base;
//                        base.routeit();
//                    });
                }
            } else {
                SmartBlocks.basics.show_message("You don't have the rights to access this app");
                SmartBlocks.router.back();
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
            if (!SmartBlocks.current_app || SmartBlocks.current_app.get("token") == base.get("token")) {
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