define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/Socials/Apps/Profile/Templates/profile.html',
    'Apps/Socials/Views/ContactItem'
], function ($, _, Backbone, ProfileTemplate, ContactItem) {
    var ProfileView = Backbone.View.extend({
        tagName: "div",
        className: "user_profile_app",
        initialize: function () {
            var base = this;
            base.user = base.model;
        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;

            base.render();
            base.registerEvents();
        },
        render: function () {
            var base = this;

            var template = _.template(ProfileTemplate, {
                user: base.user
            });

            base.$el.html(template);

            var contacts = base.user.get("contacts");

            base.$el.find(".contact_list").html("");


            for (var k in contacts) {
                var contact = contacts[k];
                console.log("USER", contact);
                var contact_item = new ContactItem({
                    model: contact
                });
                contact_item.init(base.SmartBlocks);
                base.$el.find(".contact_list").append(contact_item.$el);

            }

        },
        registerEvents: function () {
            var base = this;



        }
    });

    return ProfileView;
});