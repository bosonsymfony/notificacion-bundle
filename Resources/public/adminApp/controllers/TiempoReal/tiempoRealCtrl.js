/**
 * NotificacionBundle/Resources/public/adminApp/controllers/TiempoReal/tiempoRealCtrl.js
 */
angular.module('app')
    .controller('tiempoRealCtrl',
        ['$scope', 'tiempoRealSvc', '$mdDialog',
            function ($scope, tiempoRealSvc, $mdDialog) {

                var bookmark;

                $scope.selected = [];

                $scope.filter = {
                    options: {
                        debounce: 500
                    }
                };

                $scope.query = {
                    filter: '',
                    limit: '5',
                    order: 'id',
                    page: 1
                };

                function getEntities(query) {
                    $scope.promise = tiempoRealSvc.entities.get(query || $scope.query, success).$promise;
                }

                function success(entities) {
                    $scope.entities = entities;
                    $scope.selected = [];
                }

                $scope.onPaginate = function (page, limit) {
                    getEntities(angular.extend({}, $scope.query, {page: page, limit: limit}));
                };

                $scope.onReorder = function (order) {
                    getEntities(angular.extend({}, $scope.query, {order: order}));
                };

                $scope.removeFilter = function () {
                    $scope.filter.show = false;
                    $scope.query.filter = '';

                    if ($scope.filter.form.$dirty) {
                        $scope.filter.form.$setPristine();
                    }
                };

                $scope.$watch('query.filter', function (newValue, oldValue) {
                    if (!oldValue) {
                        bookmark = $scope.query.page;
                    }

                    if (newValue !== oldValue) {
                        $scope.query.page = 1;
                    }

                    if (!newValue) {
                        $scope.query.page = bookmark;
                    }

                    getEntities();
                });

                $scope.deleteSelected = function (event) {
                    $mdDialog.show({
                        clickOutsideToClose: true,
                        controller: 'tiempoRealDeleteCtrl',
                        focusOnOpen: false,
                        targetEvent: event,
                        locals: {
                            entities: $scope.selected
                        },
                        templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/TiempoReal/delete-dialog.html'
                    }).then(getEntities);
                };
                
                $scope.addEntity = function (event) {
                    $mdDialog.show({
                        clickOutsideToClose: true,
                        controller: 'tiempoRealCreateCtrl',
                        focusOnOpen: false,
                        targetEvent: event,
                        templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/TiempoReal/save-dialog.html'
                    }).then(getEntities);
                };

                $scope.editEntity = function (event) {
                    tiempoRealSvc.entities.query({id: $scope.selected[0].id},
                        function (response) {
                            $mdDialog.show({
                                clickOutsideToClose: true,
                                controller: 'tiempoRealUpdateCtrl',
                                focusOnOpen: false,
                                targetEvent: event,
                                templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/TiempoReal/update-dialog.html',
                                locals: {
                                    object: response
                                }
                            }).then(getEntities);
                        }, function (error) {
                            alert(error);
                        }
                    );
                }
                $scope.showEntity = function (event) {
                    tiempoRealSvc.entities.query({id: $scope.selected[0].id},
                        function (response) {
                            $mdDialog.show({
                                clickOutsideToClose: true,
                                controller: 'tiempoRealShowCtrl',
                                focusOnOpen: false,
                                targetEvent: event,
                                templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/TiempoReal/show-dialog.html',
                                locals: {
                                    object: response
                                }
                            }).then(getEntities);
                        }, function (error) {
                            alert(error);
                        }
                    );
                }
            }
        ]
    )
    .controller('tiempoRealDeleteCtrl',
        ['$scope', '$mdDialog', 'entities', '$q', 'tiempoRealSvc', '$http',
            function ($scope, $mdDialog, entities, $q, tiempoRealSvc, $http) {

                $scope.cancel = $mdDialog.cancel;

                function deleteEntity(entity, index) {
                    var deferred = tiempoRealSvc.entities.remove({id: entity.id});

                    deferred.$promise.then(function () {
                        entities.splice(index, 1);
                    });

                    return deferred.$promise;
                }

                function onComplete() {
                    $mdDialog.hide();
                }

                $scope.delete = function () {
                    $q.all(entities.forEach(deleteEntity)).then(onComplete);
                }
            }
        ]
    )
    .controller('tiempoRealCreateCtrl',
        ['$scope', '$mdDialog', 'tiempoRealSvc', 'toastr',
            function ($scope, $mdDialog, tiempoRealSvc, toastr) {

                var update = false;

                var hide = true;

                $scope.cancel = function () {
                    if (update) {
                        return $mdDialog.hide();
                    } else {
                        return $mdDialog.cancel();
                    }
                };

                function success(response) {
                    if (hide) {
                        $mdDialog.hide();
                    } else {
                        update = true;
                        clean();
                    }
                    toastr.success(response.data);
                }

                function clean() {
                    $scope.entity = {};
                }

                function error(errors) {
                    $scope.errors = errors.data;
                    if (errors.status === 401) {
                        toastr.error(errors.data, 'HOla', {timeOut: 2500});
                    }
                }

                function addEntity() {

                    if ($scope.form.$valid) {
                        $scope.entity['notificacionbundle_notificacion[users]'] = $scope.selectedUsers.map(function (user) {
                            return user.id;
                        });
                        $scope.entity['notificacionbundle_notificacion[roles]'] = $scope.selectedRoles.map(function (role) {
                            return role.id;
                        });
                        tiempoRealSvc.getCsrfToken('notificacion').then(function (data) {
                            console.log(data.data);
                            $scope.entity['notificacionbundle_notificacion[_token]'] = data.data;
                            tiempoRealSvc.entities.save($scope.entity, success, error);
                            }
                        )
                    }
                }

                $scope.accept = function () {
                    hide = true;
                    addEntity();
                };

                $scope.apply = function () {
                    hide = false;
                    addEntity();
                };

                $scope.errors = {};

                /**
                 * choice dialog
                 */


                $scope.readonly = false;
                $scope.selectedUserItem = null;
                $scope.searchUserText = null;
                $scope.queryUserSearch = queryUserSearch;
                $scope.users = null;
                $scope.selectedUsers = [];
                $scope.numberBuffer = '';
                $scope.autocompleteUserRequireMatch = true;
                $scope.transformChip = transformChip;
                /**
                 * Return the proper object when the append is called.
                 */
                function transformChip(chip) {
                    // If it is an object, it's already a known chip
                    if (angular.isObject(chip)) {
                        return chip;
                    }
                    // Otherwise, create a new one
                    return {name: chip, type: 'new'}
                }

                /**
                 * Search for users.
                 */
                function queryUserSearch(query) {
                    return tiempoRealSvc.users(query)
                        .then(function (data) {
                            return data.data.map(function (user) {
                                user._lowerusername = user.username.toLowerCase();
                                user.email = user.email.toLowerCase();
                                user._lowerid = user.id;
                                return user;
                            });
                        });
                }

                /**
                 * choice dialog
                 */

                /**
                 * choice dialog Roles
                 */


                $scope.readonly = false;
                $scope.selectedRoleItem = null;
                $scope.searchRoleText = null;
                $scope.queryRoleSearch = queryRoleSearch;
                $scope.roles = null;
                $scope.selectedRoles = [];
                $scope.numberBuffer = '';
                $scope.autocompleteRoleRequireMatch = true;
                $scope.transformRoleChip = transformRoleChip;
                /**
                 * Return the proper object when the append is called.
                 */
                function transformRoleChip(chip) {
                    // If it is an object, it's already a known chip
                    if (angular.isObject(chip)) {
                        return chip;
                    }
                    // Otherwise, create a new one
                    return {name: chip, type: 'new'}
                }

                /**
                 * Search for roles.
                 */
                function queryRoleSearch(query) {
                    return tiempoRealSvc.roles(query)
                        .then(function (data) {
                            return data.data.map(function (role) {
                                role._lowernombre = role.nombre.toLowerCase();
                                role._lowerid = role.id;
                                return role;
                            });
                        });
                }

                /**
                 * choice dialog
                 */


            }
        ]
    )
    .controller('tiempoRealUpdateCtrl',
        ['$scope', '$mdDialog', 'tiempoRealSvc', 'object',
            function ($scope, $mdDialog, tiempoRealSvc, object) {

                $scope.entity = {
                    'notificacionbundle_notificacion[fecha]': object.fecha,
                    'notificacionbundle_notificacion[titulo]': object.titulo,
                    'notificacionbundle_notificacion[contenido]': object.contenido,
                    'notificacionbundle_notificacion[estado]': object.estado,
                    //'notificacionbundle_notificacion[_token]': object._token,
                };


                $scope.cancel = function () {
                    return $mdDialog.cancel();
                };

                function success(response) {
                    $mdDialog.hide();
                }

                function error(errors) {
                    $scope.errors = errors.data;
                }

                function updateEntity() {
                    if ($scope.form.$valid) {
                        tiempoRealSvc.update.save({id: object.id}, angular.extend({}, $scope.entity, {_method: 'PUT'}), success, error);
                    }
                }

                $scope.accept = function () {
                    updateEntity();
                };
            }
        ]
    ).controller('tiempoRealShowCtrl',
    ['$scope', '$mdDialog', 'object',
        function ($scope, $mdDialog, object) {
            $scope.entity = {
                'notificacionbundle_notificacion[fecha]': object.fecha,
                'notificacionbundle_notificacion[titulo]': object.titulo,
                'notificacionbundle_notificacion[contenido]': object.contenido,
                'notificacionbundle_notificacion[autor]': object.autor.username,
                'notificacionbundle_notificacion[users]': [{username: object.user.username, email: object.user.email}]
            };
            $scope.cancel = function () {
                return $mdDialog.cancel();
            };
        }
    ]
);