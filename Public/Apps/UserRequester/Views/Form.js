define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/UserRequester/Templates/form.html'
], function ($, _, Backbone, FormTemplate) {
    var FormView = Backbone.View.extend({
        tagName: "div",
        className: "form_cache cache",
        initialize: function () {

        },
        init: function (SmartBlocks, data) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.data = data;
            base.render();
        },
        render: function () {
            var base = this;

            var template = _.template(FormTemplate, {
                data: base.data
            });

            base.$el.html(template);
            $("body").append(base.$el);
            base.registerEvents();
            base.$el.find(".user_r_form_container").width(base.$el.find("form"));
        },
        registerEvents: function () {
            var base = this;
            base.$el.delegate(".cancel_button", "click", function () {
                base.$el.remove();
            });

            base.$el.delegate(".validate_button", "click", function () {
                base.SmartBlocks.startLoading("Linking your accounts");
                $.post(base.data.action, base.$el.find(".form").serialize(), function (data, status) {
                    base.SmartBlocks.show_message(data.message);
                    base.SmartBlocks.stopLoading();
                    base.$el.remove();

                });

            });
        }

    });

    return FormView;
});