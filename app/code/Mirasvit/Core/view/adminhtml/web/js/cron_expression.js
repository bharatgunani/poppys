define([
    'jquery',
    'Mirasvit_Core/js/lib/prettycron',
    'Mirasvit_Core/js/lib/later'
], function ($, cron) {
    'use strict';

    return function (el) {
        var $input = $(el);
        var $container = $('<div />');

        $container.insertAfter($input);

        $input.on('change keyup', function (e) {
            render($(e.target), $container);
        });

        $input.on('change', function (e) {
            render($(e.target), $container);
        });

        render($input, $container);

        function render($input, $container) {
            $container.html('');

            var val = $input.val();

            var readable = cron.toString(val);

            var $p = $('<p />')
            .css('font-size', '13px')
            .css('background', isValid($input) ? '#7db97d' : '#dc7e7e')
            .css('padding', '5px')
            .css('color', '#fff')
            .html(readable);
            $container.append($p);

            later.schedule(later.parse.cron(val)).next(3).forEach(function (next) {
                var $p = $('<p />').css('font-size', '11px').html(next.toGMTString());
                $container.append($p);
            })
        }

        function isValid($input) {
            var val = $input.val();

            var parts = val.split(' ');

            parts = parts.filter(function (item) {
                if (item.trim()) {
                    return item;
                }
            });

            return parts.length == 5;
        }
    };
});