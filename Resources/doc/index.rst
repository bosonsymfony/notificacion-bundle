Componente: NotificacionBundle
=============================


1. Descripción general
----------------------

    Está orientado a gestionar las notificaciones en tiempo real y de correo electronico.
    Garantiza también el registro de todas las excepciones lanzadas en tiempo de ejecución en ficheros logs con un formato entendible para su posterior análisis.


2. Instalación
--------------

    1. Copiar el componente dentro de la carpeta `vendor/boson/notificacion-bundle/UCI/Boson`.
    2. Registrarlo en el archivo `app/autoload.php` de la siguiente forma:

       .. code-block:: php

           // ...
           $loader = require __DIR__ . '/../vendor/autoload.php';
           $loader->add("UCI\\Boson\\NotificacionBundle", __DIR__ . '/../vendor/boson/notificacion-bundle');
           // ...

    3. Activarlo en el kernel de la siguiente manera:

       .. code-block:: php

           // app/AppKernel.php
           public function registerBundles()
           {
               return array(
                   // ...
                   new UCI\Boson\NotificacionBundle\NotificacionBundle(),
                   // ...
               );
           }

    4. Luego debes modificar la Clase DependencyInjection\[Nombredetubundle]Extension.php específicamente el método **load**
       para que cargue las notificaciones de tu bundle:

	   .. code-block:: php

	   	   //clase NotificacionExtension, método load
		   public function load()
		   {
		       // ...
			   $loader->load('services.yml');

			   $ExcpExtension = new ExcepcionesExtension();
			   $ExcpExtension->loadFileExcepciones($container);
		   }

2.1 Configuración
-----------------
Para enviar notificaciones en tiempo real es necesario
	1. Incluir la librería cliente de socket.io.

	      <script src="{{ asset('bundles/notificacion/node_modules/socket.io-client/socket.io.js') }}"></script>
          <script src="{{ asset('bundles/notificacion/node_modules/angular-toastr/dist/angular-toastr.tpls.min.js') }}"></script>
          <script>
             var socket = io.connect('http://10.58.10.152:3000');
          </script>
	2. Cargar la librería.
	3. Conectar el socket. Aquí se muestra un ejemplo de cómo incluir y registrar los sockets desde un controlador de AngularJs.

	function getToken(){
        $http.get($scope.urlServer+"/notificaciones/security-token").success(function (data) {
            socket.emit('newClient', {"security": data });
        });
    }
    getToken();

    socket.on('notification', function (data) {
        toastr.info(data)
    });
    socket.on('errorConnection', function (data) {
        toastr.error(data)
    });
    4. Obtener la información qué está pública.
    5. Enviarla (emitir un socket con esa información).

Ejemplo de como incluir y registrar los sockets con jquery.

	socket.emit('newClient', {"security": data });

    socket.on('notification', function (data) {
        toastr.info(data)
    });
    socket.on('errorConnection', function (data) {
        toastr.error(data)
    });

3. Especificación funcional
---------------------------

3.1. Requisitos funcionales
~~~~~~~~~~~~~~~~~~~~~~~~~~~


3.1.1. Enviar notificación a rol.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de permitir el envío de notificaciones a un rol determinado.
   	Cuando se envía una notificación a un rol todos los usuarios que tengan asignado dicho rol recibirán esta notificación en su bandeja de notificaciones.
   	Este requisito se encarga de permitir el envío de notificaciones a un rol determinado.


3.1.2. Enviar notificación a usuario.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de permitir el envío de notificaciones a un usuario determinado.
   	Cuando se envía una notificación a un usuario este podrá ver los detalles de la misma esta en su bandeja de notificaciones.

3.1.3. Enviar notificación por correo electrónico a usuario.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
  	Este requisito se encarga de permitir el envío de notificaciones por correo electrónico a un usuario independientemente del rol al que se encuentre asociado.

3.1.4. Enviar notificación por correo electrónico a rol.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
   	Este requisito se encarga de permitir el envío de notificaciones por correo electrónico a un rol.

3.1.5. Brindar servicio de notificacion a sistema externo.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
   	Este requisito se encarga de brindar servicios para notificar desde sistemas externos.

