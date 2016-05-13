/**
 * NotificacionBundle/Resources/public/adminApp/controllers/TiempoReal/tiempoRealSvc.js
 */
angular.module('app')
        .factory('tiempoRealSvc',
                ['$resource','$http',
                    function ($resource,$http) {
                        return {
                            entities: $resource(Routing.generate('notificacion_create', {}, true) + ':id', null, {
                                'query': {
                                    isArray: false
                                }
                            }),
                            users: function (query){
                                return $http.get(Routing.generate('notification_users', {}, true)+'?filter='+query)
                            },
                            roles: function (query){
                                return $http.get(Routing.generate('notification_roles', {}, true)+'?filter='+query)
                            }
                        };
                    }
                ]
        );