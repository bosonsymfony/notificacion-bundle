/**
 * NotificacionBundle/Resources/public/adminApp/directives/notificacionHomeDirective.js
 */
angular.module('app')
    .directive('notificacionHomeDirective',
        function () {
            return {
                restrict: 'A',
                link: function ($scope, element, attrs) {
                    element.bind('mouseenter', function () {
                        element.css('background-color', 'yellow');
                        element.css('color', 'red');
                    });
                    element.bind('mouseleave', function () {
                        element.css('background-color', 'white');
                        element.css('color', 'black');
                    });
                }
            }
        }
    ).directive('notificacionFileModel', ['$parse',
    function ($parse) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                var model = $parse(attrs.notificacionFileModel);
                var modelSetter = model.assign;

                element.bind('change', function () {
                    scope.$apply(function () {
                        modelSetter(scope, element[0].files[0]);
                    });
                });
            }
        };
    }]);
