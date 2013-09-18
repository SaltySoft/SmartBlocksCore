define([
    "underscore",
    "backbone",
    "Apps/Common/Models/Block",
    "Apps/Common/Models/Application"
], function (_, Backbone, Block, Application) {
    var BlocksCollection = Backbone.Collection.extend({
        url:"/Blocks",
        model:Block,
        reparse: function () {
            var base = this;
            var models = base.models;
            for (var k in models) {
                var app_array = models[k].get('apps');
                var new_array = [];
                for (var j in app_array) {
                    new_array.push(new Application(app_array[j]));
                }
                models[k].set("apps", new_array);
            }
            console.log(base);
        },
        parse:function (response, status) {
            for (var k in response) {
                var apps = response[k].apps;
                var applications = [];

                for (var i in apps) {
                    var application = new Application(apps[i]);
                    applications.push(application);
                }

                response[k].apps = applications;
            }
//            console.log(response);
            return response;
        }
    });

    return BlocksCollection;
});