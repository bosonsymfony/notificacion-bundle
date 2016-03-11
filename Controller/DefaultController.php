<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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

}
