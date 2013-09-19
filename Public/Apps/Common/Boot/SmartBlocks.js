define([
    'underscore',
    'backbone',
    'SmartBlocks',
    'UserModel',
    "LoadingScreen",
    "UsersCollection",
    "Apps/UserRequester/app"
], function (_, Backbone, sb_basics, User, LoadingScreen, UsersCollection, UserRequester) {
    var SmartBlocks = {
        Url: {
            params: []
        },
        basics: sb_basics,
        events: null,
        current_session: null,
        current_user: null,
        router: null,
        init: function () {
            if ("WebSocket" in window) {
                var websocket = new WebSocket(socket_server, "muffin-protocol");
                SmartBlocks.basics.websocket = websocket;
            }
            SmartBlocks.events = _.extend({}, Backbone.Events);
            SmartBlocks.basics.server_handshake(websocket, user_session);
            SmartBlocks.current_session = user_session;

            if (websocket !== undefined) {
                websocket.onmessage = function (data) {
                    var message = SmartBlocks.basics.parseWs(data);
                    SmartBlocks.events.trigger("ws_notification", message);
                };
            }

            SmartBlocks.basics.Router = Backbone.Router.extend({
                routes: {
                    "": "entry",
                    ":appname": "launch_app",
                    ":appname/:params": "launch_app"
                },
                initialize: function () {
                    this.route(/^([a-zA-Z]*?)\/(.*?)$/, "launch_app", this.launch_app);
                },
                entry: function () {
                    SmartBlocks.Methods.entry();
                },
                launch_app: function (appname, params) {

                    SmartBlocks.Url.params = params ? params.split("/") : [];
                    var app = SmartBlocks.Data.apps.where({
                        token: appname
                    })[0];
                    if (app && (!SmartBlocks.current_app || SmartBlocks.current_app.get("id") != app.get("id"))) {
                        app = SmartBlocks.Data.apps.get(app.get('id'));
                        SmartBlocks.Methods.setApp(app);
                    }
                }

            });

            SmartBlocks.router = new SmartBlocks.basics.Router();


            SmartBlocks.Config = {};
            SmartBlocks.Config.entry_app = {
                block: 'kernel',
                app: 'app_organizer'
            };
            SmartBlocks.basics.init_solution();

            SmartBlocks.Data.blocks = new SmartBlocks.Collections.Blocks();
            SmartBlocks.Data.apps = new SmartBlocks.Collections.Applications();
            $(document).keyup(function (e) {
                if (e.keyCode == 27) {
                    SmartBlocks.current_app.quit();
                }
            });

            SmartBlocks.loading_screen = new LoadingScreen();
            SmartBlocks.Methods.render(SmartBlocks.loading_screen);
            SmartBlocks.loading_screen.init();

            User.getCurrent(function (current_user) {
                SmartBlocks.basics.connected_users = new UsersCollection();

                var timers = [];
                SmartBlocks.current_user = current_user;
                UserRequester.initialize(SmartBlocks.basics);


                SmartBlocks.loading_screen.setText("Loading data");
                SmartBlocks.loading_screen.setLoad(0);
                SmartBlocks.Data.apps.fetch({
                    success: function () {
                        SmartBlocks.Data.blocks.fetch({
                            success: function () {
                                var blocks = SmartBlocks.Data.blocks;
                                SmartBlocks.Methods.count = 0;
                                for (var k in blocks.models) {
                                    var block = blocks.models[k];
                                    var types = block.get("types");
                                    SmartBlocks.Methods.count += types != null ? types.length : 0;
                                }
                                SmartBlocks.loading_screen.setMax(SmartBlocks.Methods.count);
                                SmartBlocks.Methods.processed = 0;
                                for (var k in blocks.models) {
                                    var block = blocks.models[k];
                                    var types = block.get("types");
                                    SmartBlocks.Blocks = {};
                                    SmartBlocks.Blocks[block.get("name")] = {
                                        Models: {},
                                        Collections: {},
                                        Data: {}
                                    };
                                    for (var t in types) {
                                        var type = types[t];
                                        SmartBlocks.Methods.addType(type, block);
                                    }
                                }

                                //Hearbeats. If I'm living, my heart beats.
                                SmartBlocks.events.on("ws_notification", function (message) {
                                    if (message.app == "heartbeat") {
                                        SmartBlocks.basics.connected_users.push(message.user);
                                        clearTimeout(timers[message.user.id]);
                                        timers[message.user.id] = setTimeout(function () {
                                            SmartBlocks.basics.connected_users.remove(message.user);
                                        }, 10000);
                                    }
                                });

                                SmartBlocks.basics.connected_users.on("add", function () {
                                    SmartBlocks.basics.connected_users.trigger("change");
                                });

                                SmartBlocks.basics.connected_users.on("remove", function () {
                                    SmartBlocks.basics.connected_users.trigger("change");
                                });

                                setInterval(function () {
                                    //SmartBlocks.heartBeat(current_user);
                                }, 5000);

                            }
                        });
                    }
                });


            });


        },
        Methods: {
            render: function (view) {
                var base = this;
                $("#content").html(view.$el);
            },
            setApp: function (app) {
                var base = this;
                SmartBlocks.current_app = app;
                app.launch();
            },
            entry: function () {
                if (!SmartBlocks.entry_app) {
                    var block = SmartBlocks.Data.blocks.where({
                        token: SmartBlocks.Config.entry_app.block
                    })[0];
                    if (block) {
                        var apps = new SmartBlocks.Collections.Applications(block.get('apps'));
                        var app = apps.where({
                            token: SmartBlocks.Config.entry_app.app
                        })[0];
                        if (app) {
                            app = SmartBlocks.Data.apps.get(app.get('id'))
                            SmartBlocks.entry_app = app;
                        }
                    }
                }

                SmartBlocks.Methods.setApp(SmartBlocks.entry_app);
            },
            types: {
                count: 0,
                processed: 0
            },
            addType: function (type, block) {
                require([type.model_location, type.collection_location], function (model, collection) {
                    SmartBlocks.Blocks[block.get("name")].Models[type.model_name] = model;
                    SmartBlocks.Blocks[block.get("name")].Collections[type.collection_name] = collection;
                    SmartBlocks.Blocks[block.get("name")].Data[type.plural] = new collection();
                    SmartBlocks.Blocks[block.get("name")].Data[type.plural].fetch({
                        success: function () {
                            SmartBlocks.loading_screen.setLoad(SmartBlocks.Methods.processed + 1);
                            if (++SmartBlocks.Methods.processed >= SmartBlocks.Methods.count) {
                                //Done loading types
                                Backbone.history.start();
                                $(window).bind("hashchange", function () {
                                    SmartBlocks.events.trigger("hashchange");
                                });
                            }
                        }
                    });
                });
            }
        }
    };

    window.SmartBlocks = SmartBlocks;

    return SmartBlocks;
});