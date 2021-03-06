<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template; //Debe quitarse
use Symfony\Component\HttpFoundation\Response;
use UCI\Boson\BackendBundle\Controller\BackendController;
use UCI\Boson\NotificacionBundle\Entity\Correo;
use UCI\Boson\NotificacionBundle\Form\CorreoEntityType;
use UCI\Boson\NotificacionBundle\Form\CorreoServiceType;
use UCI\Boson\NotificacionBundle\Form\CorreoType;
use UCI\Boson\NotificacionBundle\Form\Model\SendNotMail;
use UCI\Boson\NotificacionBundle\Form\Model\SendNotTiempoReal;
use UCI\Boson\TrazasBundle\EventListener\AccionListener;

/**
 * CorreoServiceController. Controlador de los servicios REST para notificar.
 *
 */
class CorreoServiceController extends BackendController
{
    /**
     * @var array
     */
    private $listFields = array(
        'fields' => array(
            'id',
            'fecha',
            'titulo',
            'contenido',
            'adjunto'
        ),
        'associations' => array(
            'tipo' => array(
                'fields' => array(
                    'id',
                    'nombre'),
                'associations' => array()
            ),
            'autor' => array(
                'fields' => array(
                    'username',
                    'email',
                    'roles',
                    'id'),
                'associations' => array()
            ),
            'user' => array(
                'fields' => array(
                    'username',
                    'email',
                    'roles',
                    'id'),
                'associations' => array()
            )
        )
    );

    /**
     * @var array
     */
    private $searchFields = array(
        'fecha' => 'datetime',
        'titulo' => 'string',
        'contenido' => 'text',
        'adjunto' => 'boolean'
    );

    /**
     * @var array
     */
    private $defaultMaxResults = array(5, 10, 15);

