define([
    "jquery",
    "underscore",
    "backbone",
    "text!Apps/Socials/Templates/main_view.html",
    "Apps/Socials/Apps/Profile/Views/Profile",
    "Apps/Socials/Apps/Me/Views/Me",
    "UserModel"
], function ($, _, Backbone, MainViewTemplate, ProfileView, MeView, User) {
    var MainView = Backbone.View.extend({
        tagName: "div",
        className: "main_view",
        initialize: function () {

        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.render();

            var Router = Backbone.Router.extend({
                routes: {
                    "me": "me",
                    "profile/:id": "profile"
                },
                me: function () {
                    base.me();
                    console.log("AP");
                },
                profile: function (id) {
                    base.profile(id);
                }
            });

            var app_router = new Router();
            Backbone.history.start();


        },
        render: function () {
            var base = this;
            var template = _.template(MainViewTemplate, {});

            base.$el.html(template);
        },
        me: function () {
            var base = this;

            var me_view = new MeView();
            me_view.init(base.SmartBlocks);
            base.$el.find(".sub_app_holder").html(me_view.$el);
        },
        profile: function (id) {
            var base = this;

            var user = new User({ id: id });
            base.SmartBlocks.startLoading("Getting user information");
            user.fetch({
                success: function () {
                    var profile_view = new ProfileView({
                        model: user
                    });
                    profile_view.init(base.SmartBlocks);
                    base.$el.find(".sub_app_holder").html(profile_view.$el);
                    base.SmartBlocks.stopLoading();
                }
            });
        },
        registerEvents: function () {

        }
    });

    return MainView;
});