define([
    'underscore',
    'backbone',
    'Apps/FileSharing/Models/Folder'
], function (_, Backbone, Folder) {
    var FoldersCollection = Backbone.Collection.extend({
        model: Folder,
        url: "/Folders"
    });

    return FoldersCollection;
});