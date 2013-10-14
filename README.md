SmartBlocks Core
===============

What is SmartBlocks?
--------------------

SmartBlocks is a framework that allows you to quickly and easily create an HTML5 applications ecosystem.
SmartBlocks handles the back-end and front-end parts of it.

How does it work?
-----------------
It's all about the "Blocks" part.

When you get a copy of SmartBlocks, you'll find
a directory structure, not very far from those you can find in usual MVC web frameworks,
with the app, config, public folders.

You'll be working from the plugins folder. In this folder, you'll be able to install
application blocks.

A block represents a part of your app ecosystem. It contains ways to handle data (with
a backend) and the apps that use that data. It also contains a configuration that allows
you to inform the ecosystem about the available apps, and how they will be routed.

As for now, a few blocks are being developed for demonstration purposes. In the future,
you'll be able to download blocks from an block store website, and you'll be able to
create and interconnect your own blocks - as soon as a real documentation is available ;).

The back-end
------------

The back-end allows the developer to handle data storing. It is written in PHP and uses
an MVC framework of our doing, Muffin Framework, based on the Doctrine ORM, and that allows
you to easily write REST webservices for your data (which is useful for the front-end as
Backbone natively supports REST for models).

The front-end
-------------

The whole application ecosystem works as a single page application.

The front end is written in JavaScript, and uses a variety of well known front-end frameworks,
like jQuery, UnderscoreJS, BackboneJS, AmplifyJS and so on.

The front end uses the data available from the back-end, which is loaded in a data library when a user
launches the page - usually only the data he is allowed to have access to : data security is handled in
the back end and data is filtered according to the backend controllers actions policies.

Routing
-------

Front-end routing is based on Backbone's router system. Routes are defined in an app descriptor in
the config file that describes available apps in a block.

```json
{
    "name": "app name",
    "token": "app_identifier",
    [...]
    "routing": {
        "name/name/:param1/name/:param2" : "route_keyword"
    }
}
```
In this app config example, the developer will connect routes to action in the front-end in the following way :

```javascript
app.initRoutes({
    route_keyword: function (param1, param2) {
        //actions to execute, view loading etc.
    }
});
```


Front-end shortcuts
-------------------

The shortcut system eases the development of shortcuts in your front-end app.

```javascript
SmartBlocks.Shortcuts.add([17, 18, 68], function () {
    //shortcut actions
}, "#appname/subappname");
```

In this example, a shortcut on Ctr-Alt-A is created, and effective only when the URL starts with #appname/subappname

And so on...
============

I know, I don't explain much about how to use the framework, but the project is still under heavy development.
However, I will write a lot more about SmartBlocks in a near future, so if that kind of project seems interesting
to you, please stay tuned.

I will also be very pleased to answer any question you might have about the project, and will be glad to listen
to any advice.

Anyway, thanks for reading :).