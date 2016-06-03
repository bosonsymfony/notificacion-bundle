/**
 * NotificacionBundle/Resources/public/adminApp/filters/notificacionHomeFilter.js
 */
angular.module('app')
    .filter('notificacionHomeFilter',
        function () {
            return function (input) {
                input = input || '';
                var out = "";
                for (var i = 0; i < input.length; i++) {
                    out = input.charAt(i) + out;
                }
                return out;
            }
        }
    )
    .filter('ellipsis', function () {
        return function (text, length) {
            if (text.length > length) {
                return text.substr(0, length) + '...';
            }
            return text;
        }
    });