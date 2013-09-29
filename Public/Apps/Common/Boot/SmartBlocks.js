define([
    'jquery',
    'underscore',
    'backbone',
    'SmartBlocks',
    'UserModel',
    "LoadingScreen",
    "UsersCollection",
    "Apps/UserRequester/app"
], function ($, _, Backbone, sb_basics, User, LoadingScreen, UsersCollection, UserRequester) {

    var temp = {};

    var SmartBlocks = {
        Url: {
            params: []
        },
        basics: sb_basics,
        events: null,
        current_session: null,
        current_user: null,
        router: null,
        init: function (callback, production) {
            temp.callback = callback;

            if (production) {
                console.log = function () {
                };
            }

            SmartBlocks.events = _.extend({}, Backbone.Events);

            SmartBlocks.started = false;
            SmartBlocks.events.on("start_solution", function () {
                //Done loading everything, launching main app
                if (!SmartBlocks.started) {
                    SmartBlocks.Methods.start();
                    if (temp.callback) {
                        temp.callback();
                    }
                }
                SmartBlocks.started = true;
            });

            SmartBlocks.basics.Router = Backbone.Router.extend({
                routes: {
                    "": "entry",
                    ":appname": "launch_app",
                    ":appname/:params": "launch_app"
                },
                initialize: function () {
                    this.route(/^([a-zA-Z]*?)\/(.*?)$/, "launch_app", this.launch_app);
                    this.routesHit = 0;
                    Backbone.history.on('route', function () {
                        this.routesHit++;
                    }, this);
                },
                entry: function () {
                    SmartBlocks.Url.params = {};
                    SmartBlocks.Url.appname = "";
                    SmartBlocks.Url.full = "";
                    SmartBlocks.Methods.entry();
                },
                launch_app: function (appname, params) {

                    SmartBlocks.Url.params = params ? params.split("/") : [];
                    SmartBlocks.Url.appname = appname;
                    SmartBlocks.Url.full = appname + "/" + params;
                    var app = SmartBlocks.Data.apps.where({
                        token: appname
                    })[0];
                    if (app && (!SmartBlocks.current_app || SmartBlocks.current_app.get("token") != app.get("token"))) {
                        app = SmartBlocks.Data.apps.get(app.get('token'));
                        SmartBlocks.Methods.setApp(app);
                    }
                },
                back: function () {
                    if (this.routesHit > 1) {
                        window.history.back();
                    } else {
                        window.location = "#";
                    }
                }

            });

            SmartBlocks.Shortcuts = {
                initial_shortcuts: [
                ],
                init: function () {
                    var base = this;

                    base.shortcuts = $.extend([], base.initial_shortcuts);

                    base.keydown_list = {};

                    $(document).bind("keydown", function (e) {
                        if (e.keyCode != 123) { //123 == F12. This key blocks all other shortcuts when opening debug console.
                           base.keydown_list[e.keyCode] = true;

                            var active_keys = [];
                            for (var k in base.keydown_list) {
                                if (base.keydown_list[k]) {
                                    active_keys.push(k);
                                }
                            }
                            for (var k in base.shortcuts) {
                                var shortcut = base.shortcuts[k];
                                var checked_keys = 0;
                                for (var i in shortcut.keys) {
                                    var key = shortcut.keys[i];
                                    if (base.keydown_list[key]) {
                                        checked_keys += 1;
                                    }
                                }
                                if (checked_keys == active_keys.length && checked_keys == shortcut.keys.length) {
                                    if (!shortcut.url_mask || ("#" + SmartBlocks.Url.full).indexOf(shortcut.url_mask) == 0) {
                                        shortcut.action();
                                        e.preventDefault();
                                    }
                                }
                            }
                        }
                    });

                    $(document).bind("keyup", function (e) {
                        base.keydown_list[e.keyCode] = false;
                    });
                },
                add: function (list, callback, url_mask) {
                    var base = this;
                    base.shortcuts.push({
                        keys: list,
                        action: callback,
                        url_mask: url_mask
                    });
                },
                clear: function () {
                    var base = this;
                    base.shortcuts = $.extend({}, base.initial_shortcuts);
                }
            };

            SmartBlocks.Shortcuts.init();

            SmartBlocks.router = new SmartBlocks.basics.Router();


            SmartBlocks.basics.init_solution();

            SmartBlocks.Data.blocks = new SmartBlocks.Collections.Blocks();
            SmartBlocks.Data.apps = new SmartBlocks.Collections.Applications();


            SmartBlocks.Methods.startMainLoading("Loading apps", 8);

            User.getCurrent(function (current_user) {

                SmartBlocks.basics.connected_users = new UsersCollection();

                var timers = [];
                SmartBlocks.current_user = current_user;
                UserRequester.initialize(SmartBlocks.basics);
                SmartBlocks.Data.apps.fetch({
                    success: function () {
                        SmartBlocks.Methods.continueMainLoading(1, "Loading blocks");
                        SmartBlocks.Data.blocks.fetch({
                            success: function () {
                                SmartBlocks.Methods.continueMainLoading(1, "Loading config");
                                $.ajax({
                                    url: "/Configs/front_end_config",
                                    success: function (data, status) {
                                        SmartBlocks.Methods.continueMainLoading(1, "Loading data");
                                        SmartBlocks.Config = data;
                                        var blocks = SmartBlocks.Data.blocks;
                                        SmartBlocks.Methods.count = 0;
                                        for (var k in blocks.models) {
                                            var block = blocks.models[k];
                                            var types = block.get("types");
                                            SmartBlocks.Methods.count += types != null ? types.length : 0;
                                        }
                                        SmartBlocks.Methods.processed = 0;
                                        SmartBlocks.Blocks = {};
                                        for (var k in blocks.models) {
                                            var block = blocks.models[k];
                                            var types = block.get("types");

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

                                        if ("WebSocket" in window) {
                                            if (window.io) {
                                                console.log(io);
                                                var socket = io.connect("http://" + SmartBlocks.Config.server_name + ":10001");
                                                socket.emit('set id', SmartBlocks.Config.session_id);
                                                socket.on('msg', function (data) {
                                                    SmartBlocks.events.trigger("ws_notification", data);
                                                });
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
                                    },
                                    error: function () {

                                    }
                                });


                            }
                        });
                    }
                });


            });


        },
        States: {
            main_loading: false
        },
        Methods: {
            start: function () {
                Backbone.history.start();
                $(window).bind("hashchange", function () {
                    SmartBlocks.events.trigger("hashchange");
                    for (var k in SmartBlocks.Data.apps.models) {
                        var app = SmartBlocks.Data.apps.models[k];
                        if (app.ready)
                            app.route();
                        else {
                            app.events.once("ready", function () {
                                app.route();
                            });
                        }

                    }
                });
            },
            render: function (view) {
                var base = this;
                $("#content").html(view.$el);
            },
            setApp: function (app) {
                var base = this;
                app.launch();

            },
            entry: function () {
                if (!SmartBlocks.entry_app) {
                    var block = SmartBlocks.Data.blocks.where({
                        token: SmartBlocks.Config.startup_app.block
                    })[0];
                    if (block) {
                        SmartBlocks.Url.params = SmartBlocks.Config.startup_app.url_params;
                        var apps = new SmartBlocks.Collections.Applications(block.get('apps'));
                        var app = apps.where({
                            token: SmartBlocks.Config.startup_app.app
                        })[0];

                        if (app) {
                            app = SmartBlocks.Data.apps.get(app.get('token'));
                            SmartBlocks.entry_app = app;
                            SmartBlocks.Methods.setApp(SmartBlocks.entry_app);
                        }
                    }
                } else {
                    SmartBlocks.Methods.setApp(SmartBlocks.entry_app);
                }


            },
            startMainLoading: function (message, steps) {
                if (!SmartBlocks.loading_screen) {
                    SmartBlocks.loading_screen = new LoadingScreen();
                }
                if (!SmartBlocks.loading_screen.initiated) {
                    SmartBlocks.loading_screen.init();
                }
                SmartBlocks.loading_screen.setMax(steps);
                SmartBlocks.loading_screen.setText(message);
                SmartBlocks.loading_screen.setLoad(1);
                SmartBlocks.States.main_loading = true;

                SmartBlocks.Methods.render(SmartBlocks.loading_screen);
            },
            continueMainLoading: function (step_add, message) {
                if (message) {
                    SmartBlocks.loading_screen.setText(message);
                }
                SmartBlocks.loading_screen.setLoad(SmartBlocks.loading_screen.pb_view.load + step_add);
            },
            stopMainLoading: function () {
                SmartBlocks.States.main_loading = false;
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
                            SmartBlocks.Methods.continueMainLoading((1 / SmartBlocks.Methods.count) * 3, "Loading data");
                            if (++SmartBlocks.Methods.processed >= SmartBlocks.Methods.count) {
                                var blocks = SmartBlocks.Data.blocks;
                                var blocks_count = blocks.length;
                                var processed_blocks = 0;
                                for (var k in blocks.models) {
                                    var block = blocks.models[k];
                                    if (block.get("main")) {
                                        console.log(block.get("name"), " has a main");
                                        require([block.get("main")], function (main) {
                                            main.init();
                                            processed_blocks++;
                                            if (processed_blocks >= blocks_count) {
                                                SmartBlocks.events.trigger("start_solution");
                                                SmartBlocks.Methods.continueMainLoading((1 / blocks_count) * 2, "Initiating");
                                            }
                                        });
                                    } else {
                                        processed_blocks++;
                                        if (processed_blocks >= blocks_count) {
                                            SmartBlocks.events.trigger("start_solution");
                                            SmartBlocks.Methods.continueMainLoading((1 / blocks_count) * 2, "Initiating");
                                        }
                                    }
                                }


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