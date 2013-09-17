
define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/AppOrganizer/Templates/home.html'
], function ($, _, Backbone, HomeTemplate) {
    var AoHome = Backbone.View.extend({
        tagName:"div",
        className:"k_ao_home",
        initialize:function () {

        },
        init:function (AppEvents) {
            this.AppEvents = AppEvents;
            this.render();
        },
        render:function () {
            var template = _.template(HomeTemplate, {});
            this.$el.html(template);
            return this;
        }
    });

    return AoHome;
});