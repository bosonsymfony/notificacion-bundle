/**
 * NotificacionBundle/Resources/public/adminApp/controllers/TiempoReal/tiempoRealSvc.js
 */
angular.module('app')
    .factory('tiempoRealSvc',
        ['$resource', '$http',
            function ($resource, $http) {
                return {
                    entities: $resource(Routing.generate('notificacion_create', {}, true) + ':id', null, {
                        'query': {
                            isArray: false
                        },
                        'get': {
                            ignoreLoadingBar: true
                        }
                    }),
                    users: function (query) {
                        return $http.get(Routing.generate('notification_users', {
                            'filter': query
                        }, true), {
                            ignoreLoadingBar: true
                        })
                    },
                    roles: function (query) {
                        return $http.get(Routing.generate('notification_roles', {
                            'filter': query
                        }, true), {
                            ignoreLoadingBar: true
                        })
                    },
                    getCsrfToken: function (id_form) {
                        return $http.post(Routing.generate('notificacion_csrf_form', {}, true),{id_form:id_form}, {
                            ignoreLoadingBar: true
                        })
                    },
                    getTranslations: function () {
                        return $http.get(Routing.generate('notificacion_validators_form', {}, true),{}, {
                            ignoreLoadingBar: true
                        })
                    }
                };
            }
        ]
    );