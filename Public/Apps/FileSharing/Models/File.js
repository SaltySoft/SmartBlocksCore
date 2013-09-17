define([
    'underscore',
    'backbone'
], function (_, Backbone) {
    var File = Backbone.Model.extend({
        urlRoot: "/Files",
        defaults: {
        },
        parse: function (response) {

            var parent_f_object = response.parent_folder;
            var parent_folder = new File(parent_f_object);
            response.parent_folder = parent_folder;

            var subfiles = response.subfiles;
            var subfiles_collection = new FilesCollection();
            for (var k in subfiles) {
                var file = new File(subfiles[k]);
                subfiles_collection.add(file);
            }

            response.subfiles = subfiles_collection;
            return response;
        }
    });

    var FilesCollection = Backbone.Collection.extend({
        model: File,
        url: "/Files"
    });

    return File;
});