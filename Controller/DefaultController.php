<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use UCI\Boson\NotificacionBundle\Entity\Notificacion;
use UCI\Boson\NotificacionBundle\Form\NotificacionType;

class DefaultController extends Controller
{
    public function indexAction($name = "daniel")
    {
        $securityInf = $this->container->get("notificacion.notification")->getUserSecurityInfo();

        return $this->render('NotificacionBundle:Default:index.html.twig', array('securityInf' => $securityInf));
    }

    public function securityTokenAction()
    {
        $securityInf = $this->container->get("notificacion.notification")->getUserSecurityInfo();
        return new Response(json_encode($securityInf));
    }

    public function getNotifListAdminAction()
    {
        die('getNotifListAdminAction');
    }

    public function getNotifByIdAction($idNotif)
    {
        die('getNotifByIdAction(' . $idNotif . ')');
    }

    public function addNotifAction(Request $request)
    {
        die("addNotifAction");
    }

    public function modNotifAction(Request $request, $idNotif)
    {
        $entity = new Notificacion();
        $form = $this->createForm(new NotificacionType(), $entity, array(
            'method' => 'PUT'
        ));
        die("Line:" . __LINE__);
        $form->handleRequest($request);
        if ($form->isValid())
            $this->get('notificacion.notification')->modNotif($form->getData());
        die("modNotifAction(" . $idNotif . ")");
    }

    public function delNotifAction($idNotif)
    {
        die("delNotifAction(" . $idNotif . ")");
    }

}
