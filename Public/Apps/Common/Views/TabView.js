define([
    'jquery',
    'underscore',
    'backbone',
    'text!TabsTemplate',
    'jqueryui'
], function ($, _, Backbone, TabsTemplate) {
    var AppView = Backbone.View.extend({
        tagName:"div",
        className:"k_um_tabs_view",
        events:{

        },
        initialize:function () {

        },
        init:function (AppEvents) {
            this.AppEvents = AppEvents;
            this.render();
        },
        render:function () {
            var base = this;
            var template = _.template(TabsTemplate, {
            });
            base.$el.html(template);

        },
        addTab:function(name, element, link) {
            var base = this;

            //Tab controller button creation
            var control_button_li = $(document.createElement("li"));
            control_button_li.addClass("tab_controller");
            var control_button = $(document.createElement("a"));
            if (link == undefined)
                control_button.attr("href", "javascript:void(0);");
            else
                control_button.attr("href", "#" + link);
            control_button.html(name);
            control_button_li.append(control_button);

            base.$el.find(".tabs_controller").append(control_button_li);

            //Tab creation and content insertion
            var tab = $(document.createElement("div"));
            tab.addClass("tab");
            tab.append(element);
            base.$el.find(".tabs_container").append(tab);

            control_button.click(function () {
                base.$el.find(".tab").hide();
                tab.show();
            });
        },
        show:function(index) {
            var base = this;
            base.$el.find(".tab").hide();
            var i = 0;
            base.$el.find(".tab").each(function (){
                i++;
                var elt = $(this);
                if (i == index) {
                    elt.show();
                }
            });
        }
    });

    return AppView;
});