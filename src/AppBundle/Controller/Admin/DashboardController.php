<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("dashboard", name="admin_dashboard")
     */
    public function indexAction()
    {
        return $this->render('@App/admin/dashboard/dashboard.html.twig');
    }
}