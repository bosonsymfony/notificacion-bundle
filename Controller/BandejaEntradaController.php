<?php

namespace UCI\Boson\NotificacionBundle\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
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
 */
class BandejaEntradaController extends BackendController
{
    /**
     * @var Arreglo de variable utilizandas en la vista
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
                )
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
     * @var array de campos para la búsqueda
     */
    private $searchFields = array(
        'id' => 'integer'
    );

    /**
     * @var array de resultados.
     */
    private $defaultMaxResults = array(5, 10, 15);


    /**
     * Lists all BandejaEntrada entities.
     *
     * @Route("/bandejaentrada/", name="bandejaentrada", options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if(!$securityContext->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $respuesta = json_encode(array('data'=>array(),'count'=>0,'error'=> $this->get('translator')->trans('message.bandejaentrada.errorAuth')));
        }
        else{
            $token =$this->get("security.token_storage")->getToken();
            $user = $token->getUser()->getId();
            $filter = $request->get('filter', "");
            $limit = $request->get('limit', 5);
            $page = $request->get('page', 1);
            $order = $request->get('order', "id");
            $respuesta = $this->serialize($this->PaginateResults($filter, $page, $limit, $order,$user));

        }
        return new Response($respuesta, 200, array(
            'content-type' => 'application/json'
        ));
    }

    /**
     * Finds and displays a BandejaEntrada entity.
     *
     * @Route("/bandejaentrada/{id}", name="bandejaentrada_show", options={"expose"=true})
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
     * @Route("/bandejaentrada/{id}", name="bandejaentrada_delete", options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository('NotificacionBundle:Notificacion','notificacion')->find($id);
        if (!$entity) {
            return new Response($this->get("translator")->trans("message.notificacion_tr.show_fail"), Response::HTTP_NOT_FOUND);
        }
        try {
            $entity->setDeletedAt(new \DateTime());
            $em->persist($entity);
            $em->flush($entity);

        } catch (\Exception $ex) {
            return new Response(json_encode(array('data' => sprintf($this->get('translator')->trans('message.notificacion_tr.delete_fail'),$id), 'type' => 'error')));
        }
        return new Response(json_encode(array('data' => sprintf($this->get('translator')->trans('message.notificacion_tr.delete_success'),$id), 'type' => 'success')));
    }

    /**
     * @param string $filter
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     */
    public function PaginateResults($filter = "", $page = 1, $limit = 5, $order = "id",$userId = null)
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
        $qb->andWhere('notificacion.deleted_at > :currentDate OR notificacion.deleted_at IS NULL');
        $qb->andWhere('TiempoReal.user = :identif');
        $qb->setParameter("currentDate",new \DateTime())
            ->setParameter("identif",$userId);
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
