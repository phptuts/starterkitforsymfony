<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("dashboard", name="admin_dashboard")
     */
    public function indexAction()
    {
        return $this->render('@Admin/dashboard/dashboard.html.twig');
    }
}