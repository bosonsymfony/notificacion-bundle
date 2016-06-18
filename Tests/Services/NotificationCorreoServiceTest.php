<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15/06/16
 * Time: 23:14
 */

namespace UCI\Boson\NotificacionBundle\Tests\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use UCI\Boson\NotificacionBundle\Form\Model\SendNotMail;
use UCI\Boson\NotificacionBundle\Services\NotificationCorreoService;
use UCI\Boson\SeguridadBundle\Exception\DuplicatedResourceException;

class NotificationCorreoServiceTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $mailSvc;
    private $urlAttach;
    private $lastInsertedId;
    private $lastInsertedId2;
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
        $this->urlAttach = static::$kernel->getContainer()->getParameter('notification_store_attachments');
        $this->mailSvc = static::$kernel->getContainer()->get('notificacion.correo');
        try {
            static::$kernel->getContainer()->get('boson.saml_user_provider')->registrarUsuario('testuser', 'testuser@uci.cu');
            static::$kernel->getContainer()->get('security.role_hierarchy')->adicionarRol('ROLE_TEST');
            static::$kernel->getContainer()->get('security.role_hierarchy')->adicionarUsuarioARol('ROLE_TEST','testuser');
        } catch (DuplicatedResourceException $ex) {

        }
        catch (\Exception $e){

        }


    }
    
    
//    public function testnotifyByUser()
//    {
//        static::bootKernel(array());
////        $notificacion->setAdjunto(new UploadedFile(__DIR__.DIRECTORY_SEPARATOR.'file.txt','file.txt'));
//        dump(static::$kernel->getContainer()->get('notificacion.correo')->notifyByUser('titulo', 'contenido', ['dacasals@uci.cu']));
//    }

    /**
     */
    public function testSendNotification()
    {
        $notificacion = new SendNotMail();
        $autor = $this->em->getRepository('SeguridadBundle:Usuario')->findByUsername('testuser');
        $notificacion->setAutor($autor[0]);
        $notificacion->setTitulo('Titulo');
        $notificacion->setContenido('Contenido');
        $notificacion->setUsers($autor);
        $notificacion->setRoles(array());

        $this->assertEquals(1, $this->mailSvc->sendNotification($notificacion));

        $notificacion2 = new SendNotMail();
        $rol =  $this->em->getRepository('SeguridadBundle:Rol')->findByNombre('ROLE_TEST');
        $notificacion2->setAutor($autor[0]);
        $notificacion2->setTitulo('Titulo');
        $notificacion2->setContenido('Contenido');
        $notificacion2->setUsers(array());
        $notificacion2->setRoles($rol);
        $notificacion2->setRoles($rol);
        $this->assertEquals(1, $this->mailSvc->sendNotification($notificacion2));
        $result = $this->em->getRepository('NotificacionBundle:Notificacion')->findAll();
        $this->lastInsertedId = $result[count($result)-1];
        $this->lastInsertedId = $this->lastInsertedId->getId();
        $this->assertFalse(file_exists($this->urlAttach.DIRECTORY_SEPARATOR.$this->lastInsertedId.'.zip'));

        $notificacion3 = new SendNotMail();
        $notificacion3->setAutor($autor[0]);
        $notificacion3->setTitulo('Titulo');
        $notificacion3->setContenido('Contenido');
        $notificacion3->setUsers(array());
        $notificacion3->setRoles($rol);
        $upf = new UploadedFile(__DIR__.DIRECTORY_SEPARATOR.'file.txt','file.txt',null,null,null,true);
        $notificacion3->setAdjunto($upf);
        $this->assertEquals(1, $this->mailSvc->sendNotification($notificacion3));
        $result2 = $this->em->getRepository('NotificacionBundle:Notificacion')->findAll();
        $this->lastInsertedId2 = $result2[count($result2)-1];
        $this->lastInsertedId2 = $this->lastInsertedId2->getId();
        $this->assertTrue(file_exists($this->urlAttach.DIRECTORY_SEPARATOR.$this->lastInsertedId2.'.zip'));
    }

    public function testGetNombreAdjunto(){
        $result = $this->em->getRepository('NotificacionBundle:Notificacion')->findAll();
        $this->lastInsertedId = $result[count($result)-1];
        $this->lastInsertedId = $this->lastInsertedId->getId();
       
        $this->assertEquals('file.txt', $this->mailSvc->getNombreAdjunto($this->lastInsertedId));
    }
    public function testDeleteAdjunto(){
        $result = $this->em->getRepository('NotificacionBundle:Notificacion')->findAll();
        $this->lastInsertedId = $result[count($result)-1];
        $this->lastInsertedId = $this->lastInsertedId->getId();
       $this->mailSvc->deleteAdjunto($this->lastInsertedId);
       $this->assertFalse(file_exists($this->urlAttach.DIRECTORY_SEPARATOR.$this->lastInsertedId.'.zip')); 
    }


}
