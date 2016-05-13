<?php

namespace UCI\Boson\NotificacionBundle\Controller;

use UCI\Boson\BackendBundle\Controller\BackendController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends BackendController
{
    /**
     * @Route(path="/notificacion/admin/scripts/config.notificacion.js", name="notificacion_app_config")
     */
    public function getAppAction()
    {
        return $this->jsResponse('NotificacionBundle:Scripts:config.js.twig');
    }

}
