<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use UCI\Boson\NotificacionBundle\Entity\Notificacion;
use UCI\Boson\NotificacionBundle\Entity\TiempoReal;
use UCI\Boson\NotificacionBundle\Form\NotificacionType;
use UCI\Boson\NotificacionBundle\Form\TiempoRealType;

class DefaultController extends Controller
{
    public function indexAction($name = "daniel")
    {

        $fomV = $this->createForm(new TiempoRealType(),null, array(
            'action' => $this->generateUrl('notificacion_add'),
            'method' => 'POST',
        ));
        return $this->render('NotificacionBundle:Default:index.html.twig', array('form' => $fomV->createView()));

        $securityInf = $this->container->get("notificacion.notification")->getUserSecurityInfo();
        $client = new Client();
        $body = array_merge(array('security_data' => $securityInf['data']),
            array('notif_data' => array('user' => $name, 'mensaje' => 'Hola este es el mensaje 2')));

        $resp = $client->post('http://10.58.10.152:3000/notification/' . $name,
            ['json' => $body,
                'headers' =>
                    [
                        'authorization' => $securityInf['token'],
                        'content-type' => 'application/json'
                    ]]
        );
        //$request->setBody(json_encode($body));
        //$resp = $client->send($request);
        $output = json_decode($resp->getBody()->getContents(), true);
        return new Response("dadas");
        return $this->render('NotificacionBundle:Default:index.html.twig', array('securityInf' => $securityInf, 'output' => $output));
    }

    public function securityTokenAction()
    {
        $securityInf = $this->get("notificacion.notification")->getUserSecurityInfo();
        return new Response(json_encode($securityInf));
    }

    public function getNotifListAdminAction(Request $request)
    {
        if ($request->query->get('limit')) {
            $limit = $request->query->get('limit');
        } else {
            $limit = 30;
        }
        if ($request->query->get('page')) {
            $start = $request->query->get('page') * $limit;

        } else {
            $start = 0;
        }
        $manager = $this->get('doctrine.orm.entity_manager');
        $qb = $manager->createQueryBuilder();

        $secInfo = $this->get('notificacion.notification')->getUserSecurityInfo();

        $qb->select('notificacion')->from('NotificacionBundle:Notificacion', 'notificacion')
            ->where('notificacion.autor = ' . $secInfo['data']['userid']);
        $qb->setMaxResults($limit);
        $qb->setFirstResult($start);
        $query = $qb->getQuery();
        $arrayNotif = $query->getArrayResult();
        return new Response(json_encode($arrayNotif), 200);
    }

    public function getNotifByIdAction($idNotif)
    {

        $manager = $this->get('doctrine.orm.entity_manager');
        $qb = $manager->createQueryBuilder();

        $secInfo = $this->get('notificacion.notification')->getUserSecurityInfo();

        $qb->select('notificacion')->from('NotificacionBundle:Notificacion', 'notificacion')
            ->where('notificacion.id = ' . $idNotif);
        $query = $qb->getQuery();
        try{
            $notif = $query->getSingleScalarResult();
        }
        catch(NoResultException $ex){
            return new Response(json_encode($this->get('translator')->trans('message.get404')), 404);
        }

        if($notif['autor'] === $secInfo['data']['userid'])
        {
            return new Response(json_encode(sprintf($this->get('translator')->trans('message.get403'),$idNotif), 403));
        }
        return new Response(json_encode($notif), 200);
    }

    public function addNotifAction(Request $request)
    {
        $entity = new TiempoReal();
        $parameters = $request->request->all();
        $formNot = new TiempoRealType();
        $name = $formNot->getName();
        if(!array_key_exists($name,$parameters)){
            $parameters = $parameters[$name];
            $request->request->replace(array($name => $parameters));
        }
        $form = $this->createForm($formNot, $entity, array(
            'method' => 'POST',
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $secInfo = $this->get('notificacion.notification')->getUserSecurityInfo();
            $fecha = new \DateTime();
            $entity->setFecha($fecha);
            $entity->setEstado(false);
            $autor = $this->getDoctrine()->getRepository('SeguridadBundle:Usuario')->find($secInfo['data']['userid']);
            $entity->setAutor($autor);

            $resp = $this->getDoctrine()->getRepository('NotificacionBundle:TiempoReal')->createNotification($entity);
            if($resp === true){
                /**
                 * llamada al servicio de notificación
                 */
                $this->get('notificacion.tiemporeal')
                    ->notifyByUser(
                        $parameters['titulo'],
                        $parameters['contenido'],
                        $entity->getUser()->getEmail()
                    );
            }

            return new Response('The Content was created successfully.');
        }
        else{
            return new Response($form->getErrorsAsString(4));
        }
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
