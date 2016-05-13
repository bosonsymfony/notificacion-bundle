
/**
 * NotificacionBundle/Resources/public/adminApp/services/notificacionHomeSvc.js
 */
angular.module('app')
        .service('notificacionHomeSvc', [
                    function () {
                        var message = '';

                        function setMessage(newMessage) {
                            message = newMessage;
                        }

                        function getMessage() {
                            return message;
                        }

                        return {
                            setMessage: setMessage,
                            getMessage: getMessage,
                            $get: function () {

                            }
                        }
                    }
                ]
        );