    /**
     * Lists all Correo entities.
     *
     * @Route("/notificacion_services/notificacionmail/", name="notificacionmail_services", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $filter = $request->get('filter', "");
        $limit = $request->get('limit', 5);
        $page = $request->get('page', 1);
        $order = $request->get('order', "id");
        return $this->getResponseFormated($request,$this->PaginateResults($filter, $page, $limit, $order), Response::HTTP_CREATED);
    }
    private function trans($key){
        return $this->get("translator")->trans($key);
    }

    /**
     * Creates a new Correo entity.
     *
     * @Route("/notificacion_services/notificacionmail/", name="notificacionmail_services_create", options={"expose"=true})
     * @Method("POST")
     */
    public function createAction(Request $request)
    {

        $entity = new SendNotMail();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $secInfo = $this->get('notificacion.notification')->getUserSecurityInfo();
            if (is_null($secInfo['data']['userid'])) {
                return new Response($this->get('translator')->trans('notificacion.post401'), Response::HTTP_UNAUTHORIZED);
            }
            $autor = $this->getDoctrine()->getRepository('SeguridadBundle:Usuario')->find($secInfo['data']['userid']);
            $entity->setAutor($autor);
            $resp = $this->get('notificacion.correo')->sendNotification($entity);
            if ($resp !== 0)
                return $this->getResponseFormated($request,$this->trans("notificacion.notificacion_tr.create_success"),Response::HTTP_CREATED);
            return $this->getResponseFormated($request,$this->trans("notificacion.notificacion_tr.create_fail"),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $errors = $this->getAllErrorsMessages($form);
        return new Response($this->serialize($errors), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function getResponseFormated(Request $request, $data, $code = Response::HTTP_OK)
    {
        $format = 'json';
        $accepts = $request->headers->get('accept');

        if (strpos($accepts, "*/*") !== false || strpos($accepts, "application/json" !== false)) {
            $format = 'json';
            $header = array("charset" => "UTF-8", "content-type" => "application/json");
        } elseif (strpos($accepts, "application/xml") !== false) {
            $format = 'xml';
            $header = array("charset" => "UTF-8", "content-type" => "application/xml");
        }
        $objSerialiced = $this->get("serializer")->serialize($data, $format);
        $response = new Response($objSerialiced, $code, $header);
        return $response;
    }

    /**
     * Creates a form to create a Correo entity.
     *
     * @param Correo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SendNotMail $model)
    {
        $formMail = new CorreoServiceType();
        $form = $this->createForm($formMail, $model, array(
            'method' => 'POST',
            'csrf_protection'=>false
        ));
        return $form;
    }

    /**
     * Finds and displays a Correo entity.
     *
     * @Route("/notificacion_services/notificacionmail/{id}", name="notificacionmail_services_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();
        $qb->select('partial correo.{id,fecha,titulo,contenido,adjunto,autor},partial user.{username,id,email},partial autor.{username,id,email}')->from('NotificacionBundle:Correo', 'correo')->where('correo.id = :identifier')
            ->leftjoin('correo.user', 'user')
            ->leftjoin('correo.autor', 'autor')
            ->setParameter('identifier', $id);

        $query = $qb->getQuery();
        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->setHydrationMode(Query::HYDRATE_ARRAY);
        $entity = $query->getOneOrNullResult();
        if (!$entity) {
            return new Response('Unable to find Correo entity.', Response::HTTP_NOT_FOUND);
        }
        if ($entity['adjunto'] === true) {
            $entity['adjunto'] = $this->get('notificacion.correo')->getNombreAdjunto($id);
        }
        return new Response(json_encode($entity));
    }


    /**
     * Deletes a Correo entity.
     *
     * @Route("/notificacion_services/notificacionmail/{id}", name="notificacionmail_services_delete", options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request,$id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository('NotificacionBundle:Correo')->find($id);

        if (!$entity) {
            return new Response('Unable to find Correo entity.', Response::HTTP_NOT_FOUND);
        }
        if($entity->getAdjunto() === true)
            $this->get('notificacion.correo')->deleteAdjunto($entity->getId());
        try {
            $em->remove($entity);
            $em->flush();
        } catch (\Exception $ex) {
            return $this->getResponseFormated($request,sprintf($this->get('translator')->trans('notificacion.notificacion_tr.delete_fail'),$id),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return  $this->getResponseFormated($request,sprintf($this->get('translator')->trans('notificacion.notificacion_tr.delete_success'),$id),Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     *
     * @param string $filter
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     */
    public function PaginateResults($filter = "", $page = 1, $limit = 5, $order = "id")
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $selectFields = "partial Correo.{" . implode(', ', $this->listFields['fields']) . "}";
        $selectAssociations = $this->generateSelect($this->listFields['associations'], 'Correo');
        $qb = $em->createQueryBuilder();

        list($limit, $order, $direction) = $this->transformQuery($limit, $order);

        $qb ->select($selectFields)
            ->from('NotificacionBundle:Correo', 'Correo')
            //->orderBy('Correo.' . $order, $direction)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        foreach ($selectAssociations['select'] as $selectAssociation) {
            $qb->addSelect($selectAssociation);
        }

        foreach ($selectAssociations['join'] as $index => $selectAssociation) {
            $qb->leftJoin($selectAssociation, $index);
        }

        foreach ($this->searchFields as $index => $searchField) {
            $like = ($searchField !== 'string') ? "CAST(Correo.$index AS TEXT)" : "LOWER(Correo.$index)";
            $qb->orWhere("$like LIKE '%$filter%'");
        }

        $query = $qb->getQuery();
        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->setHydrationMode(Query::HYDRATE_ARRAY);

        $paginator = new Paginator($query);
        $count = $paginator->count();

        return array(
            'data' => $paginator->getIterator()->getArrayCopy(),
            'count' => $count
        );
    }

    /**
     * @param $limit
     * @param $order
     * @return array
     */
    public function transformQuery($limit, $order)
    {
        $limit = (in_array($limit, $this->defaultMaxResults)) ? $limit : $this->defaultMaxResults[0];
        if ($this->startsWith($order, '-')) {
            return array($limit, substr($order, 1), 'DESC');
        } else {
            return array($limit, $order, 'ASC');
        }
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * @param array $associations
     * @param $parent
     * @return array
     */
    private function generateSelect(array $associations, $parent)
    {
        $result = array(
            'select' => array(),
            'join' => array()
        );

        foreach ($associations as $index => $association) {
            $select = 'partial ' . $index . '.{' . implode(', ', $association['fields']) . '}';
            $result['select'][] = $select;
            $join = $parent . '.' . $index;
            $result['join'][$index] = $join;

            if (array_key_exists('associations', $association)) {
                $result = array_merge_recursive($result, $this->generateSelect($association['associations'], $index));
            }
        }
        return $result;
    }
}
