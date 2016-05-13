/**
 * NotificacionBundle/Resources/public/adminApp/controllers/Notificacion/notificacionCtrl.js
 */
angular.module('app')
        .controller('notificacionCtrl',
                ['$scope', 'notificacionSvc', '$mdDialog',
                    function ($scope, notificacionSvc, $mdDialog) {

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
                            $scope.promise = notificacionSvc.entities.get(query || $scope.query, success).$promise;
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
                                controller: 'notificacionDeleteCtrl',
                                focusOnOpen: false,
                                targetEvent: event,
                                locals: {
                                    entities: $scope.selected
                                },
                                templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/Notificacion/delete-dialog.html'
                            }).then(getEntities);
                        };

                        $scope.addEntity = function (event) {
                            $mdDialog.show({
                                clickOutsideToClose: true,
                                controller: 'notificacionCreateCtrl',
                                focusOnOpen: false,
                                targetEvent: event,
                                templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/Notificacion/save-dialog.html'
                            }).then(getEntities);
                        };

                        $scope.editEntity = function (event) {
                            notificacionSvc.entities.query({id: $scope.selected[0].id},
                                    function (response) {
                                        $mdDialog.show({
                                            clickOutsideToClose: true,
                                            controller: 'notificacionUpdateCtrl',
                                            focusOnOpen: false,
                                            targetEvent: event,
                                            templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/Notificacion/update-dialog.html',
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
        .controller('notificacionDeleteCtrl',
                ['$scope', '$mdDialog', 'entities', '$q', 'notificacionSvc',
                    function ($scope, $mdDialog, entities, $q, notificacionSvc) {

                        $scope.cancel = $mdDialog.cancel;

                        function deleteEntity(entity, index) {
                            var deferred = notificacionSvc.entities.remove({id: entity.id});

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
        .controller('notificacionCreateCtrl',
                ['$scope', '$mdDialog', 'notificacionSvc',
                    function ($scope, $mdDialog, notificacionSvc) {

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
                        }

                        function clean() {
                            $scope.entity = {};
                        }

                        function error(errors) {
                            $scope.errors = errors.data;
                        }

                        function addEntity() {

                            if ($scope.form.$valid) {
                                notificacionSvc.entities.save($scope.entity, success, error);
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
                    }
                ]
        )
        .controller('notificacionUpdateCtrl',
                ['$scope', '$mdDialog', 'notificacionSvc', 'object',
                    function ($scope, $mdDialog, notificacionSvc, object) {

                        $scope.entity = {
                            'notificacionbundle_notificacion[fecha]': object.fecha,
                            'notificacionbundle_notificacion[titulo]': object.titulo,
                            'notificacionbundle_notificacion[contenido]': object.contenido,
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
                                notificacionSvc.entities.save({id: object.id}, angular.extend({}, $scope.entity, {_method: 'PUT'}), success, error);
                            }
                        }

                        $scope.accept = function () {
                            updateEntity();
                        };
                    }
                ]
        );