3.1.6. Alertar a usuarios conectados de notificaciones en tiempo real.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga, luego de enviar una notificación a un usuario en específico, alertarlo de la existencia de notificaciones.
	Para poder consultar con detalles la notificación enviada accederá a su bandeja de notificaciones.
	La descripción de toda excepción de tipo **LocalException** puede ser obtenida mediante el método getDescripcion().

3.1.7. Buscar notificación de usuario.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de buscar una notificación de usuario de las existentes en el sistema.
	Al insertar los criterios de búsqueda establecidos por el usuario el sistema mostrará un listado de aquellas notificaciones que cumplen con el criterio especificado.

3.1.8. Eliminar notificación de usuario.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de eliminar notificaciones pertenecientes a un usuario de su bandeja de notificaciones.

3.1.9. Listar notificación de usuario.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de listar todas las notificaciones de un usuario en su bandeja de notificaciones.

3.1.10. Mostrar detalles de notificación de usuario.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de mostrar los detalles de las notificaciones.
	Cuando se le envía una notificación a un usuario, estas se podrán consultar en la bandeja de notificaciones.

3.1.11. Buscar notificación de administrador.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de buscar una notificación perteneciente al administrador de las existentes en el sistema.
	Al insertar los criterios de búsqueda establecidos por el administrador el sistema mostrará un listado de aquellas notificaciones que cumplen con el criterio especificado.

3.1.12. Eliminar notificación de administrador.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de eliminar notificaciones de la bandeja de notificaciones del administrador.

3.1.13. Listar notificación de administrador.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de listar todas las notificaciones del administrador en su bandeja de notificaciones.

3.1.14. Mostrar detalles de notificación de administrador.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	Este requisito se encarga de mostrar los detalles de las notificaciones en la bandeja de entrada del administrador.

3.2. Requisitos no funcionales
------------------------------

4. Servicios que brinda
-----------------------
	* notifyByUser de la clase NotificationTRService recibe los parámetros $titulo, $contenido y $user. Se encarga de notificar a un usuario en tiempo real.
	* notifyByUser de la clase NotificationCorreoService recibe los parámetros $titulo, $contenido, $usuarios y $adjunto. Se encarga de notificar a un usuario por 		correo electrónico.


5. Servicios de los que depende
-------------------------------
	* 'security.token_storage'. Se encarga de obtener el token de seguridad con los datos de los usuarios conectados.
	* 'mailer'. Servicio para el envío de correos electrónicos por smtp.
	* 'doctrine'. Se encarga de obtener el manejador de doctrine para la persistencia de datos.
	* 'logger' Se encarga de registrar logs si ocurren fallos en el envío de datos.

6. Eventos generados
--------------------

7. Eventos observados
---------------------

	.. code-block:: php

	    onKernelException(GetResponseForExceptionEvent $event)

	El evento onKernelException es observado con el objetivo de escribir los logs de las excepciones ocurridas en el sistema. Ver implementación  de la clase ..\\ExcepcionesBundle\\EventListener.


8. Otros detalles claves
------------------------
	1. Para el envío de notificaciones de correo electrónico la PC debe tener el certificado UCICA.
	   Los sistemas basados en UBUNTU deben:
		*Guardar en usr/share/ca_certificates con otro nombre (UCICA.crt) el certificado.
		*Activar con el comando  dpkg_reconfigure ca_certificates.
		*Seleccionar el certificado y agregarlo.

	2. Verificar la configuración del componente BackandBundle.
		*boson/backend-bundle

	3. Configuraciones
		*En el fichero de configuración conf.yml se debe configurar los siguientes parámetros:
		  mailer_encryption: tls
		  mailer_port: 25
     	  mailer_auth_mode: login

		*En el fichero  de configuración parameters.yml se debe copiar los siguientes parámetros:
		  encryption:  "%mailer_encryption%"
	      port:  "%mailer_port%"
          auth_mode:  "%mailer_auth_mode%"
---------------------------------------------

:Versión: 1.0 17/7/2015
:Autores: Daniel Arturo Casals Amat dacasals@uci.cu

Contribuidores
--------------

:Entidad: Universidad de las Ciencias Informáticas. Centro de Informatización de Entidades.

Licencia
--------



