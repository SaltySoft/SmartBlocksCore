requirejs.config({
    baseUrl:'/',
    paths:sb_paths,
    shim:sb_shims
});

/*Fill with default apps (file sharing and chat)*/
var apps = ["underscore", "backbone", "SmartBlocks", "Apps/Chat/app", "Apps/FileSharing/app", "Apps/NotificationsCenter/app", "UserModel", "UsersCollection", "Apps/UserRequester/app", "Externals"];

if (app !== undefined) {
    apps.push(app);
}



$(document).ready(function () {
    //Uncomment next line to disable default context menu everywhere in SmartBlocks
//    $("body").attr("oncontextmenu", "return false");
    requirejs(apps,
        function (/*defaults, */_, Backbone, sb_basics, ChatApp, FileSharingApp, NotifCenterApp, User, UsersCollection, UserRequester, Externals, App) {

            window.SmartBlocks = {};
            SmartBlocks.basics = sb_basics;
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

                },
                me: function () {
                    base.me();
                    console.log("AP");
                },
                profile: function (id) {
                    base.profile(id);
                }
            });

            SmartBlocks.Methods = {
                render: function (view) {
                    var base = this;
                    $("#content").html(view.$el);
                }
            };



            SmartBlocks.router = new SmartBlocks.basics.Router();
            Backbone.history.start();

            SmartBlocks.basics.init_solution();

            SmartBlocks.Data.blocks = new SmartBlocks.Collections.Blocks();

            SmartBlocks.Data.blocks.fetch({
                success: function () {
                    var blocks =SmartBlocks.Data.blocks.models;

                    for (var k in blocks) {
                        var block = blocks[k];
                        var apps = block.get("apps");
                        console.log(apps);
                        for (var j in apps) {
                            var app = apps[j];
                            if (app.get("entry_point")) {
                                app.launch();
                            }
                        }

                    }
                }
            });

            User.getCurrent(function (current_user) {
                SmartBlocks.basics.connected_users = new UsersCollection();

                var timers = [];


                SmartBlocks.current_user = current_user;
                UserRequester.initialize(SmartBlocks.basics);



//                if (App) {
//                    App.initialize(SmartBlocks.basics);
//                    if (App.sync) {
//                        var sync_timer = 0;
////                        sync_timer = setInterval(function () {
////                            App.sync();
////                        }, 60000);
//
//                        $(document).keyup(function (e) {
//                            if (e.keyCode == 107) {
//                                console.log("Syncing");
//                                App.sync();
//                                clearInterval(sync_timer);
//                            }
//                        });
//                    }
//                }


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
//                    SmartBlocks.heartBeat(current_user);
                }, 5000);
            });
        });
});
