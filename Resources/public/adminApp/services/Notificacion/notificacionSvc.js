/**
 * NotificacionBundle/Resources/public/adminApp/controllers/Notificacion/notificacionSvc.js
 */
angular.module('app')
        .factory('notificacionSvc',
                ['$resource',
                    function ($resource) {
                        return {
                            entities: $resource(Routing.generate('notificacion_create', {}, true) + ':id', null, {
                                'query': {
                                    isArray: false
                                }
                            })
                        };
                    }
                ]
        );