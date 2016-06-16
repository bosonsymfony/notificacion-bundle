<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15/06/16
 * Time: 23:14
 */

namespace UCI\Boson\NotificacionBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UCI\Boson\NotificacionBundle\Services\NotificationCorreoService;
class NotificationCorreoServiceTest extends WebTestCase
{

    public static function setUpBeforeClass()
    {
        //start the symfony kernel
        $kernel = static::createKernel();
        $kernel->boot();

        //get the DI container
        self::$container = $kernel->getContainer();

        //now we can instantiate our service (if you want a fresh one for
        //each test method, do this in setUp() instead
        self::$correoService = self::$container->get('notificacion.correo');
    }

    public function testSendNotification(){

    }
    public function testNotifyByUser($titulo, $contenido, $usuarios, $adjunto= null){

    }

    public function testStoreAdjunto(UploadedFile $file, $id){
        
    }


    public function testDeleteAdjunto($id){
//        self::$correoService->sendNotification("")
    }

    public function testGetNombreAdjunto($id){
        
    }
}
