define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/Socials/Views/ContactItem'
], function ($, _, Backbone, ContactItemView) {
    var ContactListView = Backbone.View.extend({
        tagName: "ul",
        className: "contact_list_view",
        initialize: function () {
            var base = this;
            base.user = base.model;
        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;

            base.render();
        },
        render: function () {
            var base = this;
            base.$el.html("");
            for (var k in base.user.get("contacts")) {
                var contact = base.user.get("contacts")[k];
                console.log("CONTACT", contact);
                var contact_item_view = new ContactItemView({
                    model: contact
                });
                contact_item_view.init(base.SmartBlocks);
                base.$el.append(contact_item_view.$el);
            }

        },
        registerEvents: function () {
            var base = this;


        }
    });

    return ContactListView;
});