<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template; //Debe quitarse
use Symfony\Component\HttpFoundation\Response;
use UCI\Boson\BackendBundle\Controller\BackendController;
use UCI\Boson\NotificacionBundle\Entity\Notificacion;

/**
 * Notificacion controller.
 *
 * @Route("/notificacion")
 */
class NotificacionController extends BackendController
{
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
                    'nombre',
                ),
                'associations' => array()
            ),
            'autor' => array(
                'fields' => array(
                    'username',
                    'usernameCanonical',
                    'email',
                    'emailCanonical',
                    'enabled',
                    'salt',
                    'password',
                    'lastLogin',
                    'locked',
                    'expired',
                    'expiresAt',
                    'confirmationToken',
                    'passwordRequestedAt',
                    'roles',
                    'credentialsExpired',
                    'credentialsExpireAt',
                    'id',
                    'dominio',
                ),
                'associations' => array()
            ),
        )

    );

    private $searchFields = array(
        'fecha' => 'datetime',
        'titulo' => 'string',
        'contenido' => 'text',
    );

    private $newFormFields = array(
        'fecha',
        'titulo',
        'contenido',
    );

    private $editFormFields = array(
        'fecha',
        'titulo',
        'contenido',
    );

    private $defaultMaxResults = array(5, 10, 15);


    /**
     * Lists all Notificacion entities.
     *
     * @Route("/", name="notificacion", options={"expose"=true})
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
     * Creates a new Notificacion entity.
     *
     * @Route("/", name="notificacion_create", options={"expose"=true})
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = new Notificacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($entity);
            $em->flush();

            return new Response('The Notificacion was created successfully.');
        }

        $errors = $this->getAllErrorsMessages($form);
        return new Response($this->serialize($errors), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Creates a form to create a Notificacion entity.
     *
     * @param Notificacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Notificacion $entity)
    {
        $form = $this->get('form.factory')->createNamedBuilder('notificacionbundle_notificacion', 'form', $entity, array('csrf_protection'   => false));

        foreach ($this->newFormFields as $index => $newFormField) {
            $form->add($newFormField);
        }
        $form->setAction($this->generateUrl('notificacion_create'));

        return $form->getForm();
    }

    /**
     * Displays a form to create a new Notificacion entity.
     *
     * @Route("/new", name="notificacion_new", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Notificacion();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Notificacion entity.
     *
     * @Route("/{id}", name="notificacion_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $entity = $em->getRepository('NotificacionBundle:Notificacion')->find($id);

        if (!$entity) {
            return new Response('Unable to find Notificacion entity.', Response::HTTP_NOT_FOUND);
        }

        return new Response($this->serialize($entity));
    }
                                                                                                                                                                                                                      /**
    * Creates a form to edit a Notificacion entity.
    *
    * @param Notificacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Notificacion $entity)
    {
        $form = $this->get('form.factory')->createNamedBuilder('notificacionbundle_notificacion', 'form', $entity, array(
            'csrf_protection' => false
        ));
        foreach ($this->editFormFields as $index => $editFormField) {
            $form->add($editFormField);
        }

        $form->setMethod('PUT');

        return $form->getForm();
    }

    /**
     * Edits an existing Notificacion entity.
     *
     * @Route("/{id}", name="notificacion_update", options={"expose"=true})
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $entity = $em->getRepository('NotificacionBundle:Notificacion')->find($id);

        if (!$entity) {
            return new Response('Unable to find Notificacion entity.', Response::HTTP_NOT_FOUND);
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return new Response('The Usuario was updated successfully.');
        }

        $errors = $this->getAllErrorsMessages($editForm);
        return new Response($this->serialize($errors), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Deletes a Notificacion entity.
     *
     * @Route("/{id}", name="notificacion_delete", options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository('NotificacionBundle:Notificacion')->find($id);

        if (!$entity) {
            return new Response('Unable to find Notificacion entity.', Response::HTTP_NOT_FOUND);
        }

        $em->remove($entity);
        $em->flush();

        return new Response("The Notificacion with id '$id' was deleted successfully.");
    }

    public function PaginateResults($filter = "", $page = 1, $limit = 5, $order = "id")
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $selectFields = "partial Notificacion.{" . implode(', ', $this->listFields['fields']) . "}";
        $selectAssociations = $this->generateSelect($this->listFields['associations'], 'Notificacion');
        $qb = $em->createQueryBuilder();

        list($limit, $order, $direction) = $this->transformQuery($limit, $order);

        $qb
            ->select($selectFields)
            ->from('NotificacionBundle:Notificacion', 'Notificacion')
            ->orderBy('Notificacion.' . $order, $direction)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        foreach ($selectAssociations['select'] as $selectAssociation) {
            $qb->addSelect($selectAssociation);
        }

        foreach ($selectAssociations['join'] as $index => $selectAssociation) {
            $qb->leftJoin($selectAssociation, $index);
        }

        foreach ($this->searchFields as $index => $searchField) {
            $like = ($searchField !== 'string') ? "CAST(Notificacion.$index AS TEXT)" : "LOWER(Notificacion.$index)";
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

    public function transformQuery($limit, $order)
    {
        $limit = (in_array($limit, $this->defaultMaxResults)) ? $limit : $this->defaultMaxResults[0];
        if ($this->startsWith($order, '-')) {
            return array($limit, substr($order, 1), 'DESC');
        } else {
            return array($limit, $order, 'ASC');
        }
    }

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
