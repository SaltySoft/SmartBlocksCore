define([
    'jquery',
    'underscore',
    'backbone',
    'jqueryui'
], function ($, _, Backbone) {

    var func_set = {
        loadings: [],
        timer: 0,
        title_timer: 0,
        notif_sound: new Audio("/sounds/notif.wav"),
        init_solution: function () {
            $("body").delegate(".log_button", "click", function () {
                var elt = $("#user_log");
                if (elt.is(":visible")) {
                    elt.hide(200);
                } else {
                    elt.show(200);
                }
            });

            $("body").delegate(".notif_button", "click", function () {
                var elt = $("#notification_center_container");
                if (elt.is(":visible")) {
                    elt.hide(200);
                } else {
                    elt.show(200);
                }
            });

            Date.prototype.getMonthName = function (lang) {
                lang = lang && (lang in Date.locale) ? lang : 'en';
                return Date.locale[lang].month_names[this.getMonth()];
            };

            Date.prototype.getDayName = function (lang) {
                lang = lang && (lang in Date.locale) ? lang : 'en';
                return Date.locale[lang].day_names[this.getDay()];
            };

            Date.prototype.getMonthNameShort = function (lang) {
                lang = lang && (lang in Date.locale) ? lang : 'en';
                return Date.locale[lang].month_names_short[this.getMonth()];
            };

            Date.locale = {
                en:{
                    month_names:['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    month_names_short:['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    day_names:['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
                }
            };
        },
        show_message: function (message) {
            clearTimeout(this.timer);
            $("#flash_message").html(message);
            $("#flash_shower").fadeIn(300);
            this.timer = setTimeout(function () {
                $("#flash_shower").fadeOut(300)
            }, 3000)
        },
        server_handshake: function (websocket, identification) {
            var base = this;
            if (websocket !== undefined) {
                websocket.addEventListener("open", function (event) {
                    data_array = {};
                    data_array.identification = identification;
                    websocket.send(JSON.stringify(data_array));
                });
            }

        },
        parseWs: function (message) {

            if (message) {
                message.data = message.data.replace(':ecom:', '&');
                console.log(JSON.parse(message.data));
                var ob = JSON.parse(JSON.parse(message.data));
                return ob;
            } else {
                return undefined;
            }
        },
        startLoading: function (message) {
            var base = this;
            $("#loader").html('<img src="/images/loader.gif" />');
            $("#loader").show();
            if (message) {
                $("#loader").append(" <span>" + message + "</span>");
                base.loadings.push({
                    message: message
                });
                return base.loadings.length - 1;
            } else {
                $("#loader").append("<span>Loading</span>");
                base.loadings.push({
                    message: "Loading"
                });
                return base.loadings.length - 1;
            }

        },
        stopLoading: function (index) {
            $("#loader").hide();
        },
        notifySound: function () {
            var base = this;
            base.notif_sound.play();
        },
        animateTitle: function (message) {
            var base = this;
            clearInterval(base.title_timer);
            var m = message + " - ";
            base.title_timer = setInterval(function () {
                $(document).attr('title', m);
                m = m.substring(1) + m[0];
            }, 100);
        },
        setTitle: function (title) {
            var base = this;
            clearInterval(base.title_timer);
            $(document).attr('title', title);
        },
        chatNotif: function (user_id) {
            $.ajax({
                url: "/Discussions",
                data: {
                    user_id: user_id
                },
                success: function (data, status) {
                    var number = 0;
                    for (var d in data) {
                        if (data[d].notify) {
                            number++;
                        }
                    }

                    if (number <= 0) {
                        $("#chat_notif").html(0);
                        $("#chat_notif").hide();
                    } else {
                        $("#chat_notif").html(number);
                        $("#chat_notif").show();
                    }
                }
            });

        },
        sendWs: function (app, data, to) {
            var base = this;
            data.app = app;
            array = undefined;
            if (to != "all") {
                var array = [];
                var sess = to;
                for (var k in  sess) {
                    array[k] = sess[k];
                }
            }


            var ob = {
                data: data,
                session_ids: array,
                broadcast: to == "all"
            };
            if (base.websocket && base.websocket.readyState == 1) {
                try {
                    base.websocket.send(JSON.stringify(ob));
                } catch (e) {

                }
            }
        },
        heartBeat: function (user) {
            var base = this;

            base.sendWs("heartbeat", {
                app: "heartbeat",
                user: user.attributes
            }, "all");
        }
    };

    return func_set;
});