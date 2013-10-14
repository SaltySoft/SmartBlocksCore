var sb_paths = {
    jquery: "/javascript/jquery",
    underscore: "/javascript/underscore",
    underscore_string: '/javascript/underscore-string',
    backbone: "/javascript/backbone",
    text: "/javascript/text",
    default: "/javascript/default",
    jqueryui: "/javascript/jquery-ui.min",
    jqueryflip:"/javascript/jquery.flip.min",
    SmartBlocks: "/javascript/SmartBlocks",

    UserModel: "/Apps/Common/Models/User",
    UsersCollection: "/Apps/Common/Collections/Users",

    ContextMenuView: "/Apps/Common/Views/ContextMenu",

    Class: '/Apps/Common/Useful/Class',

    jDeepCopy: "/javascript/jqueryDeepCopy",
    amplify_lib: "/javascript/amplify.min",
    amplify: "/javascript/amplify.min",
    fullCalendar: "/javascript/fullcalendar.min",

    LoadingTemplate: "/Apps/Common/Templates/loading.html",
    LoadingScreen: "/Apps/Common/Views/LoadingScreen",
    'Externals': 'Apps/Common/Useful/External'
};

var sb_shims = {

    'underscore': {
        exports: '_'
    },
    underscore_string: {
        deps: ['underscore'],
        exports: '_s'
    },
    'backbone': {
        deps: ['underscore', 'jquery'],
        exports: 'Backbone'
    },
    'jqueryflip': {
        deps: ['jquery']
    },
    amplify: {
        "amplify": {
            deps: ['jquery'],
            exports: "amplify"
        }
    },
    fullCalendar: {
        deps: ['jquery', 'jqueryui'],
        exports: "fullCalendar"
    }
};