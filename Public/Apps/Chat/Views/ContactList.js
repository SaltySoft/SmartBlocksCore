define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/Chat/Templates/contact_list.html',
    'Apps/Chat/Views/ContactItem'
], function ($, _, Backbone, ContactListTemplate, ContactItem) {
    var ContactList = Backbone.View.extend({
        tagName: "div",
        className: "chat_contact_list",
        initialize: function () {

        },
        init: function (SmartBlocks, main_view) {
            var base = this;
            base.SmartBlocks = SmartBlocks;
            base.main_view = main_view;

            base.render();
            base.registerEvents();

        },
        render: function () {
            var base = this;

            var template = _.template(ContactListTemplate, {});
            base.$el.html(template);

            var contacts = base.SmartBlocks.current_user.get("contacts");
            for (var k in contacts) {
                var contact_item = new ContactItem({
                    model: contacts[k]
                });
                contact_item.init(base.SmartBlocks, base);
                base.$el.find(".contact_list_container").html(contact_item.$el);
            }
        },
        registerEvents: function () {
            var base = this;

            base.$el.delegate(".contact_list_button", "click", function () {
                base.$el.find(".contact_list_container").toggle();
            });
        }
    });
    return ContactList;
});