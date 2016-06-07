<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use UCI\Boson\BackendBundle\Controller\BackendController;
use UCI\Boson\NotificacionBundle\Entity\TiempoReal;
use UCI\Boson\NotificacionBundle\Form\Model\SendNotTiempoReal;
use UCI\Boson\NotificacionBundle\Form\TiempoRealServiceType;
use UCI\Boson\NotificacionBundle\Form\TiempoRealType;

/**
 * TiempoReal controller. Clase controladora que se encarga de
 *
 */
class TiempoRealController extends BackendController
{
    /**
     * @var array
     */
    private $listFields = array(
        'fields' => array(
            'id',
            'estado',
        ),
        'associations' => array(
            'notificacion' => array(
                'fields' => array(
                    'id',
                    'fecha',
                    'titulo',
                    'contenido',
                ),
                'associations' => array(
                    'autor' => array(
                        'fields' => array(
                            'username',
                            'email',
                            'roles',
                            'id'
                        ),
                        'associations' => array()
                    ),
                ),
            ),
            'user' => array(
                'fields' => array(
                    'username',
                    'email',
                    'roles',
                    'id'
                ),
                'associations' => array()
            ),
        )
    );

    /**
     * @var array
     */
    private $searchFields = array(
        'estado' => 'boolean',
    );

    /**
     * @var array
     */
    private $defaultMaxResults = array(5, 10, 15);


    /**
     * Lists all TiempoReal entities.
     *
     * @Route("/api/notificacion_services/notificacion/", name="notificacion", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $filter = $request->get('filter', "");
        $limit = $request->get('limit', 5);
        $page = $request->get('page', 1);
        $order = $request->get('order', "id");
        return $this->getResponseFormated($request, $this->PaginateResults($filter, $page, $limit, $order), Response::HTTP_CREATED);
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

    private function trans($key)
    {
        return $this->trans($key);
    }

    /**
     * Creates a new TiempoReal entity.
     *
     * @Route("/api/notificacion_services/notificacion/", name="notificacion_create", options={"expose"=true})
     * @Method("POST")
     */
    public function createAction(Request $request)
    {

        $entity = new SendNotTiempoReal();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            /* obtengo el autor */
            $secInfo = $this->get('notificacion.notification')->getUserSecurityInfo();
            if (is_null($secInfo['data']['userid'])) {
                return $this->getResponseFormated($request, $this->get('translator')->trans('message.post401'), Response::HTTP_UNAUTHORIZED);
            }
            $autor = $this->getDoctrine()->getRepository('SeguridadBundle:Usuario')->find($secInfo['data']['userid']);
            $entity->setAutor($autor);
            /*llamo al registro de notificaciones en el repositorio*/
            $respService = $this->get('notificacion.tiemporeal')->sendNotification($entity);
            if ($respService === false) {
                return $this->getResponseFormated($this->trans("message.notificacion_tr.create_fail_0_users_notif"), Response::HTTP_BAD_REQUEST);
            } elseif (is_array($respService) && count($respService) === 0) {
                return $this->getResponseFormated($this->trans("message.notificacion_tr.create_fail"), Response::HTTP_BAD_REQUEST);
            } else {
                return $this->getResponseFormated($request, $this->trans("message.notificacion_tr.create_success"), Response::HTTP_CREATED);
            }
        }
        $errors = $this->getAllErrorsMessages($form);
        return $this->getResponseFormated($request, $errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Creates a form to create a TiempoReal entity.
     *
     * @param TiempoReal $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SendNotTiempoReal $model)
    {
        $formNot = new TiempoRealServiceType();

        $form = $this->createForm($formNot, $model, array(
            'method' => 'POST',
        ));
        return $form;
    }


    /**
     * Finds and displays a TiempoReal entity.
     *
     * @Route("/api/notificacion_services/notificacion/{id}", name="notificacion_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $entity = $em->getRepository('NotificacionBundle:TiempoReal')->find($id);

        if (!$entity) {
            return $this->getResponseFormated($request, $this->trans("message.notificacion_tr.unableTiFindEntity"), Response::HTTP_NOT_FOUND);
        }
        return $this->getResponseFormated($request, $entity, Response::HTTP_OK);
    }

    /**
     * Deletes a TiempoReal entity.
     *
     * @Route("/api/notificacion_services/notificacion/{id}", name="notificacion_delete", options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository('NotificacionBundle:Notificacion', 'notificacion')->find($id);
        if (!$entity) {
            return $this->getResponseFormated($request, array("data" => $this->trans("message.notificacion_tr.unableTiFindEntity")), Response::HTTP_NOT_FOUND);
        }
        try {
            $em->remove($entity);
            $em->flush();
        } catch (\Exception $ex) {
            return new Response(json_encode(array('data' => sprintf($this->get('translator')->trans('message.notificacion_tr.delete_fail'), $id), 'type' => 'error')));
        }
        return $this->getResponseFormated($request, array('data' => sprintf($this->get('translator')->trans('message.notificacion_tr.delete_success'), $id), 'type' => 'success'), Response::HTTP_OK);
    }


    /**
     * @param string $filter
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     */
    public function PaginateResults($filter = "", $page = 1, $limit = 5, $order = "id")
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $selectFields = "partial TiempoReal.{" . implode(', ', $this->listFields['fields']) . "}";
        $selectAssociations = $this->generateSelect($this->listFields['associations'], 'TiempoReal');
        $qb = $em->createQueryBuilder();

        list($limit, $order, $direction) = $this->transformQuery($limit, $order);

        $qb
            ->select($selectFields)
            ->from('NotificacionBundle:TiempoReal', 'TiempoReal')
            //->orderBy('TiempoReal.' . $order, $direction)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        foreach ($selectAssociations['select'] as $selectAssociation) {
            $qb->addSelect($selectAssociation);
        }

        foreach ($selectAssociations['join'] as $index => $selectAssociation) {
            $qb->leftJoin($selectAssociation, $index);
        }

        foreach ($this->searchFields as $index => $searchField) {
            $like = ($searchField !== 'string') ? "CAST(TiempoReal.$index AS TEXT)" : "LOWER(TiempoReal.$index)";
            $qb->orWhere("$like LIKE '%$filter%'");
        }
        $qb->orWhere("CAST(notificacion.fecha AS TEXT) LIKE '%$filter%'");
        $qb->orWhere("LOWER(notificacion.titulo) LIKE '%$filter%'");
        $qb->orWhere("LOWER(notificacion.contenido) LIKE '%$filter%'");

        $query = $qb->getQuery();
        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->setHydrationMode(Query::HYDRATE_ARRAY);
        //dump($query->getDQL());die;

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
    private function startsWith($haystack, $needle)
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
