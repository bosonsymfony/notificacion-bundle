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
                        }
                    }),
                    users: function (query) {
                        return $http.get(Routing.generate('notification_users', {}, true) + '?filter=' + query)
                    },
                    roles: function (query) {
                        return $http.get(Routing.generate('notification_roles', {}, true) + '?filter=' + query)
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
                    }
                };
            }
        ]
    );