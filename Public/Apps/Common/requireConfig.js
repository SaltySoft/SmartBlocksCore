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
    JobModel: "/Apps/Common/Models/Job",
    GroupModel: "/Apps/Common/Models/Group",
    UsersCollection: "/Apps/Common/Collections/Users",
    JobsCollection: "/Apps/Common/Collections/Jobs",
    GroupsCollection: "/Apps/Common/Collections/Groups",

    TabView: "/Apps/Common/Views/TabView",
    TabsTemplate: "/Apps/Common/Templates/tabs.html",

    SearchResultsTemplate: '/Apps/Common/Templates/search_results.html',
    SelectionTemplate: '/Apps/Common/Templates/selection.html',
    ContextMenuView: "/Apps/Common/Views/ContextMenu",

    Class: '/Apps/Common/Useful/Class',
    ColorPicker: '/javascript/colorpicker',
    TextEditorView : "/Apps/Common/Views/TextEditor",

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