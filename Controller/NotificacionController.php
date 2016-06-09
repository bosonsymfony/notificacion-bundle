<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Response;
use UCI\Boson\BackendBundle\Controller\BackendController;
use UCI\Boson\NotificacionBundle\Entity\Notificacion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Notificacion controller.
 *
 */
class NotificacionController extends BackendController
{

    public function indexAction(){
        $securityInf = $this->get("notificacion.notification")->getUserSecurityInfo();
        return $this->render("NotificacionBundle:Default:index.html.twig",array("securityInf"=>$securityInf));
    }

    /**
     * Obtiene el token para que los formularios de angular trabajen.
     * 
     * @Route("/notificacion_bundle/csrf_token", name="notificacion_csrf_form", options={"expose"=true})
     * @Method("POST")
     */
    public function getCsrfTokenAction(Request $request){
        $tokenId = $request->request->get('id_form');
        $csrf = $this->get('security.csrf.token_manager');
        $token = $csrf->getToken( 'notificacionbundle_'.$tokenId);
        return new Response($token);
    }



    /**
     * @return Response
     */
    public function securityTokenAction()
    {
        $securityInf = $this->get("notificacion.notification")->getUserSecurityInfo();
        return new Response(json_encode($securityInf));
    }

    /**
     * @param Request $request
     * @return Response
     */
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

    /**
     * @param Request $request
     * @return Response
     */
    public function getUsersAction(Request $request)
    {
        $filter = $request->get('filter', null);
        $limit = $request->get('limit', 5);
        $page = $request->get('page', 1);


        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('usuario.username, usuario.email,usuario.id')->from('SeguridadBundle:Usuario', 'usuario');
        if ($page > 1) {
            $qb->setFirstResult($page * $limit);
        }
        if ($filter) {
            $qb->where("usuario.username LIKE '%$filter%'");
        }
        $qb->setMaxResults($limit);

        $users = $qb->getQuery()->getArrayResult();
        return new Response(json_encode($users), 200, array(
            'content-type' => 'application/json'
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getRolesAction(Request $request)
    {
        $filter = $request->get('filter', null);
        $limit = $request->get('limit', 5);
        $page = $request->get('page', 1);


        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('rol.nombre,rol.id')->from('SeguridadBundle:Rol', 'rol');
        if ($page > 1) {
            $qb->setFirstResult($page * $limit);
        }
        if ($filter) {
            $qb->where("rol.nombre LIKE '%$filter%'");
        }
        $qb->setMaxResults($limit);

        $roles = $qb->getQuery()->getArrayResult();
        return new Response(json_encode($roles), 200, array(
            'content-type' => 'application/json'
        ));
    }

}
