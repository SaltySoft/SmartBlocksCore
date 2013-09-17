/**
 * Copyright (C) 2013 Antoine Jackson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 */

$(document).ready(
    function () {
        $("#flash_hider").click(
            function () {
                $("#flash_shower").fadeOut(300);
            }
        );
    }


);

function loading(str) {
    $("#loader").html(str);
    $("#loader").fadeIn();
}
function endloading(str) {
    $("#loader").fadeOut(function () {
        $("#loader").html("");
    });
}
var timer;
function show_message(msg) {
    clearTimeout(timer);
    $("#flash_message").html(msg);
    $("#flash_shower").fadeIn(300);
    timer = setTimeout(function () {
        $("#flash_shower").fadeOut(300)
    }, 3000)
}




SmartBlocks = $.extend({}, {
    user_id:0,

    setUserId:function (user_id) {
        base = this;
        base.user_id = user_id;
    },

    load_user:function (callback) {
        base = this;
        base.user = new User({ id:base.user_id });
        base.user.fetch({
            success:function () {
                callback(base.user);
            }
        });
    }
});

