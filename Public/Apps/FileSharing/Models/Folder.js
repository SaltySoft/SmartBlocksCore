define([
    'underscore',
    'backbone',
    'Apps/FileSharing/Models/File',
    'UserModel',
    'GroupModel'
], function(_, Backbone, File, User, Group) {
    var Folder = Backbone.Model.extend({
        urlRoot: "/Folders",
        defaults: {
        },
        parse: function (response, options) {
            var files = response.files;
            var files_array = new Array();
            for (var key in files)
            {
                var file = new File(files[key]);
                files_array.push(file);
            }
            response.files = files_array;

            var users = response.users_allowed;
            var users_array = new Array();
            for (var key in users)
            {
                var user = new User(users[key]);
                users_array.push(user);
            }
            response.users_allowed = users_array;

            var groups = response.groups_allowed;
            var groups_array = new Array();
            for (var key in groups)
            {
                var group = new Group(groups[key]);
                groups_array.push(group);
            }
            response.groups_allowed = groups_array;

            var folders = response.folders;
            var folders_array = new Array();
            for (var key in folders)
            {
                var folder = new Folder(folders[key]);
                folders_array.push(folder);
            }
            response.folders = folders_array;
            return response;
        }
    });

    return Folder;
});