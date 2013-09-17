define([
    'jquery',
    'underscore',
    'backbone',
    'text!/Apps/Common/Templates/color_picker.html'
], function ($, _, Backbone, ColorPickerTemplate) {
    var ColorPicker = Backbone.View.extend({
        tagName: "div",
        className: 'color_picker',
        initialize: function (size) {
            var base = this;
            base.color = "grey";
            base.size = size;
            return base.render();
        },
        render: function () {
            var base = this;

            var template = _.template(ColorPickerTemplate, {});
            base.$el.html(template);

            base.background = new Image();
            base.background.src = "/Apps/Common/Resources/colormap.gif";

            base.canvas = base.$el.find(".color_picker_chooser_canvas")[0];

            base.context = base.canvas.getContext("2d");

            base.background.onload = function () {
                base.context.drawImage(base.background, 0, 0);
            };
            if (base.size !== undefined) {
                base.$el.find(".color_picker_square").css("width", base.size);
                base.$el.find(".color_picker_square").css("height", base.size);
            }

            base.initializeEvents();
            return base.$el;
        },
        initializeEvents: function () {
            var base = this;
            base = $.extend(base, Backbone.Events);
            var color_chooser =  base.$el.find(".color_picker_chooser");
            base.$el.delegate(".color_picker_square", "click", function () {
                color_chooser.show(200);
            });

            var hide_timer = 0;
            base.$el.delegate(".color_picker", "mousemove", function () {
                clearTimeout(hide_timer);
            });
            base.$el.delegate(".color_picker_chooser", "mousemove", function () {
                clearTimeout(hide_timer);
            });

            base.$el.delegate(".color_picker", "mouseout", function () {
                hide_timer = setTimeout(function () {
                    color_chooser.hide(200);
                }, 500);
            });
            base.$el.delegate(".color_picker_chooser", "mouseout", function () {
                hide_timer = setTimeout(function () {
                    color_chooser.hide(200);
                }, 500);
            });

            $(base.canvas).click(function (e) {
                var canvas = $(base.canvas);

                var imgData = base.context.getImageData(e.pageX - canvas.offset().left, e.pageY - canvas.offset().top, 1, 1).data;
                base.$el.find(".color_picker_square").css("background-color", 'rgb(' + imgData[0] + ',' + imgData[1] + ',' + imgData[2] + ')');
                base.color =  'rgb(' + imgData[0] + ',' + imgData[1] + ',' + imgData[2] + ')';
                base.trigger("color_changed", base.color);
            });
        }
    });

    return ColorPicker;
});