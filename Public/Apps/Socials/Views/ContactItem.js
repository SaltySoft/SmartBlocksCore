define([
    'jquery',
    'underscore',
    'backbone',
    'text!Apps/Socials/Templates/contact_item.html',
    'Apps/Socials/Models/ContactRequest',
    'ContextMenuView'
], function ($, _, Backbone, ContactItem, ContactRequest, ContextMenu) {
    var ContactListItemView = Backbone.View.extend({
        tagName: "li",
        className: "contact_list_item",
        initialize: function () {
            var base = this;
            base.user = base.model;
        },
        init: function (SmartBlocks, request) {
            var base = this;
            base.SmartBlocks = SmartBlocks;

            base.request = request;

            base.render();
            base.registerEvents();
        },
        render: function () {
            var base = this;

            var template = _.template(ContactItem, {
                user: base.user
            });

            base.$el.html(template);
            if (base.request && typeof base.request != "boolean") {
                base.$el.find(".accept").show();
                base.$el.find(".decline").show();
            }
            if (base.request && typeof base.request == "boolean") {
                base.$el.find(".add_button").show();
            }
        },
        registerEvents: function () {
            var base = this;
            base.$el.attr("oncontextmenu", "return false;");

            if (base.SmartBlocks.connected_users.get(base.user.get("id"))) {
                base.$el.find(".online").addClass("true");
            }

            base.SmartBlocks.connected_users.on("change", function () {
                if (base.SmartBlocks.connected_users.get(base.user.get("id"))) {
                    base.$el.find(".online").addClass("true");
                }
                else {
                    base.$el.find(".online").removeClass("true");
                }
            });

            if (base.request) {
                if (typeof base.request == "boolean") {
                    base.$el.delegate(".add_button", "click", function () {
                        var contact_request = new ContactRequest();

                        contact_request.set("sender", base.SmartBlocks.current_user);
                        contact_request.set("target", base.user);
                        contact_request.save({}, {
                            success: function (model, response) {
                                console.log("USER", base.user);
                                if (response.error !== true) {
                                    base.SmartBlocks.show_message("The request was successfully sent.");
                                    base.SmartBlocks.sendWs("socials", {
                                        action: "contact_notif",
                                        message: base.SmartBlocks.current_user.get("username") + " wants to add you."
                                    }, [
                                        base.user.get("session_id")
                                    ]);
                                }
                                else
                                    base.SmartBlocks.show_message(response.message);
                            }
                        });
                    });
                }
                else {

                    base.$el.delegate(".accept", "click", function () {
                        var contact_request = base.request;
                        var contacts = base.SmartBlocks.current_user.get("contacts");
                        contacts.push(contact_request.get("sender"));
                        base.SmartBlocks.current_user.set("contacts", contacts);
                        console.log("ADDED CONTACT", base.SmartBlocks.current_user);
                        base.SmartBlocks.current_user.save({}, {
                            success: function () {
                                base.request.destroy({
                                    success: function () {
                                        base.$el.remove();
                                        //Notifications
                                        base.SmartBlocks.sendWs("socials", {
                                            action: "contact_change",
                                            message: base.SmartBlocks.current_user.get("username") + " has accepted your request"
                                        }, [
                                            contact_request.get("sender").get("session_id")
                                        ]);
                                    }
                                });
                            }
                        });
                    });
                    base.$el.delegate(".decline", "click", function () {
                        base.request.destroy({
                            success: function () {
                                base.$el.remove();
                            }
                        });

                    });
                }
            }
            base.$el.mousedown(function (e) {

                if (e.which == 3) {
                    var context_menu = new ContextMenu();
                    context_menu.init(base.SmartBlocks);
                    context_menu.addButton("View profile", function () {
                        window.location = "/Users/socials#profile/" + base.user.get('id')
                    });

                    context_menu.show(e);

                    return false;
                }
            });
        }
    });

    return ContactListItemView;
});