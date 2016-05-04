<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use GuzzleHttp\Post\PostBody;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
class DefaultController extends Controller
{
    public function indexAction($name = "daniel")
    {


        $securityInf = $this->container->get("notificacion.notification")->getUserSecurityInfo();
        $client = new Client();
        $body = array_merge(array('security_data'=>$securityInf['data']),
                            array('notif_data'=> array('user'=>$name,'mensaje'=>'Hola este es el mensaje')));

        $resp = $client->post('http://10.58.10.152:3000/notification/'.$name,
            ['json'=>$body,
            'headers'=>
            [
                'authorization'=>$securityInf['token'],
                'content-type'=>'application/json'
            ]]
            );
        //$request->setBody(json_encode($body));
        //$resp = $client->send($request);
        $output = json_decode($resp->getBody()->getContents(),true);
        return $this->render('NotificacionBundle:Default:index.html.twig', array('securityInf' => $securityInf,'output'=>$output));
    }
    public function securityTokenAction()
    {
        $securityInf = $this->container->get("notificacion.notification")->getUserSecurityInfo();
        return new Response(json_encode($securityInf));
    }

}
