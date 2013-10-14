define([
], function () {
    var external_module = {
        webshell: {
            exec: function (obj) {
                if (obj.code && obj.process) {
                    wsh.exec({
                        code: obj.code,
                        args: obj.args,
                        process: obj.process
                    });
                }
            }
        }
    };
    window.Externals = external_module;
    return external_module;
});