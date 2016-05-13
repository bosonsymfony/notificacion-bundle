
/**
 * NotificacionBundle/Resources/public/adminApp/controllers/notificacionHomeCtrl.js
 */
angular.module('app')
        .controller('notificacionHomeCtrl',
                ['$scope', 'notificacionHomeSvc',
                    function ($scope, notificacionHomeSvc) {

                        $scope.showSource = false;
                        notificacionHomeSvc.setMessage('Welcome to the NotificacionBundle example configuration.');
                        $scope.message = notificacionHomeSvc.getMessage();

                    }
                ]
        );