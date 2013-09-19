define([
    'underscore',
    'backbone'
], function (_, Backbone) {
    var Application = Backbone.Model.extend({
        defaults: {
        },
        initialize: function () {
            var base = this;
        },
        launch: function () {
            var base = this;
            console.log("LAUNCHING");
            if (base.get("entry_point")) {

                require([base.get("entry_point")], function (View) {
                    var view = new View();
                    console.log(view.el);
                    SmartBlocks.Methods.render(view);
                    view.init(base);
                });
            }
        },
        quit: function () {
            var base = this;
            SmartBlocks.Methods.entry();
        }
    });

    return Application;
});