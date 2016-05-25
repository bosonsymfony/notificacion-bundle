/**
 * NotificacionBundle/Resources/public/adminApp/controllers/BandejaEntrada/bandejaEntradaSvc.js
 */
angular.module('app')
        .factory('bandejaEntradaSvc',
                ['$resource',
                    function ($resource) {
                        return {
                            entities: $resource(Routing.generate('bandejaentrada', {}, true) + ':id', null, {
                                'query': {
                                    isArray: false
                                },'get': {
                                    ignoreLoadingBar: true
                                }
                            })
                        };
                    }
                ]
        );