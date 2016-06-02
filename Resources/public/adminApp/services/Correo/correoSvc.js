/**
 * NotificacionBundle/Resources/public/adminApp/controllers/Correo/correoSvc.js
 */
angular.module('app')
    .factory('correoSvc',
        ['$resource', '$http',
            function ($resource, $http) {
                return {
                    entities: $resource(Routing.generate('notificacionmail_create', {}, true) + ':id', null, {
                        'query': {
                            isArray: false
                        },
                        save: {
                            method: 'POST',
                            transformRequest: angular.identity,
                            headers: {'Content-Type': undefined}
                        },
                        'get': {
                            ignoreLoadingBar: true
                        }
                    }),
                    update: $resource(Routing.generate('notificacionmail_create', {}, true) + ':id', null, {
                        'query': {
                            isArray: false
                        },
                        save: {
                            method: 'POST',
                            transformRequest: angular.identity,
                            headers: {'Content-Type': undefined}
                        },
                        'get': {
                            ignoreLoadingBar: true
                        }
                    }),
                    users: function (query) {
                        return $http.get(Routing.generate('notification_users', {
                                'filter':query
                            }, true),{
                            ignoreLoadingBar: true
                        })
                    },
                    roles: function (query) {
                        return $http.get(Routing.generate('notification_roles', {
                                'filter':query
                            }, true),{
                            ignoreLoadingBar: true
                        })
                    },
                    uploadFileToUrl: function (file, uploadUrl) {
                        var fd = new FormData();
                        fd.append('file', file);
                        $http.post(uploadUrl, fd, {
                                transformRequest: angular.identity,
                                headers: {'Content-Type': undefined}
                            })
                            .success(function () {
                            })
                            .error(function () {
                            });
                    },
                    getCsrfToken: function (id_form) {
                        return $http.post(Routing.generate('notificacion_csrf_form', {}, true),{id_form:id_form}, {
                            ignoreLoadingBar: true
                        })
                    }
                };
            }
        ]
    );