define([
    'underscore',
    'backbone'
], function (_, Backbone) {
    var Application = Backbone.Model.extend({
        defaults:{

        },
        launch: function () {
            var base = this;
            if (base.get("entry_point")) {
                require([base.get("entry_point")], function (View) {
                    var view = new View();
                    SmartBlocks.Methods.render(view);

                    view.init();
                })
            }
        }
    });

    return Application;
});