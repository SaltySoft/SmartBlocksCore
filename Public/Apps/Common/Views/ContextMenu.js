define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/Common/Templates/context_menu.html'
], function ($, _, Backbone, ContextMenuTemplate) {
    var ContextMenu = Backbone.View.extend({
        tagName:"div",
        className:"context_menu",
        shown:false,
        initialize:function () {

        },
        init:function () {

        },
        addButton:function (text, callback, icon) {
            var base = this;
            var button = $(document.createElement("div"));
            button.addClass("context_menu_button");

            button.click(function () {
                base.$el.hide();
                callback();
            });

            var _text = $(document.createElement("div"));
            _text.html(text);
            button.append(_text);

            if (icon) {
                var img = $(document.createElement("img"));
                img.attr("src", icon);
                img.addClass("context_menu_icon");
                button.prepend(img);
            }
            base.$el.attr("oncontextmenu", "return false;");
            base.$el.append(button);
        },
        show:function (e) {
            var base = this;
            if (base.shown != true) {
                base.shown = true;
                $("body").append(base.$el);

                base.$el.css("left", e.pageX);
                base.$el.css("top", e.pageY);

                var timer = 0;
                base.$el.mousemove(function () {
                    clearTimeout(timer);
                });

                base.$el.mouseout(function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        base.$el.hide();
                    }, 500);
                });
            } else {
                base.$el.show();
                base.$el.css("left", e.pageX);
                base.$el.css("top", e.pageY);

                var timer = 0;
                base.$el.mousemove(function () {
                    clearTimeout(timer);
                });

                base.$el.mouseout(function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        base.$el.hide();
                    }, 500);
                });
            }

            return false;
        }
    });

    return ContextMenu;
});