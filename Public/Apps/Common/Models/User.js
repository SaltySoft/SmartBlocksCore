define([
    'jquery',
    'underscore',
    'backbone',
    'JobModel',
    'GroupModel',
    'amplify'
], function ($, _, Backbone, Job, Group) {
    var User = Backbone.Model.extend({
        urlRoot: "/Users",
        defaults: {
            username: "unregistered"
        },
        parse: function (response, option) {

            var jobs = response.jobs;
            var job_array = new Array();
            for (var key in jobs) {
                var job = new Job(jobs[key]);
                job_array.push(job);
            }
            response.jobs = job_array;

            var groups = response.groups;
            var group_array = new Array();
            for (var key in groups) {
                var group = new Group(groups[key]);
                group_array.push(group);
            }
            response.groups = group_array;

            var contacts_a = response.contacts;
            var contacts = [];
            for (var k in contacts_a) {
                var contact = new User(contacts_a[k]);
                contacts.push(contact);
            }

            response.contacts = contacts;
            return response;
        },
        getImageUrl: function (size, callback) {
            var base = this;
            Externals.webshell.exec({
                code: function () {
                    echo(apis.gravatar({
                        mail: args.usermail,
                        size: args.size
                    }, {
                        view: null
                    }));
                },
                args: {
                    size: size,
                    usermail: base.get('email')
                },
                process: function (json, meta) {
                    if (callback) {
                        callback(meta.view);
                    }
                }
            });
        }
    });
    User.getCurrent = function (callback) {
//        if (amplify.store("current_user")) {
//            console.log(amplify.store("current_user"));
//            var user = new User(amplify.store("current_user"));
//            console.log(user);
//            callback(user);
//
//        } else {
        $.ajax({
            url: "/Users/current_user",
            success: function (data, status) {
                if (!data.status || data.status != "error") {
                    var user = new User(data);
                    user.fetch({
                        success: function () {
                            callback(user);
                            amplify.store("current_user", user, { expires: 5000});
                        }
                    });

                }
            }
        });
//        }

    }
    window.User = User;
    return User;
});

