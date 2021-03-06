<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Tests\Model;
use Symfony\Component\Translation\MessageCatalogue;
use UCI\Boson\BackendBundle\Controller\BackendController;
use UCI\Boson\NotificacionBundle\Entity\TiempoReal;
use UCI\Boson\NotificacionBundle\Entity\TipoNotificacion;
use UCI\Boson\NotificacionBundle\Form\Model\SendNotTiempoReal;
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
            'fecha',
            'titulo',
            'contenido',
        ),
        'associations' => array(
            'tipo' => array(
            'fields' => array(
                'id',
                'nombre'),
            'associations' => array()
        ),
        )
    );

    /**
     * @var array
     */
    private $searchFields = array(
        'fecha' => 'date',
        'titulo' => 'string',
        'contenido' => 'string',
        'id' => 'integer',
    );

    /**
     * @var array
     */
    private $defaultMaxResults = array(5, 10, 15);

//    /**
//     * Lists all TiempoReal entities.
//     *
//     * @Route("/notificacion_validators_form", name="notificacion_validators_form", options={"expose"=true})
//     * @Method("GET")
//     */
//    public function getValidatorFormAction(){
//        $currentCatalogue = new MessageCatalogue($this->getParameter('locale'));
//        $a = '/home/daniel/Documentos/source/pruebasInternas/bosonwithsandbox/src/UCI/Boson/NotificacionBundle/Resources/translations';
//
//        $this->get('translation.extractor')->extract($a,$currentCatalogue);
//        dump($currentCatalogue);die;
//    }

    /**
     * Lists all TiempoReal entities.
     *
     * @Route("/notificacion/", name="notificacion", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $filter = $request->get('filter', "");
        $limit = $request->get('limit', 5);
        $page = $request->get('page', 1);
        $order = $request->get('order', "id");
        return new Response($this->serialize($this->PaginateResults($filter, $page, $limit, $order)), 200, array(
            'content-type' => 'application/json'
        ));
    }

    /**
     * Creates a new TiempoReal entity.
     *
     * @Route("/notificacion/", name="notificacion_create", options={"expose"=true})
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
                return new Response($this->get('translator')->trans('notificacion.post401'), Response::HTTP_UNAUTHORIZED);
            }
            $autor = $this->getDoctrine()->getRepository('SeguridadBundle:Usuario')->find($secInfo['data']['userid']);
            $entity->setAutor($autor);
            /*llamo al registro de notificaciones en el repositorio*/
            $respService = $this->get('notificacion.tiemporeal')->sendNotification($entity);
            if ($respService === false) {
                return new Response(json_encode(array("data" => $this->get("translator")->trans("notificacion.notificacion_tr.create_fail_0_users_notif"), "type" => "warning")), Response::HTTP_BAD_REQUEST);

            } elseif (is_array($respService) && count($respService) === 0) {
                return new Response(json_encode(array("data" => $this->get("translator")->trans("notificacion.notificacion_tr.create_fail"), "type" => "warning")), Response::HTTP_BAD_REQUEST);
            } else {
                return new Response(json_encode(array("data" => $this->get("translator")->trans("notificacion.notificacion_tr.create_success"), "type" => "success")), Response::HTTP_CREATED);
            }
        }
        $errors = $this->getAllErrorsMessages($form);
        return new Response($this->serialize($errors), Response::HTTP_INTERNAL_SERVER_ERROR);
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
        $formNot = new TiempoRealType();

        $form = $this->createForm($formNot, $model, array(
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Finds and displays a TiempoReal entity.
     *
     * @Route("/notificacion/{id}", name="notificacion_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $qb = $em->createQueryBuilder()->select('notificacion, tiempoReales','partial autor.{username,id,email}','partial users.{id,username,email}')
                ->from('NotificacionBundle:Notificacion','notificacion')
                ->join('notificacion.autor','autor')
                ->join('notificacion.tiempoReales','tiempoReales')
                ->join('tiempoReales.user','users')
                ->where('notificacion.id = :id')
                ->setParameter('id',$id);
                $query = $qb->getQuery();
                $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
                $query->setHydrationMode(Query::HYDRATE_ARRAY);
                $entity = $query->getArrayResult();
        if (!$entity[0]) {
            return new Response($this->get("translator")->trans("notificacion.notificacion_tr.show_fail"), Response::HTTP_NOT_FOUND);
        }
        return new Response($this->serialize($entity[0]));
    }

//    /**
//     * Creates a form to edit a TiempoReal entity.
//     *
//     * @param TiempoReal $entity The entity
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createEditForm(TiempoReal $entity)
//    {
//        $form = $this->get('form.factory')->createNamedBuilder('notificacionbundle_notificacion', 'form', $entity, array(//'csrf_protection' => false
//        ));
//        foreach ($this->editFormFields as $index => $editFormField) {
//            $form->add($editFormField);
//        }
//
//        $form->setMethod('PUT');
//
//        return $form->getForm();
//    }
//
//    /**
//     * Edits an existing TiempoReal entity.
//     *
//     * @Route("/notificacion/{id}", name="notificacion_update", options={"expose"=true})
//     * @Method("PUT")
//     */
//    public function updateAction(Request $request, $id)
//    {
//        $em = $this->get('doctrine.orm.entity_manager');
//
//        $entity = $em->getRepository('NotificacionBundle:TiempoReal')->find($id);
//
//        if (!$entity) {
//            return new Response('Unable to find TiempoReal entity.', Response::HTTP_NOT_FOUND);
//        }
//
//        $editForm = $this->createEditForm($entity);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isValid()) {
//            $em->flush();
//
//            return new Response('The Usuario was updated successfully.');
//        }
//
//        $errors = $this->getAllErrorsMessages($editForm);
//        return new Response($this->serialize($errors), Response::HTTP_INTERNAL_SERVER_ERROR);
//    }

    /**
     * Deletes a TiempoReal entity.
     *
     * @Route("/notificacion/{id}", name="notificacion_delete", options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository('NotificacionBundle:Notificacion', 'notificacion')->find($id);
        if (!$entity) {
            return new Response($this->get("translator")->trans("notificacion.notificacion_tr.show_fail"), Response::HTTP_NOT_FOUND);
        }
        try {
            $em->remove($entity);
            $em->flush();
        } catch (\Exception $ex) {
            return new Response(json_encode(array('data' => sprintf($this->get('translator')->trans('notificacion.notificacion_tr.delete_fail'), $id), 'type' => 'error')));
        }
        return new Response(json_encode(array('data' => sprintf($this->get('translator')->trans('notificacion.notificacion_tr.delete_success'), $id), 'type' => 'success')));
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
        $selectFields = "partial Notificacion.{" . implode(', ', $this->listFields['fields']) . "}";
        $selectAssociations = $this->generateSelect($this->listFields['associations'], 'Notificacion');
        $qb = $em->createQueryBuilder();

        list($limit, $order, $direction) = $this->transformQuery($limit, $order);


         $qb->select($selectFields)
            ->from('NotificacionBundle:Notificacion', 'Notificacion')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        $qb->addSelect('tipo');
//
        $qb->innerJoin('Notificacion.tipo','tipo');

        $qb->where("CAST(Notificacion.fecha AS TEXT) LIKE '%$filter%' OR LOWER(Notificacion.titulo) LIKE '%$filter%' OR LOWER(Notificacion.contenido) LIKE '%$filter%'" );
        $qb->andWhere("tipo.nombre = :noTiempoReal");
        $qb->setParameter('noTiempoReal', TipoNotificacion::TiempoReal);

//
//        $qb->groupBy('notificacion.id');
        $query = $qb->getQuery();
//        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
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
