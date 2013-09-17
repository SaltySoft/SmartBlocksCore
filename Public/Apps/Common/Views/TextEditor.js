define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/Common/Templates/text_editor.html'
], function ($, _, Backbone, TextEditorTemplate) {
    var TextEditorView = Backbone.View.extend({
        tagName: "div",
        className: "text_editor",
        events: {
        },
        initialize: function () {
        },
        init: function (text, heightFactor) {
            var base = this;
            base.events = _.extend({}, Backbone.Events);
            base.text = text;
            base.heightFactor = heightFactor !== undefined ? heightFactor : 100;
            base.buffer = "";
            base.render();
        },
        render: function () {
            var base = this;
            var template = _.template(TextEditorTemplate, {
                text: base.text
            });
            base.$el.html(template);

            base.initRichTextEditor();
        },
        getText: function () {
            var base = this;
            return base.frame.contents().find("body").html();
        },
        setText: function (text) {
            var base = this;
            base.frame.contents().find("body").html(text);
            base.resizeFrame();
        },
        charAt: function (pos) {
            var base = this;
            var content = base.frame.contents().find("body").html();
            return content.substring(pos, pos + 1);
        },
        initRichTextEditor: function () {
            var base = this;
            console.log("initRichTextEditor");
            var frame = base.$el.find(".richTextEditor");
            base.frame = frame;

            frame.load(function () {


                base.frameDoc = frame[0].contentWindow.document;
                var link = $(document.createElement("link"));


                base.frameDoc.open();
                base.frameDoc.write(base.text);
                base.frameDoc.close();

                base.frameDoc.designMode = "on";

                var contents = frame.contents();
                contents.find("head").append('<link rel="stylesheet" href="/Apps/Common/Css/text_editor.css" type="text/css" />');
                console.log(contents.find("head"));
                base.initializeEvents();
                base.resizeFrame();
            });
        },
        resizeFrame: function () {
            var base = this;
            base.frame.css("height", base.heightFactor);
            var needed_height = base.frame.contents().find("body").outerHeight();
            if (needed_height % base.heightFactor != 0) {
                needed_height += base.heightFactor - (needed_height % base.heightFactor);
            }
            base.frame.css("height", needed_height);
        },
        initializeEvents: function () {
            var base = this;
            var frame = base.$el.find(".richTextEditor");

            base.$el.delegate(".editor_button", "click", function (e) {
                $(this).toggleClass("selected");
                console.log("click", frame);
                var contentWindow = frame[0].contentWindow;

                contentWindow.focus();
                contentWindow.document.execCommand($(this).attr("data-commandName"), false, null);
                contentWindow.focus();

                base.events.trigger("text_editor_text_change");
            });

            base.$el.delegate(".editor_option", "change", function () {
                console.log("onchange", $(this).val());
                var contentWindow = frame[0].contentWindow;

                contentWindow.focus();
                contentWindow.document.execCommand($(this).attr("data-commandName"), false, $(this).val());
                contentWindow.focus();

                base.events.trigger("text_editor_text_change");
            });
            var fire_blur_timer = 0;
            $('body', $(frame).contents()).blur(function (event) {
//                var textUpdate = event.currentTarget.innerHTML;
//                base.text = textUpdate;
//                var message = {
//                    status: "text_update",
//                    text: textUpdate
//                };
                clearTimeout(hide_timer);
//                hide_timer = setTimeout(function () {
//                    base.$el.find(".editor_button_container").fadeOut();
//                }, 1000);
                clearTimeout(fire_blur_timer);
                fire_blur_timer = setTimeout(function () {
                    base.events.trigger('blur');
                }, 500);
            });
            var show_timer = 0;
            base.$el.mouseover(function () {
                clearTimeout(show_timer);
                clearTimeout(hide_timer);
//                show_timer = setTimeout(function () {
//                    base.$el.find(".editor_button_container").fadeIn();
//                }, 100);

            });
            var hide_timer = 0;
            base.$el.mouseout(function () {
                clearTimeout(hide_timer);
//                hide_timer = setTimeout(function () {
//                    base.$el.find(".editor_button_container").fadeOut();
//                }, 1000);

            });

            $('body', $(frame).contents()).focus(function (event) {
                clearTimeout(show_timer);
                clearTimeout(hide_timer);
//                show_timer = setTimeout(function () {
//                    base.$el.find(".editor_button_container").fadeIn();
//                }, 100);
                clearTimeout(fire_blur_timer);
                base.events.trigger('focus');
            });

            base.caret = 0;
            frame.contents().delegate("body", "keydown", function (e) {
                text_save = base.getText();

                base.caret = base.caretPosition();
                base.buffer += base.charAt(base.caretPosition().start);

                base.resizeFrame();
            });

            frame.contents().delegate("body", "keyup", function (e) {
                if (base.getText() != text_save) {
                    base.events.trigger("text_editor_text_change");
                }
                base.resizeFrame();
            });

            var text_save = null;
            frame.contents().delegate("body", "mousedown", function (e) {
                text_save = base.getText();
            });

            frame.contents().delegate("body", "mouseup", function (e) {
                base.events.trigger("text_editor_select", base.caretPosition());

                if (base.getText() != text_save) {
                    base.events.trigger("text_editor_text_change");
                }
                base.resizeFrame();
            });
        },
        caretPosition: function () {
            var base = this;
            var element = base.frame[0];
            var range = element.contentWindow.getSelection().getRangeAt(0);
            console.log("OFFSET", range);

            return {start: range.line, end: range.endOffset};
        },
        lock: function () {
            var base = this;
            if (base.frameDoc)
                base.frameDoc.designMode = "off";
            base.$el.find(".richTextEditor").addClass("locked");
        },
        unlock: function () {
            var base = this;
            if (base.frameDoc)
                base.frameDoc.designMode = "on";
            base.$el.find(".richTextEditor").removeClass("locked");
        }
    });

    return TextEditorView;
});