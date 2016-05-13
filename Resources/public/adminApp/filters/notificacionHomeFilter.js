
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
        );