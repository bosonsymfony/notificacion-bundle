/**
 * NotificacionBundle/Resources/public/adminApp/controllers/Correo/correoCtrl.js
 */
angular.module('app')
    .controller('correoCtrl',
        ['$scope', 'correoSvc', '$mdDialog',
            function ($scope, correoSvc, $mdDialog) {

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
                    $scope.promise = correoSvc.entities.get(query || $scope.query, success).$promise;
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
                        controller: 'correoDeleteCtrl',
                        focusOnOpen: false,
                        targetEvent: event,
                        locals: {
                            entities: $scope.selected
                        },
                        templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/Correo/delete-dialog.html'
                    }).then(getEntities);
                };

                $scope.addEntity = function (event) {
                    $mdDialog.show({
                        clickOutsideToClose: true,
                        controller: 'correoCreateCtrl',
                        focusOnOpen: false,
                        targetEvent: event,
                        templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/Correo/save-dialog.html'
                    }).then(getEntities);
                };

                $scope.editEntity = function (event) {
                    correoSvc.entities.query({id: $scope.selected[0].id},
                        function (response) {
                            $mdDialog.show({
                                clickOutsideToClose: true,
                                controller: 'correoUpdateCtrl',
                                focusOnOpen: false,
                                targetEvent: event,
                                templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/Correo/update-dialog.html',
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
                    correoSvc.entities.query({id: $scope.selected[0].id},
                        function (response) {
                            $mdDialog.show({
                                clickOutsideToClose: true,
                                controller: 'correoShowCtrl',
                                focusOnOpen: false,
                                targetEvent: event,
                                templateUrl: $scope.$urlAssets + 'bundles/notificacion/adminApp/views/Correo/show-dialog.html',
                                locals: {
                                    object: response
                                }
                            }).then(getEntities);
                        }, function (error) {
                            alert(error.message);
                        }
                    );
                }
            }
        ]
    )
    .controller('correoDeleteCtrl',
        ['$scope', '$mdDialog', 'entities', '$q', 'correoSvc',
            function ($scope, $mdDialog, entities, $q, correoSvc) {

                $scope.cancel = $mdDialog.cancel;

                function deleteEntity(entity, index) {
                    var deferred = correoSvc.entities.remove({id: entity.id});

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
    .controller('correoCreateCtrl',
        ['$scope', '$mdDialog', 'correoSvc', 'toastr',
            function ($scope, $mdDialog, correoSvc, toastr) {
                $scope.$watch('files.length', function (newVal, oldVal) {
                    console.log($scope.files);
                });
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

                        var fd = new FormData();
                        if ($scope.files.length > 0)
                            fd.append('notificacionbundle_notificacionmail[adjunto]', $scope.files[0].lfFile);
                        fd.append('notificacionbundle_notificacionmail[titulo]', $scope.entity['notificacionbundle_notificacionmail[titulo]']);
                        fd.append('notificacionbundle_notificacionmail[contenido]', $scope.entity['notificacionbundle_notificacionmail[contenido]']);
                        if ($scope.selectedUsers.length !== 0){
                            var arra = [];
                            arra = $scope.selectedUsers.map(function (user) {
                                return user.id;
                            });
                            for(var i =0; i<arra.length;i++){
                                fd.append('notificacionbundle_notificacionmail[users]['+i+']', arra[i] );
                            }
                        }
                        if ($scope.selectedRoles.length !== 0){
                            var arra = [];
                            arra = $scope.selectedRoles.map(function (user) {
                                return user.id;
                            });
                            for(var i =0; i<arra.length;i++){
                                fd.append('notificacionbundle_notificacionmail[roles]['+i+']', arra[i] );
                            }
                        }
                        correoSvc.entities.save(fd, success, error);
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
                    return correoSvc.users(query)
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
                    return correoSvc.roles(query)
                        .then(
                            function (data) {
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
    .controller('correoUpdateCtrl',
        ['$scope', '$mdDialog', 'correoSvc', 'object',
            function ($scope, $mdDialog, correoSvc, object) {
                $scope.$watch('files.length', function (newVal, oldVal) {
                    if(newVal != oldVal) $scope.divAdjuntoFromServer = false;
                    if($scope.files.length === 0 && $scope.adjuntoFromServer)
                        $scope.divAdjuntoFromServer = true;
                });

                /**
                 *
                 * inicialinzando chips
                 */
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



                $scope.readonly = false;
                $scope.selectedUserItem = null;
                $scope.searchUserText = null;
                $scope.queryUserSearch = queryUserSearch;
                $scope.users = null;
                $scope.selectedUsers = [];
                $scope.numberBuffer = '';
                $scope.autocompleteUserRequireMatch = true;
                $scope.transformChip = transformChip;


                $scope.files =  [];
                $scope.adjuntoFromServer = [];
                $scope.entity = {
                    'notificacionbundle_notificacionmail[fecha]': object.fecha,
                    'notificacionbundle_notificacionmail[titulo]': object.titulo,
                    'notificacionbundle_notificacionmail[contenido]': object.contenido,
                    'notificacionbundle_notificacionmail[estado]': object.estado,
                    'notificacionbundle_notificacionmail[adjunto]': object.adjunto,
                };
                console.log(object.adjunto);
                if(object.adjunto){
                    $scope.divAdjuntoFromServer = true;
                    $scope.adjuntoFromServer.push(object.adjunto);
                    //console.log("addPalo");
                    //$scope.files.push({ifDataUrl:'',ifFile:  new Blob(),ifFileName:object.adjunto});
                }
                $scope.selectedUsers = object.user;
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
                        var fd = new FormData();
                        if ($scope.files.length > 0)
                            fd.append('notificacionbundle_notificacionmail[adjunto]', $scope.files[0].lfFile);
                        fd.append('notificacionbundle_notificacionmail[titulo]', $scope.entity['notificacionbundle_notificacionmail[titulo]']);
                        fd.append('notificacionbundle_notificacionmail[contenido]', $scope.entity['notificacionbundle_notificacionmail[contenido]']);
                        if ($scope.selectedUsers.length !== 0){
                            var arra = [];
                            arra = $scope.selectedUsers.map(function (user) {
                                return user.id;
                            });
                            for(var i =0; i<arra.length;i++){
                                fd.append('notificacionbundle_notificacionmail[users]['+i+']', arra[i] );
                            }
                        }
                        fd.append('_method', 'PUT');
                       // console.log(fd.get('notificacionbundle_notificacionmail[users]'));
                       // if ($scope.selectedUsers.length !== 0){
                       //     fd.append('notificacionbundle_notificacionmail[users][]', $scope.selectedUsers.map(function (user) {
                       //         return user.id;
                       //     }));
                       // }
                        if ($scope.selectedRoles.length !== 0){
                            fd.append('notificacionbundle_notificacionmail[roles][]', $scope.selectedRoles.map(function (role) {
                                return role.id;
                            }));
                        }

                        correoSvc.update.save({id: object.id},fd,success,error);
                    }
                }



                $scope.accept = function () {
                    updateEntity();
                };

                /**
                 * choice dialog
                 */

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
                    return correoSvc.users(query)
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
                    return correoSvc.roles(query)
                        .then(
                            function (data) {
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
    ).controller('correoShowCtrl',
    ['$scope', '$mdDialog', 'correoSvc', 'object',
        function ($scope, $mdDialog, bandejaEntradaSvc, object) {
            $scope.entity = {
                'notificacionbundle_notificacionmail[fecha]': object.fecha.date,
                'notificacionbundle_notificacionmail[titulo]': object.titulo,
                'notificacionbundle_notificacionmail[contenido]': object.contenido,
                'notificacionbundle_notificacionmail[autor]': object.autor.username,
                'notificacionbundle_notificacionmail[users]': object.user.map(function(item){
                    return {username:item.username,email:item.email} ;
                })
            };
            $scope.cancel = function () {
                return $mdDialog.cancel();
            };
        }
    ]
);