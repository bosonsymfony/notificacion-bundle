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
 * BandejaEntrada controller.
 *
 * @Route("/bandejaentrada")
 */
class BandejaEntradaController extends BackendController
{
    /**
     * @var array Arreglo de campos para la vista
     */
    private $listFields = array(
        'fields' => array(
            'id',
            'fecha',
            'titulo',
            'contenido',
            'deleted_at'
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
                    'email',
                    'id'
                ),
                'associations' => array()
            ),
        )

    );

    /**
     * @var array de campos para la búsqueda
     */
    private $searchFields = array(
        'fecha' => 'datetime',
        'titulo' => 'string',
        'contenido' => 'text',
    );

    /**
     * @var array de resultados.
     */
    private $defaultMaxResults = array(5, 10, 15);


    /**
     * Lists all BandejaEntrada entities.
     *
     * @Route("/", name="bandejaentrada", options={"expose"=true})
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
     * Finds and displays a BandejaEntrada entity.
     *
     * @Route("/{id}", name="bandejaentrada_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $entity = $em->getRepository('NotificacionBundle:Notificacion')->find($id);

        if (!$entity) {
            return new Response('Unable to find Notificacion  entity.', Response::HTTP_NOT_FOUND);
        }

        return new Response($this->serialize($entity));
    }

    /**
     * Deletes a Notificacion entity.
     *
     * @Route("/{id}", name="bandejaentrada_delete", options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository('NotificacionBundle:Notificacion')->find($id);

        if (!$entity) {
            return new Response('Unable to find Notificacion entity.', Response::HTTP_NOT_FOUND);
        }
        $entity->setDeletedAt(new \DateTime());
        $em->persist($entity);
        $em->flush($entity);

        return new Response("The Notificacion with id '$id' was deleted successfully.");
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
        $currentDate = new \DateTime();
        $qb ->select($selectFields)
            ->from('NotificacionBundle:Notificacion', 'Notificacion')
            ->orderBy('Notificacion.' . $order, $direction)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->setParameter('currentDate', $currentDate);
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
        $qb->andWhere('Notificacion.deleted_at > :currentDate OR Notificacion.deleted_at IS NULL');

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
