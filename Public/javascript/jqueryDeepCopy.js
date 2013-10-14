jQuery.extend_deep = function() {
    console.log("Deep copying");
    var target = arguments[0] || {}, i = 1, length = arguments.length, deep = false, options;

    if ( target.constructor == Boolean ) {
        deep = target;
        target = arguments[1] || {};
        i = 2;
    }

    if ( typeof target != "object" && typeof target != "function" )
        target = {};

    if ( length == 1 ) {
        target = this;
        i = 0;
    }

    for ( ; i < length; i++ )
        if ( (options = arguments[ i ]) != null )
            for ( var name in options ) {
                if ( target === options[ name ] )
                    continue;

                if ( deep && options[ name ] && typeof options[ name ] == "object" && target[ name ] && !options[ name ].nodeType )
//                    target[ name ] = jQuery.extend( target[ name ], options[ name ] );
                    target[ name ] = jQuery.extend_deep( true, target[ name ], options[ name ] );

                else if ( options[ name ] != undefined )
                    target[ name ] = options[ name ];

            }

    return target;
};