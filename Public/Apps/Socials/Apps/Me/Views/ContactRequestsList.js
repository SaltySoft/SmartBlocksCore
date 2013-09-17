define([
    'jquery',
    'underscore',
    'backbone',
    'Apps/Socials/Collections/ContactRequests',
    'Apps/Socials/Views/ContactItem'
], function ($, _, Backbone, ContactRequestsCollection, ContactItem) {
    var ContactRequestsListView = Backbone.View.extend({
        tagName: "ul",
        className: "contact_requests_list",
        initialize: function () {

        },
        init: function (SmartBlocks) {
            var base = this;
            base.SmartBlocks = SmartBlocks;

            base.contact_requests = new ContactRequestsCollection();
            base.fetchRequests();
            base.registerEvents();
        },
        fetchRequests: function () {
            var base = this;
            base.contact_requests.fetch({
                success: function (list) {
                    base.render();
                }
            });
        },
        render: function () {
            var base = this;
            base.$el.html("");
            for (var k in base.contact_requests.models) {
                var contact_request = base.contact_requests.models[k];

                var contact_item = new ContactItem({
                    model: contact_request.get("sender")
                });
                contact_item.init(base.SmartBlocks, contact_request);
                base.$el.append(contact_item.$el);
            }
        },
        registerEvents: function () {
            var base = this;

        }
    });

    return ContactRequestsListView;
});