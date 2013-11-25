requirejs.config({
    baseUrl: '/',
    paths: sb_paths,
    shim: sb_shims
});

/*Fill with default apps (file sharing and chat)*/
var apps = [
    "underscore",
    "backbone",
    "SmartBlocks",
    "UserModel",
    "UsersCollection",
    "Apps/UserRequester/app",
    "Externals",
    "LoadingScreen",
    "Apps/Common/Boot/SmartBlocks"
];

$(document).ready(function () {
    //Uncomment next line to disable default context menu everywhere in SmartBlocks
    requirejs(apps,
        function (/*defaults, */_, Backbone, sb_basics, User, UsersCollection, UserRequester, Externals, LoadingScreen, SmartBlocks) {

            SmartBlocks.init(function () {

            });
        });
});
